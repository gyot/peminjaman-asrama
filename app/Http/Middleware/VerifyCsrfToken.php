<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Routes yang dikecualikan dari proteksi CSRF.
     *
     * @var array
     */
    protected $except = [
        '/api/setApplications', // Tambahkan endpoint yang ingin dikecualikan
    ];
}