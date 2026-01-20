@include('varcave.template.header')
@include('varcave.template.navbar')

<section class="hero is-warning">
  <div class="hero-body">
    <p class="title">404</p>
    <p class="subtitle">{{ $exception->getMessage() }}</p>
  </div>
</section>

@include('varcave.template.footer')