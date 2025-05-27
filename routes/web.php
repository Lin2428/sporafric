<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/impression-form-etat', function () {
    return view('impression.form-etat');
})->name('impression.etat');

Route::get("/admin/interventions/{id}")->name("admin.interventions");
