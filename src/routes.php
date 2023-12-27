<?php

use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'supervisor', 'as' => 'supervisor.'], function () {
    Route::get('/', function () {
        $composerJsonContents = file_get_contents(__DIR__ . '/../composer.json');
        $composerData = json_decode($composerJsonContents, true);
        return view('tahseen9::welcome', ['composerData' => $composerData]);
    })->name('index');

    Route::get('/dashboard', function (){
        return view('tahseen9::dashboard');
    })->name('dashboard');
//    Route::post('/store', 'SupervisorController@store')->name('store');
//    Route::get('/{id}/edit', 'SupervisorController@edit')->name('edit');
//    Route::post('/{id}/update', 'SupervisorController@update')->name('update');
//    Route::delete('/{id}', 'SupervisorController@destroy')->name('destroy');
});
