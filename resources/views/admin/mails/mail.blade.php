@component('mail::message')
Dear **{{$user['name']}}**,  {{-- use double space for line break --}}

Your request for password reset has been recieved. Please Click on the link below to reset your password.


@component('mail::button', ['url' => $user['link']])
Reset Password
@endcomponent
Thank You.
@endcomponent