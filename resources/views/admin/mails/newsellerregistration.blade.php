@component('mail::message')
Dear **{{  $data['name']  }}**,  {{-- use double space for line break --}}

{{$data['body']}}

Brand Name: {{  $data['name']  }}<br>
Seller Email: {{  $data['email']  }}<br>

Thank You.
@endcomponent