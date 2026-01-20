@include('varcave.template.header')
@include('varcave.template.navbar')

<section class="section">
    <div class="fixed-grid has-2-cols-mobile has-5-cols-desktop">
            
        <div class="grid">
            <!-- <pre>{{print_r($pageFields->pageFields)}}</pre>-->
            @foreach ($pageFields->pageFields as $pf )
                <div class="cell">
                    <p class="title is-5"> {{ $pf->field->label() }} :</p>
                    <p class="subtitle is-7"> {{ $caveData->{$pf->field->key} }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@include('varcave.template.footer')