
@component('mail::message')
# Hello {{$user->first_name}},

Thanks for registering with us. Kindly confirm and verify your account through button link below:

@component('mail::button', ['url' => route('verify', $user->activation_code)])
Verify Account
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent

