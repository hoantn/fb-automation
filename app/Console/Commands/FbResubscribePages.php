<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Page;
use App\Services\FacebookService;

class FbResubscribePages extends Command
{
    protected $signature = 'fb:resubscribe';
    protected $description = 'Ensure all pages are subscribed to webhook events';

    public function handle(FacebookService $fb)
    {
        $count = Page::count();
        $bar = $this->output->createProgressBar($count);
        $bar->start();

        Page::chunk(50, function ($pages) use ($fb, $bar) {
            foreach ($pages as $p) {
                $fb->ensurePageSubscription($p);
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);
        $this->info('Done.');
        return 0;
    }
}
