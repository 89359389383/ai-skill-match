<?php

namespace App\Providers;

use App\Models\SkillOrder;
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
        // フリーランスログイン時、ヘッダー・ドロップダウン用データを共有
        $freelancerComposer = function ($view) {
            if (Auth::guard('freelancer')->check()) {
                $user = Auth::guard('freelancer')->user();
                $freelancer = $user->freelancer ?? null;

                $unreadApplicationCount = 0;
                $unreadScoutCount = 0;
                $userInitial = 'U';

                $salesOrderCount = 0;
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
                    $salesOrderCount = SkillOrder::query()
                        ->whereHas('skillListing', fn ($q) => $q->where('freelancer_id', $freelancer->id))
                        ->whereIn('transaction_status', ['in_progress', 'delivered'])
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
                    'salesOrderCount' => $salesOrderCount,
                    'userInitial' => $userInitial,
                ]);
            }
        };

        View::composer([
            'layouts.public',
            'freelancer.jobs.index',
            'freelancer.scouts.index',
            'freelancer.applications.index',
            'freelancer.applications.create',
            'freelancer.jobs.show',
            'freelancer.scouts.show',
            'freelancer.messages.show',
            'freelancer.profile.settings',
        ], $freelancerComposer);

        View::composer('layouts.public', function ($view) {
            if (Auth::guard('company')->check()) {
                $user = Auth::guard('company')->user();
                $company = $user->company ?? null;

                $unreadApplicationCount = 0;
                $unreadScoutCount = 0;
                $userInitial = '企';

                if ($company) {
                    $unreadApplicationCount = Thread::query()
                        ->where('company_id', $company->id)
                        ->whereNotNull('job_id')
                        ->where('is_unread_for_company', true)
                        ->count();
                    $unreadScoutCount = Thread::query()
                        ->where('company_id', $company->id)
                        ->whereNull('job_id')
                        ->where('is_unread_for_company', true)
                        ->count();
                    if (!empty($company->name)) {
                        $userInitial = mb_substr($company->name, 0, 1);
                    } elseif (!empty($user->email)) {
                        $userInitial = mb_substr($user->email, 0, 1);
                    }
                } elseif (!empty($user->email)) {
                    $userInitial = mb_substr($user->email, 0, 1);
                }

                $view->with([
                    'unreadApplicationCount' => $unreadApplicationCount,
                    'unreadScoutCount' => $unreadScoutCount,
                    'userInitial' => $userInitial,
                ]);
            }
        });
    }
}