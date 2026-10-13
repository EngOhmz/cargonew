<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Pacel\TempolaryPacel;

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
        if(!empty(auth()->user()->id)){
        TempolaryPacel::where('added_by',auth()->user()->id)->delete();
      }
        
        view()->composer('layout.master', function($view) {
            $theme = \Cookie::get('theme');
            if ($theme == 'darkmode') {
                $theme = 'dark';
            }

            $view->with('theme', $theme);
        });

        view()->composer('layout.master-auth', function($view) {
            $theme = \Cookie::get('theme');
            if ($theme == 'darkmode') {
                $theme = 'dark';
            }

            $view->with('theme', $theme);
        });
    }
}
