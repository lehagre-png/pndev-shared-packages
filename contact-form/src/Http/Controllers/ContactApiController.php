<?php

namespace PnDev\ContactForm\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use PnDev\ContactForm\Mail\ContactFormMail;
use PnDev\ContactForm\Services\CaptchaService;

class ContactApiController extends Controller
{
    /**
     * Retourne un challenge CAPTCHA + token chiffre.
     */
    public function captcha(CaptchaService $captcha): JsonResponse
    {
        $data = $captcha->generateForApi();

        return response()->json([
            'question'      => $data['question'],
            'captcha_token' => $data['captcha_token'],
        ]);
    }

    /**
     * Traite la soumission du formulaire via API.
     */
    public function submit(Request $request, CaptchaService $captcha): JsonResponse
    {
        $subjectOptions = array_keys(config('contact-form.subject_options', []));

        $rules = [
            'nom'           => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'max:255'],
            'telephone'     => ['nullable', 'string', 'max:20'],
            'company'       => ['nullable', 'string', 'max:255'],
            'message'       => ['required', 'string', 'min:10', 'max:2000'],
            'captcha'       => ['required', 'string'],
            'captcha_token' => ['required', 'string'],
        ];

        if (config('contact-form.subject_field') === 'select' && ! empty($subjectOptions)) {
            $rules['sujet'] = ['required', 'in:' . implode(',', $subjectOptions)];
        } elseif (config('contact-form.subject_field') === 'text') {
            $rules['sujet'] = ['required', 'string', 'max:255'];
        }

        // Honeypot
        $rules['website'] = ['nullable', 'max:0'];

        $validated = $request->validate($rules);

        // Rejet silencieux si honeypot rempli
        if (! empty($validated['website'] ?? '')) {
            return response()->json(['success' => true, 'message' => 'Message envoye.']);
        }

        // Valider le CAPTCHA via token
        if (! $captcha->validateToken($validated['captcha'], $validated['captcha_token'])) {
            return response()->json([
                'message' => 'La reponse de verification est incorrecte.',
                'errors'  => ['captcha' => ['La reponse de verification est incorrecte. Veuillez reessayer.']],
            ], 422);
        }

        unset($validated['captcha'], $validated['captcha_token'], $validated['website']);

        if (isset($validated['sujet'])) {
            $validated['sujet_label'] = config('contact-form.subject_options.' . $validated['sujet'], $validated['sujet']);
        }

        try {
            Mail::send(new ContactFormMail($validated));
        } catch (\Exception $e) {
            Log::error('Contact form API email failed', [
                'site'  => config('contact-form.site_name'),
                'email' => $validated['email'],
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Votre message a ete envoye avec succes. Nous vous repondrons dans les meilleurs delais.',
        ]);
    }
}
