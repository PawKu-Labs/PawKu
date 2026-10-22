<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi tambah/edit profil kucing. Aturan mengikuti form Flutter:
 * nama wajib, ras opsional (pilihan atau "Lainnya"), tanggal lahir tidak di masa depan,
 * gender jantan/betina, status steril, foto opsional.
 */
class CatRequest extends FormRequest
{
    public const BREED_OTHER = '__other__';

    public function authorize(): bool
    {
        return (bool) $this->session()->get('supabase.user.id');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'breed_choice' => ['nullable', 'string', 'max:100'],
            'breed_custom' => ['nullable', 'string', 'max:100'],
            'birth_date' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:2000-01-01',
                'before_or_equal:'.now('Asia/Jakarta')->toDateString(),
            ],
            'gender' => ['required', 'in:jantan,betina'],
            'is_neutered' => ['nullable', 'boolean'],
            'photo' => ['nullable', 'file', 'mimetypes:image/jpeg,image/png,image/webp', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama kucing wajib diisi.',
            'name.max' => 'Nama kucing maksimal 100 karakter.',
            'breed_choice.max' => 'Ras maksimal 100 karakter.',
            'breed_custom.max' => 'Ras maksimal 100 karakter.',
            'birth_date.date_format' => 'Format tanggal lahir tidak valid.',
            'birth_date.after_or_equal' => 'Tanggal lahir tidak boleh sebelum tahun 2000.',
            'birth_date.before_or_equal' => 'Tanggal lahir tidak boleh di masa depan.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'gender.in' => 'Jenis kelamin tidak valid.',
            'photo.mimetypes' => 'Foto harus berformat JPG, PNG, atau WEBP.',
            'photo.max' => 'Ukuran foto maksimal 5 MB.',
            'photo.uploaded' => 'Foto gagal diunggah. Pastikan ukurannya tidak lebih dari 5 MB.',
        ];
    }

    /** Ras final: dari pilihan dropdown, atau input teks bila memilih "Lainnya". */
    public function breed(): ?string
    {
        $choice = trim((string) $this->input('breed_choice', ''));

        if ($choice === self::BREED_OTHER) {
            $custom = trim((string) $this->input('breed_custom', ''));

            return $custom === '' ? null : $custom;
        }

        return $choice === '' ? null : $choice;
    }

    /**
     * Kolom yang boleh ditulis ke tabel cats (tanpa owner_id, photo_url, weight).
     * Kolom "weight" sengaja tidak disentuh agar nilai dari fitur lain tidak tertimpa.
     */
    public function catData(): array
    {
        $birth = trim((string) $this->input('birth_date', ''));

        return [
            'name' => trim((string) $this->input('name')),
            'breed' => $this->breed(),
            'birth_date' => $birth === '' ? null : $birth,
            'gender' => (string) $this->input('gender'),
            'is_neutered' => $this->boolean('is_neutered'),
        ];
    }
}