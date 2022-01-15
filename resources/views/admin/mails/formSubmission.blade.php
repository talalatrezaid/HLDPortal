@component('mail::message')
    <?php
    $x          = $user['message_body'];
    $find       = array_keys($user);
    $replace    = array_values($user);
    $newString  = str_ireplace($find, $replace, $x);
    $newString  = str_replace('[', '', $newString);
    $newString  = str_replace(']', '', $newString);
    echo $newString;
    ?>
@endcomponent
