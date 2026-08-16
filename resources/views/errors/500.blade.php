@include('varcave.template.header')
@include('varcave.template.navbar')

<section class="hero is-danger">
  <div class="hero-body">
    <p class="title">500</p>
    <p class="subtitle">{{ $exception->getMessage() }}</p>
  </div>
</section>

@include('varcave.template.footer')