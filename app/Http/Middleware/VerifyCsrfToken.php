<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Các URI không kiểm tra CSRF.
     * LƯU Ý: không có dấu "/" ở đầu.
     */
    protected $except = [
		'webhook/*',
		'/webhook/*',
	];
}
