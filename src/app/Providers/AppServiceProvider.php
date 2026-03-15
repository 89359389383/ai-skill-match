<?php

namespace App\Providers;

use App\Models\Thread;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 公開レイアウト（スキル販売・記事など）でフリーランスログイン時、ヘッダー用データを共有
        View::composer('layouts.public', function ($view) {
            if (!Auth::guard('freelancer')->check()) {
                return;
            }
            $user = Auth::guard('freelancer')->user();
            $freelancer = $user->freelancer ?? null;

            $unreadApplicationCount = 0;
            $unreadScoutCount = 0;
            $userInitial = 'U';

            if ($freelancer) {
                $unreadScoutCount = Thread::query()
                    ->where('freelancer_id', $freelancer->id)
                    ->whereNull('job_id')
                    ->where('is_unread_for_freelancer', true)
                    ->count();
                $unreadApplicationCount = Thread::query()
                    ->where('freelancer_id', $freelancer->id)
                    ->whereNotNull('job_id')
                    ->where('is_unread_for_freelancer', true)
                    ->count();
                if (!empty($freelancer->display_name)) {
                    $userInitial = mb_substr($freelancer->display_name, 0, 1);
                } elseif (!empty($user->email)) {
                    $userInitial = mb_substr($user->email, 0, 1);
                }
            } elseif (!empty($user->email)) {
                $userInitial = mb_substr($user->email, 0, 1);
            }

            $view->with([
                'freelancer' => $freelancer,
                'unreadApplicationCount' => $unreadApplicationCount,
                'unreadScoutCount' => $unreadScoutCount,
                'userInitial' => $userInitial,
            ]);
        });
    }
}