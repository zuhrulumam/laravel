<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/auto-deploy-test', function() => 'CI/CD Works! 🚀');
