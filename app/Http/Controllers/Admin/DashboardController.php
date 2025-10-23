<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Models\{Page, Customer, Message, Broadcast};

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function stats()
    {
        $cacheSeconds = (int)config('system.cache_duration', 10);

        return Cache::remember('admin.stats', $cacheSeconds, function () {
            $pages = Page::count();
            $customers = Customer::count();

            $today = now()->startOfDay();
            $messagesToday = Message::where('created_at', '>=', $today)->count();

            $latest = Message::orderByDesc('id')->limit(10)
                ->get(['id','page_id','customer_id','text','created_at'])
                ->map(function ($m) {
                    return [
                        'id' => $m->id,
                        'page_id' => $m->page_id,
                        'customer' => $m->customer?->name ?? $m->customer?->psid,
                        'last_message_at' => optional($m->created_at)->toDateTimeString(),
                    ];
                });

            $hourly = array_fill(0, 24, 0);
            $rows = Message::where('created_at', '>=', $today)
                ->selectRaw('strftime("%H", created_at) as h, count(*) as c')
                ->groupBy('h')->pluck('c','h')->toArray();
            foreach ($rows as $h => $c) $hourly[(int)$h] = (int)$c;

            $lastBroadcast = Broadcast::orderByDesc('id')->first();

            return response()->json([
                'pages'            => $pages,
                'customers'        => $customers,
                'messages_today'   => $messagesToday,
                'latest_convs'     => $latest,
                'hourly'           => $hourly,
                'last_broadcast'   => $lastBroadcast,
            ]);
        });
    }
}
