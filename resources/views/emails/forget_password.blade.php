@component('mail::message')
Hello {{$user['first_name']}} {{$user['last_name']}},

Your new password is:{{$user['password']}}
kindly login via the button below with the password and change password on successful login:

@component('mail::button', ['url' => route('login')])
Login Page
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent

