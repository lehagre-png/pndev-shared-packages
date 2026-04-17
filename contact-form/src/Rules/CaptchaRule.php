<?php

namespace PnDev\ContactForm\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use PnDev\ContactForm\Services\CaptchaService;

class CaptchaRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! app(CaptchaService::class)->validate((string) $value)) {
            $fail('La reponse de verification est incorrecte. Veuillez reessayer.');
        }
    }
}
