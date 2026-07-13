<!-- modal message -->
<div  id="modal-message" class="modal">
  <div class="modal-background">
  </div>
  <div class="modal-card">
    <header class="modal-card-head">
      <p class="modal-card-title" id="modal-message-title"></p>
      <button class="delete modal-message-close" aria-label="{{ Str::ucfirst( __('varcave.general.close')) }}" title="{{ Str::ucfirst( __('varcave.general.close')) }}"></button>
    </header>
    <section id="modal-message-body" class="modal-card-body">
      <!-- Futur modal content -->
    </section>
    <footer class="modal-card-foot">
      <div id="modal-message-buttons" class="buttons">
        <button id="modal-message-button-cancel" class="button">{{ Str::ucfirst( __('varcave.general.cancel')) }}</button>
      </div>
    </footer>
  </div>
</div>
<!-- end modal message -->