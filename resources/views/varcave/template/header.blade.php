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
    <title>{{ config('app.name') }}</title>
    <link rel="stylesheet" href="/lib/bulma/1.0.4/css/bulma.min.css">
    <link rel="stylesheet" href="/varcave/varcave.css" />
    <!--<link rel="stylesheet" href="/fontawesome-free-6.7.2-web/css/solid.css" /> -->
    <link rel="stylesheet" href="/lib/bootstrap-icons/1.13.1/bootstrap-icons.min.css" />
    <script src="/lib/jquery/jquery-3.7.1.min.js"></script>
    <script>
      const generalLogLevel =  "{{ env('LOG_LEVEL') }}" ;
    </script>
    <script src="/varcave/varcave.js"></script>
    <!-- <script src="/feather/js/feather.min.js"></script> -->
    

  </head>
  <body>
  
<!-- modal message -->
  <div  id="modal-message" class="modal">
    <div class="modal-background">
    </div>
    <div class="modal-card">
      <header class="modal-card-head">
        <p class="modal-card-title" id="modal-message-title">Modal title</p>
        <button class="delete modal-message-close"  aria-label="{{ __('close') }}"></button>
      </header>
      <section id="modal-message-body" class="modal-card-body">
        <!-- Futur content -->
      </section>
      <footer class="modal-card-foot">
        <div id="modal-message-buttons" class="buttons">
          
          <button id="modal-message-button-cancel" class="button">{{ __('rigmgr.general.cancel') }}</button>
        </div>
      </footer>
    </div>
  </div>

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

  <!-- MESSAGE BOX -->
  <div id="varcave-wrapper-message-box" class="columns is-mobile fixed-top-message">
    <div class="column"></div>
    <div class="column">
      <article id="varcave-message-box" class="message is-small is-one-third is-hidden">
        <div class="message-header">
          <p id="varcave-message-box-header"></p>
          <button class="delete is-small" aria-label="delete"></button>
        </div>
        <div id="varcave-message-box-body" class="message-body">
          <!-- message content -->
        </div>
      </article>
    </div>
    <div class="column"></div>
  </div>
  <!-- END MESSAGE BOX -->
  






  

    
  
