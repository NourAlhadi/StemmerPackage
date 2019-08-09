<?php

namespace Nouralhadi\Stemmer;
use Illuminate\Support\ServiceProvider;

class StemmerServiceProvider extends ServiceProvider {

    public function boot(){
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/web/views', 'stemmer');
        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'stemmer');
    }

    public function register(){

    }
}
