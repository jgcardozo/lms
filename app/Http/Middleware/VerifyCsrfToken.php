<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'user/register',
        'user/sync',
        'class-marker/webhook/result',
        'lesson/assessment-check',
        'user/cancel-access',
        'hooks/push-to-academy'
    ];
}
