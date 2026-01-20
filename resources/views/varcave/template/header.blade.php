<!DOCTYPE html>
<html data-theme=
  @if (session('theme') === 'dark')
    "dark">
  @elseif (session('theme') === 'light')
    "light">
  @else
  "">
  
  @endif
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') . ' - ' . __('varcave.homepage.title') }}</title>
    <link rel="stylesheet" href="/lib/bulma/1.0.4/css/bulma.min.css">
    <link rel="stylesheet" href="/varcave/varcave.css" />
    <!--<link rel="stylesheet" href="/fontawesome-free-6.7.2-web/css/solid.css" /> -->
    <link rel="stylesheet" href="/lib/bootstrap-icons/1.13.1/bootstrap-icons.min.css" />
    
    <script src="/lib/jquery/jquery-3.7.1.min.js"></script>

    <!-- Jquery UI libraries -->
    <script src="/lib/jquery-ui/1.14.1/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="/lib/jquery-ui/1.14.1/jquery-ui.min.css">


    <!-- Varcave settings -->
    <script>
      const generalLogLevel =  "{{ env('LOG_LEVEL') }}" ;
    </script>
    <script src="/varcave/varcave.js"></script>
    <!-- END Varcave settings -->

    <!-- BulmaJS Section -->
    <script src="/varcave/BulmaVar/bulma-var.js"></script>
    <script src="/varcave/BulmaVar/tabs.js"></script>
    <script src="/varcave/BulmaVar/modal.js"></script>
    <!-- END BulmaJS Section -->

    <!-- <script src="/feather/js/feather.min.js"></script> -->
    
  </head>
  <body>
  
  <x-varcave.modal/>

  <x-varcave.message-box />
  <!-- Progress Bar -->
  <div id="modal-progress" class="modal">
    <div class="modal-background"></div>
    <div class="modal-content">
      <div class="box">
        <div id="progress-bar" >
                <progress class="progress is-large is-info" value="" max="100"></progress>
        </div>
      </div>
    </div>
  </div>
  <!-- End progress Bar -->

  
  






  

    
  
