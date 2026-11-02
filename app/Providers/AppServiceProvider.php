<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        \Illuminate\Support\Facades\View::composer('owner.*', function (View $view): void {
            $userId = session('supabase.user.id');
            $userMitras = collect();

            if ($userId) {
                try {
                    $userMitras = DB::table('mitras')
                        ->where('owner_id', $userId)
                        ->orderBy('created_at')
                        ->get();
                } catch (\Throwable $e) {
                    report($e);
                }
            }

            $view->with('layoutUserMitras', $userMitras);
            $view->with('layoutHasMitra', $userMitras->isNotEmpty());
            $view->with('layoutHasApprovedMitra', $userMitras->contains(fn($m) => $m->status === 'approved'));
        });
    }
}