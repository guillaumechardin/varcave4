@include('varcave.template.header')
@include('varcave.template.navbar')

<link rel="stylesheet" href="/lib/glightbox/3.3.0/dist/css/glightbox.css" />
<script src="/lib/glightbox/3.3.0/dist/js/glightbox.min.js"></script>
<script src="/varcave/caveshow.js"></script>
<!-- to delete no more used ?
<script>
    const pwdConfirmationUrl = "{{ route('password.confirmation') }}";
</script>
-->
<section class="section">
    <section class="hero">
        <div class="hero-body">
            <p class="title">{{$cave->name}}</p>
        </div>
    </section>

    <div id="caveshow-tabs" class="tabs is-toggle is-toggle-rounded is-centered is-fullwidth" data-bulma="tabs">
        <ul>
            <li>
                <a  data-tabs-target="tab-cave-info">
                    <span class="icon is-small">
                        <i class="bi bi-info-square" aria-hidden="true"></i>
                    </span>
                    <span>{{ Str::ucfirst(__('varcave.caveshow.informations')) }}</span>
                </a>
            </li>
            @can('showAllCaveDetails', $caveObj)     {{-- START OF `CAN' FEATURES --}} 
                <li>
                    <a  data-tabs-target="tab-cave-description">
                        <span class="icon is-small">
                            <i class="bi bi-blockquote-left" aria-hidden="true"></i>
                        </span>
                        <span>{{ Str::ucfirst(__('varcave.caveshow.description')) }}</span>
                    </a>
                </li>
                <li>
                    <a  data-tabs-target="tab-cave-access">
                        <span class="icon is-small">
                            <i class="bi bi-geo" aria-hidden="true"></i>
                        </span>
                        <span>{{ Str::ucfirst(__('varcave.caveshow.access')) }}</span>
                    </a>
                </li>
                <li>
                    <a data-tabs-target="tab-cave-maps">
                        <span class="icon is-small">
                            <i class="bi bi-map" aria-hidden="true"></i>
                        </span>
                        <span>{{ Str::ucfirst(__('varcave.caveshow.caveMaps')) }}</span>
                    </a>
                </li>
                <li>
                    <a data-tabs-target="tab-bibliography">
                        <span class="icon is-small">
                            <i class="bi bi-book" aria-hidden="true"></i>
                        </span>
                        <span>{{ Str::ucfirst(__('varcave.caveshow.bibliography')) }}</span>
                    </a>
                </li>
                <li>
                    <a data-tabs-target="tab-documents">
                        <span class="icon is-small">
                            <i class="bi bi-file-earmark-richtext" aria-hidden="true"></i>
                        </span>
                        <span>{{ Str::ucfirst(__('varcave.caveshow.documents')) }}</span>
                    </a>
                </li>
            @endcan    {{-- END OF `CAN' FEATURES --}} 
        </ul>
    </div>

    <div id="tabs-contents">
        <ul>
            <li>
                <div id="tab-cave-info" class="tab-content mx-2 mt-2">
                        <x-varcave.caveshow.tab-cave-info :caveInfo="$caveInfo"/>
                </div>
            </li>
            @can('showAllCaveDetails', $caveObj)     {{-- START OF `CAN' FEATURES --}} 
                <li>
                    <div id="tab-cave-description" class="tab-content mx-2 mt-2">
                            <x-varcave.caveshow.tab-description :caveDescription="$caveDescription['description']" />
                    </div>
                </li>
                <li>
                    <div id="tab-cave-access" class="tab-content mx-2 mt-2">
                            <x-varcave.caveshow.tab-access :caveAccess="$caveAccess['access_text']" :caveCoordinates="$caveCoordinates" :nearCaves="$nearCaves" :cave="$cave" />
                    </div>
                </li>
                <li>
                    <div id="tab-cave-maps" class="tab-content mx-2 mt-2">
                            <x-varcave.caveshow.tab-cave-maps :caveMaps="$caveMaps"/>
                    </div>
                </li> 
                <li>
                    <div id="tab-bibliography" class="tab-content mx-2 mt-2">
                        <x-varcave.caveshow.tab-bibliography :bibliography="$caveBibliography['bibliography']" />
                    </div>
                </li>
                <li>
                    <div id="tab-documents" class="tab-content mx-2 mt-2">
                            <div class="gallery grid">
                                @foreach ($caveDocs as  $doc)
                                    <div class="cell">
                                        <a href="{{ asset('storage/'.$doc['file_path']) }}" class="glightbox" data-glightbox="gallery2">
                                            <img src="{{ asset('storage/'.$doc['file_path']) }}" />
                                        </a>
                                    </div>
                                    
                                @endforeach
                            </div>
                        <script>
                            const lightbox2 = GLightbox({
                                selector: '.glightbox',
                                touchNavigation: true,
                                loop: true,
                                zoomable: true
                            });
                        </script>
                    </div>
                </li>
            @endcan  {{-- END OF CAN FEATURES --}} 
        </ul>
    </div>
</section>

@include('varcave.template.footer')