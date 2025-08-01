{{-- resources/views/emails/template.blade.php --}}
@component('mail::message')
{!! $content !!}
@endcomponent