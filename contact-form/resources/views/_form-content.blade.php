@if(session('success'))
<div class="mb-6 rounded-lg bg-green-50 border border-green-200 p-4 text-sm text-green-700">
    <div class="flex items-center gap-2">
        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
</div>
@endif

<form method="POST" action="{{ route('contact.submit') }}" class="space-y-5">
    @csrf

    {{-- Honeypot anti-bot (invisible) --}}
    <div style="position: absolute; left: -9999px;" aria-hidden="true">
        <label for="website">Ne pas remplir</label>
        <input type="text" name="website" id="website" value="" tabindex="-1" autocomplete="off">
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="nom" class="block text-sm font-medium text-gray-700 mb-1">Nom complet <span class="text-red-500">*</span></label>
            <input type="text" name="nom" id="nom" value="{{ old('nom') }}" required
                   class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                   placeholder="Votre nom">
            @error('nom') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                   class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                   placeholder="votre@email.com">
            @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="telephone" class="block text-sm font-medium text-gray-700 mb-1">Telephone</label>
            <input type="tel" name="telephone" id="telephone" value="{{ old('telephone') }}"
                   class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                   placeholder="06 12 34 56 78">
        </div>
        <div>
            <label for="company" class="block text-sm font-medium text-gray-700 mb-1">Societe</label>
            <input type="text" name="company" id="company" value="{{ old('company') }}"
                   class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                   placeholder="Nom de votre societe">
        </div>
    </div>

    @if(config('contact-form.subject_field') === 'select')
    <div>
        <label for="sujet" class="block text-sm font-medium text-gray-700 mb-1">Sujet <span class="text-red-500">*</span></label>
        <select name="sujet" id="sujet" required
                class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="">-- Choisir --</option>
            @foreach(config('contact-form.subject_options', []) as $key => $label)
                <option value="{{ $key }}" {{ old('sujet', $planInterest ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('sujet') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>
    @elseif(config('contact-form.subject_field') === 'text')
    <div>
        <label for="sujet" class="block text-sm font-medium text-gray-700 mb-1">Sujet <span class="text-red-500">*</span></label>
        <input type="text" name="sujet" id="sujet" value="{{ old('sujet') }}" required
               class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
               placeholder="Objet de votre message">
        @error('sujet') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>
    @endif

    <div>
        <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message <span class="text-red-500">*</span></label>
        <textarea name="message" id="message" rows="5" required minlength="10"
                  class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  placeholder="Decrivez votre besoin...">{{ old('message') }}</textarea>
        @error('message') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- CAPTCHA --}}
    @if(config('contact-form.captcha.enabled', true))
    <div class="rounded-lg bg-blue-50 border border-blue-200 p-4">
        <label for="captcha" class="block text-sm font-medium text-blue-800 mb-2">
            <svg class="inline w-4 h-4 mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            Verification de securite
        </label>
        <p class="text-sm text-blue-700 mb-3 font-medium">{{ $captchaQuestion }}</p>
        <input type="text" name="captcha" id="captcha" value="{{ old('captcha') }}" required
               class="block w-full rounded-lg border border-blue-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white"
               placeholder="Votre reponse" autocomplete="off">
        @error('captcha') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>
    @endif

    <button type="submit"
            class="w-full rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
        Envoyer mon message
    </button>
</form>
