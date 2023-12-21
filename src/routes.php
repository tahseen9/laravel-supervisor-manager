<?php

use Illuminate\Support\Facades\Route;

Route::get('/laravel-supervisor-manager', function () {
    $composerJsonContents = file_get_contents(__DIR__ . '/../composer.json');
    $composerData = json_decode($composerJsonContents, true);
    return view('tahseen9::welcome', ['composerData' => $composerData]);
});
