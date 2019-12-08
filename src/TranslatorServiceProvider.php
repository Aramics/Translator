<?php
    namespace Aramics\Translator;
    use Illuminate\Support\ServiceProvider;
    class TranslatorServiceProvider extends ServiceProvider {
        public function boot()
        {
            $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        }
        public function register()
        {
        }
    }
    ?>