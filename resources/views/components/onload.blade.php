@php
    $custom = config('google-one-tap.custom');

    $attributesText = '';

    foreach ($custom as $key => $value) {
        if (is_bool($value)) {
            $value = ($value) ? 'true' : 'false';
        }

        $attributesText .= 'data-'.$key.'="'.$value.'" ';
    }
@endphp


<div id="g_id_onload"
     data-auto_prompt="{{config('google-one-tap.enable')}}"
     data-client_id="{{config('google-one-tap.client_id')}}"
     data-login_uri="{{config('google-one-tap.call_back')}}"
     data-_token="{{ csrf_token() }}"
     {!! $attributesText !!}>
</div>
