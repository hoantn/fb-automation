<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Page;
use App\Services\FacebookService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Tự subscribe ngay khi lưu Page có token + page_id
        if (class_exists(Page::class)) {
            Page::saved(function (Page $page) {
                try {
                    $token = $page->access_token ?? $page->token ?? null;
                    $pageId = $page->meta_page_id ?? $page->page_id ?? $page->id ?? null;
                    if ($token && $pageId) {
                        app(FacebookService::class)->subscribePageEvents($page);
                    }
                } catch (\Throwable $e) {
                    \Log::warning('Auto-subscribe error: '.$e->getMessage());
                }
            });
        }
    }
}
