@component('mail::message')
Dear **{{$user['name']}}**,  {{-- use double space for line break --}}

{{$user['body']}}


@component('mail::button', ['url' => $user['token']])
    Password Reset Link:
@endcomponent
Thank You.
@endcomponent