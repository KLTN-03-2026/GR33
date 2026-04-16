<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

\Dedoc\Scramble\Scramble::registerUiRoute('api/docs');
\Dedoc\Scramble\Scramble::registerJsonSpecificationRoute('api.json');
