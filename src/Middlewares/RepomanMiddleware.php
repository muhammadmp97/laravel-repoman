<?php

namespace WebPajooh\LaravelRepoman\Middlewares;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Str;

class RepoManMiddleware
{
    public function handle($request, Closure $next)
    {
        $startDate = Carbon::createFromTimeString(
            config('repoman.start_date')
        );

        $endTime = $startDate->copy()->addRealDays(config('repoman.deadline_days'));

        if ($startDate->isFuture()) {
            return $next($request);
        }

        if ($endTime->isPast()) {
            exit;
        }

        $response = $next($request);
        $content = $response->getContent();

        $diffInDays = $endTime->diffInDays(now());

        if (strtolower(config('app.timezone')) == 'asia/tehran' || strtolower(config('app.locale')) == 'fa') {
            $text = base64_decode('2KfbjNmGINmI2KjYs9in24zYqiA=') . $diffInDays . base64_decode('INix2YjYsiDYr9uM2q/YsSDYutuM2LEg2YHYudin2YQg2K7ZiNin2YfYryDYtNiv');
        } else {
            $text = base64_decode('VGhpcyB3ZWJzaXRlIGJlY29tZXMgZG93biBpbiA=') . $diffInDays . Str::plural(' day', $diffInDays) . base64_decode('IGZyb20gbm93');
        }

        $bar = base64_decode('PGRpdiBzdHlsZT0idGV4dC1hbGlnbjpjZW50ZXI7cGFkZGluZzoxNXB4IDA7YmFja2dyb3VuZDojMmEyYTJhO2NvbG9yOiNmZmY7dXNlci1zZWxlY3Q6bm9uZTsiPg==') . $text . '</div>';

        $content = preg_replace("/(<body[^>]*>)/i", "$0 {$bar}", $content);

        $response->setContent($content);

        return $response;
    }
}
