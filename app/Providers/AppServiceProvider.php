<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;

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
        // ページネーションに Bootstrap を使用
        Paginator::useBootstrap();

        // 古いMySQLに対処（必要に応じて）
        Schema::defaultStringLength(191);

        // HTTPS を強制（本番環境のみ）
        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }
    }
}
