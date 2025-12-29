<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/auto-deploy-test', function() => return 'Version 2.0 - AUTO DEPLOY! 🚀');
