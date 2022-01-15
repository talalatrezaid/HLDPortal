@component('mail::message')
Dear **Admin**,  {{-- use double space for line break --}}

{{$user['body']}}

Brand Name: {{  $user['name']  }}<br>
Seller Email: {{  $user['email']  }}<br>

Thank You.
@endcomponent