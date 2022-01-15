@component('mail::message')
Dear **{{$user['charity_name']}}**, {{-- use double space for line break --}}

Welcome to {{env('APP_NAME', 'HLD Charity')}}


Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras vel nulla tellus. Vivamus eget dignissim quam. Duis arcu lectus, convallis vitae varius sed, porttitor fringilla velit. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Etiam nunc ante, tincidunt sed mauris sed, blandit gravida neque.

Please find your login details below.

**Username:** {{$user['user_name']}}

**Password:** HollyLandDates!23


Thank You.
@endcomponent