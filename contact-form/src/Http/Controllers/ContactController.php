<?php

namespace PnDev\ContactForm\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use PnDev\ContactForm\Mail\ContactFormMail;
use PnDev\ContactForm\Rules\CaptchaRule;
use PnDev\ContactForm\Services\CaptchaService;

class ContactController extends Controller
{
    public function show(Request $request, CaptchaService $captcha)
    {
        $captchaData = $captcha->generate();
        $planInterest = $request->query('plan', $request->query('sujet', ''));

        $view = config('contact-form.layout')
            ? 'contact-form::form-with-layout'
            : 'contact-form::form';

        return view($view, [
            'captchaQuestion' => $captchaData['question'],
            'planInterest'    => $planInterest,
        ]);
    }

    public function submit(Request $request, CaptchaService $captcha)
    {
        $subjectOptions = array_keys(config('contact-form.subject_options', []));

        $rules = [
            'nom'     => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'company' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
            'captcha' => ['required', 'string', new CaptchaRule],
        ];

        if (config('contact-form.subject_field') === 'select' && ! empty($subjectOptions)) {
            $rules['sujet'] = ['required', 'in:' . implode(',', $subjectOptions)];
        } elseif (config('contact-form.subject_field') === 'text') {
            $rules['sujet'] = ['required', 'string', 'max:255'];
        }

        // Honeypot : champ invisible, doit rester vide
        $rules['website'] = ['nullable', 'max:0'];

        $validated = $request->validate($rules);

        // Rejet silencieux si honeypot rempli (bot)
        if (! empty($validated['website'] ?? '')) {
            return back()->with('success', 'Votre message a ete envoye avec succes. Nous vous repondrons dans les meilleurs delais.');
        }

        unset($validated['captcha'], $validated['website']);

        // Ajouter le label du sujet
        if (isset($validated['sujet'])) {
            $validated['sujet_label'] = config('contact-form.subject_options.' . $validated['sujet'], $validated['sujet']);
        }

        try {
            Mail::send(new ContactFormMail($validated));
        } catch (\Exception $e) {
            Log::error('Contact form email failed', [
                'site'  => config('contact-form.site_name'),
                'email' => $validated['email'],
                'error' => $e->getMessage(),
            ]);
        }

        return back()->with('success', 'Votre message a ete envoye avec succes. Nous vous repondrons dans les meilleurs delais.');
    }
}
