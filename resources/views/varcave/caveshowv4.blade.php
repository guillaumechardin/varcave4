@include('varcave.template.header')
@include('varcave.template.navbar')

<script src="/varcave/profile.js"></script>
<!-- to delete no more used ?
<script>
    const pwdConfirmationUrl = "{{ route('password.confirmation') }}";
</script>
-->
<section class="section">
    <section class="hero">
        <div class="hero-body">
            <p class="title">{{$caveData->name }}</p>
    </section>
    <div class="tabs is-centered" data-bulma="tabs">
        <ul>
            <li>
                <a  data-tabs-target="tab-cave-info">
                    <span class="icon is-small">
                        <i class="bi bi-info-square" aria-hidden="true"></i>
                    </span>
                    <span> Informations cavité </span>
                </a>
            </li>
            <li>
                <a  data-tabs-target="tab-cave-description">
                    <span class="icon is-small">
                        <i class="bi bi-blockquote-left" aria-hidden="true"></i>
                    </span>
                    <span>description & accès</span>
                </a>
            </li>
            <li>
                <a data-tabs-target="tab-cave-maps">
                    <span class="icon is-small">
                        <i class="bi bi-map" aria-hidden="true"></i>
                    </span>
                    <span>Topographies</span>
                </a>
            </li>
            <li>
                <a data-tabs-target="tab-bibliography">
                    <span class="icon is-small">
                        <i class="bi bi-book" aria-hidden="true"></i>
                    </span>
                    <span>Bibliography</span>
                </a>
            </li>
        </ul>
    </div>

    <div id="tabs-contents">
        <ul>
            <li>
                <div id="tab-cave-info" class="tab-content mx-2 mt-2">
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
                </div>
            </li>
            <li>
                <div id="tab-cave-description" class="tab-content mx-2 mt-2">
                    <x-varcave.caveshow.tab-description />
                </div>
            </li>
            <li>
                <div id="tab-cave-maps" class="tab-content mx-2 mt-2">
                    autres
                </div>
            </li> 
            <li>
                <div id="tab-bibliography" class="tab-content mx-2 mt-2">
                    Bibliographie
                    <ul>
                        <li>Livre 1</li>
                        <li>Livre 2</li>
                        <li>Livre 3</li>
                        <li>Livre 4</li>
                    </ul>
                </div>
            </li> 
        </ul>
    </div>
</section>

@include('varcave.template.footer')