<?php

use Illuminate\Support\Facades\Route;
use PnDev\ContactForm\Http\Controllers\ContactController;

$prefix = config('contact-form.routes.prefix', 'contact');
$middleware = config('contact-form.routes.middleware', ['web']);

Route::middleware($middleware)->prefix($prefix)->group(function () {
    Route::get('/', [ContactController::class, 'show'])->name('contact');
    Route::post('/', [ContactController::class, 'submit'])
        ->middleware('throttle:5,1')
        ->name('contact.submit');
});
