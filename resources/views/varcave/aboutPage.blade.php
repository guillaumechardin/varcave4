@include('varcave.template.header')
@include('varcave.template.navbar')
    <section class="section">
        <section class="hero">
            <div class="hero-body">
                <p class="title">{{ Str::ucfirst(__('varcave.update_page.title')) }}</p>
            </div>
        </section>
        <div class="box as-text-white">
            <div class="content">
                {!! $aboutContent !!}
            </div>
         </div>
    </section>

@include('varcave.template.footer')
