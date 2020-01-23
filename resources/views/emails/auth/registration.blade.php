@component('mail::message')
# Verification code

Your verification code is:

@component('mail::promotion')
<h1 style="letter-spacing: 10px; font-size: 40px;">{{ $code }}</h1>
@endcomponent

If you did not create an account, no further action is required. <br>

Regards,<br>
{{ config('app.name') }}
@endcomponent
