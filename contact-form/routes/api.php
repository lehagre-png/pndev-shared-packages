<?php

use Illuminate\Support\Facades\Route;
use PnDev\ContactForm\Http\Controllers\ContactApiController;

$prefix = config('contact-form.routes.api_prefix', 'api/contact');
$middleware = config('contact-form.routes.api_middleware', ['api', 'throttle:10,1']);

Route::middleware($middleware)->prefix($prefix)->group(function () {
    Route::get('/captcha', [ContactApiController::class, 'captcha'])->name('api.contact.captcha');
    Route::post('/', [ContactApiController::class, 'submit'])->name('api.contact.submit');
});
