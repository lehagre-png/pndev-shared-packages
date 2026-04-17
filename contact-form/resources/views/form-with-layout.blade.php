@extends(config('contact-form.layout'))
@section('title', config('contact-form.page_title', 'Contactez-nous'))
@if(View::hasSection('subtitle'))
    @section('subtitle', config('contact-form.page_subtitle', 'Une question ? Ecrivez-nous.'))
@endif
@section('meta_description', 'Contactez l\'equipe ' . config('contact-form.site_name') . '. Formulaire de contact securise. Reponse sous 24h.')
@section('meta_robots', 'index, follow')
@section(config('contact-form.layout_section', 'content'))
    @include('contact-form::_form-content')
@endsection
