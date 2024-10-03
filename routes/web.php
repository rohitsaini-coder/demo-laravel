<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {

    $roles = \App\Models\Role::get();
    return view('home',compact('roles'));

})->name('home');
