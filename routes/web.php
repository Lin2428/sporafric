<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin');

//Route::get('/admin')->name('admin');

Route::get('/impression-form-etat', function () {
    return view('impression.form-etat');
})->name('impression.etat');

Route::get("/admin/interventions/{id}")->name("admin.interventions");
