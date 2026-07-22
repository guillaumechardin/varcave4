<!DOCTYPE html>
<html data-theme=
  @if (session('theme') === 'dark' || \Illuminate\Support\Facades\Request::user()?->theme == 'dark')
    "dark">
  @elseif (session('theme') === 'light' || \Illuminate\Support\Facades\Request::user()?->theme == 'light')
    "light">
  @else
  "">
  
  @endif
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{--  CSRF-TOKEN meta is used by varcave-js blade component in `sendAjaxRequest` --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title> 
      @isset($pageTitle) 
        {{ $pageTitle }}
      @else
        {{ config('app.name') }}
      @endif
    </title>
    <link rel="icon" type="image/png" href="/img/logo_cavite_64x64.png">

    <link rel="stylesheet" href="/lib/bulma/1.0.4/css/bulma.min.css">
    <link rel="stylesheet" href="/varcave/varcave.css" />

    <link rel="stylesheet" href="/lib/bootstrap-icons/1.13.1/bootstrap-icons.min.css" />
    
    <!-- Jquery & JqueryUI libraries -->
    <script src="/lib/jquery/jquery-3.7.1.min.js"></script>
    <script src="/lib/jquery-ui/1.14.1/jquery-ui.min.js"></script>
    <script src="/lib/jquery-ui/i18n/datepicker-{{ App::getLocale() }}.js"></script>
    <link rel="stylesheet" href="/lib/jquery-ui/1.14.1/jquery-ui.min.css">

    <!-- GLOBAL VARCAVE AVAILABLE JS  -->
    <script>
          <x-varcave.varcave-js />
    </script>

    <!-- BulmaJS Section -->
    <script src="/varcave/BulmaVar/bulma-var.js"></script>
    <script src="/varcave/BulmaVar/tabs.js"></script>
    <script src="/varcave/BulmaVar/modal.js"></script>
    <!-- END BulmaJS Section -->
    
  </head>
  <body>
  
  <x-varcave.modal />

  <x-varcave.message-box />
    
  <x-varcave.progress-bar />

  <template id="copy-modal-template">
      <x-varcave.copy-cave-modal />
  </template>

  
  






  

    
  
