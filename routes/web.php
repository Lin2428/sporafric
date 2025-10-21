<?php

use App\Http\Controllers\OdooController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin');

//Route::get('/admin')->name('admin');

Route::get('/impression-form-etat', function () {
    return view('impression.form-etat');
})->name('impression.etat');

Route::get("/admin/interventions/{id}")->name("admin.interventions");
Route::get("/admin/intervention-devis/{id}")->name("admin.intervention.devis");

Route::get('/odoo', [OdooController::class, 'syncronizeDevis']);

Route::get('/download/{folder}/{filename}',function() {
    return App\Utils\FunctionUtils::download(request('filename'), request('folder'));
})->name('file.download');