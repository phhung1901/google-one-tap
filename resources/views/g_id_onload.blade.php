<script src="https://accounts.google.com/gsi/client" async="" defer=""></script>
<div id="g_id_onload"
     data-client_id="{{config('services.google.client_id')}}"
     data-login_uri="{{config('services.google.redirect')}}"
     data-_token="{{ csrf_token() }}">
</div>
