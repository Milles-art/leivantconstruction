<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('leivant:about', function () {
    $this->info('Leivant Construction Solutions marketplace is ready.');
})->purpose('Show Leivant application status.');
