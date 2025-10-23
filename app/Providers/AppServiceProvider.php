<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Page;
use App\Services\FacebookService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (class_exists(Page::class)) {
            Page::saved(function (Page $page) {
                try {
                    if ($page->access_token && $page->meta_page_id) {
                        app(FacebookService::class)->ensurePageSubscription($page);
                    }
                } catch (\Throwable $e) {
                    \Log::warning('Auto-subscribe error: '.$e->getMessage());
                }
            });
        }
    }
}
