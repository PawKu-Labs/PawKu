<?php

namespace App\Http\Controllers;

use App\Support\ActivityLogger;
use App\Support\SupabaseStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminArticleController extends Controller
{
    private const DEFAULT_CATEGORIES = ['Kucing', 'Anjing', 'Nutrisi', 'Perawatan', 'Kesehatan'];
    private const FALLBACK_IMAGE = 'https://raw.githubusercontent.com/fashaputri/asset/main/pawku.png';

    public function index(): View
    {
        $rows = DB::table('articles')->orderByDesc('created_at')->get();

        $articles = $rows->map(fn($r) => [
            'id' => $r->id,
            'title' => $r->title,
            'description' => $r->description,
            'content' => $r->content,
            'category' => $r->category,
            'author' => $r->author ?? '-',
            'status' => $r->status,
            'views' => (int) $r->views,
            'date' => substr((string) ($r->published_at ?? $r->created_at), 0, 10),
            'image' => $r->image_url ?: self::FALLBACK_IMAGE,
            'has_image' => (bool) $r->image_url,
        ])->all();

        $categories = collect(self::DEFAULT_CATEGORIES)
            ->merge($rows->pluck('category'))
            ->unique()->values();

        return view('admin.articles', compact('articles', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $image = null;
        if ($request->hasFile('image')) {
            try {
                $image = SupabaseStorage::upload($request->file('image'));
            } catch (\Throwable $e) {
                return back()->withInput()->withErrors(['image' => 'Gagal mengunggah gambar. Coba lagi.']);
            }
        }

        $userId = session('supabase.user.id');
        $author = $userId
            ? DB::table('profiles')->where('id', $userId)->selectRaw('coalesce(full_name, username) as n')->value('n')
            : null;

        $id = (string) Str::uuid();

        DB::table('articles')->insert([
            'id' => $id,
            'title' => $data['title'],
            'slug' => $this->uniqueSlug($data['title']),
            'description' => $data['description'],
            'content' => $data['content'] ?? null,
            'category' => $data['category'],
            'author' => $author ?: 'Admin PawKu',
            'status' => $data['status'],
            'image_path' => $image['path'] ?? null,
            'image_url' => $image['url'] ?? null,
            'published_at' => $data['status'] === 'publish' ? now() : null,
            'created_by' => $userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        ActivityLogger::log(
            'data',
            'create',
            'Artikel "' . $data['title'] . '" dibuat',
            'articles',
            $id,
            null,
            ['title' => $data['title'], 'category' => $data['category'], 'status' => $data['status']],
        );

        return back()->with('success', 'Artikel berhasil ditambahkan.');
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $article = DB::table('articles')->where('id', $id)->first();
        abort_if(!$article, 404);

        $data = $this->validated($request);

        $payload = [
            'title' => $data['title'],
            'description' => $data['description'],
            'content' => $data['content'] ?? null,
            'category' => $data['category'],
            'status' => $data['status'],
            'updated_at' => now(),
        ];

        // Set published_at saat pertama kali terbit; kosongkan jika kembali ke draf.
        if ($data['status'] === 'publish') {
            $payload['published_at'] = $article->published_at ?? now();
        } else {
            $payload['published_at'] = null;
        }

        $oldImagePath = null;

        if ($request->hasFile('image')) {
            try {
                $image = SupabaseStorage::upload($request->file('image'));
            } catch (\Throwable $e) {
                return back()->withInput()->withErrors(['image' => 'Gagal mengunggah gambar. Coba lagi.']);
            }
            $payload['image_path'] = $image['path'];
            $payload['image_url'] = $image['url'];
            $oldImagePath = $article->image_path;
        } elseif ($request->boolean('remove_image')) {
            $payload['image_path'] = null;
            $payload['image_url'] = null;
            $oldImagePath = $article->image_path;
        }

        DB::table('articles')->where('id', $id)->update($payload);

        // Hapus gambar lama setelah DB berhasil diperbarui.
        SupabaseStorage::delete($oldImagePath);

        ActivityLogger::log(
            'data',
            'update',
            'Artikel "' . $data['title'] . '" diperbarui',
            'articles',
            $id,
            ['title' => $article->title, 'category' => $article->category, 'status' => $article->status],
            ['title' => $data['title'], 'category' => $data['category'], 'status' => $data['status']],
        );

        return back()->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $article = DB::table('articles')->where('id', $id)->first();
        abort_if(!$article, 404);

        DB::table('articles')->where('id', $id)->delete();
        SupabaseStorage::delete($article->image_path);

        ActivityLogger::log(
            'data',
            'delete',
            'Artikel "' . $article->title . '" dihapus',
            'articles',
            $id,
            ['title' => $article->title, 'category' => $article->category, 'status' => $article->status],
            null,
        );

        return back()->with('success', 'Artikel berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'category' => ['required', 'string', 'max:50'],
            'status' => ['required', Rule::in(['publish', 'draft'])],
            'description' => ['required', 'string', 'max:300'],
            'content' => ['nullable', 'string', 'max:50000'],
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'title.required' => 'Judul wajib diisi.',
            'description.required' => 'Deskripsi singkat wajib diisi.',
            'image.mimes' => 'Gambar harus berformat JPG, PNG, atau WEBP.',
            'image.max' => 'Ukuran gambar maksimal 2 MB.',
        ]);
    }

    private function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'artikel';
        $slug = $base;
        $i = 2;

        while (DB::table('articles')->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}