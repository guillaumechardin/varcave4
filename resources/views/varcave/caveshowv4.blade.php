@include('varcave.template.header')
@include('varcave.template.navbar')

<link rel="stylesheet" href="/lib/glightbox/3.3.0/dist/css/glightbox.css" />
<script src="/lib/glightbox/3.3.0/dist/js/glightbox.min.js"></script>
<script>
    <x-varcave.caveshow.caveshow-js :caveName="$caveName" :uuid="$caveObj->uuid" />
    const caveUuid = "{{ $caveObj->uuid }}";
</script>

<section class="section">
    <section class="hero">
        <div class="hero-body">
            <p class="title">{{ $caveName}}</p>
        </div>
    </section>
    {{-- small notifications for cave restricted details --}}
    @if($caveObj->no_access == 1)
        <div id="no-access-notification" class="notification is-danger">
            <button class="delete"></button>
            <div id="no-access-content">
                {{ \App\Models\Setting::get('no_access_message') }}
            </div>
        </div>
    @endif

    @if($caveObj->is_location_protected == 1)
        <div id="location-protected-notification" class="notification is-warning">
            <button class="delete"></button>
            <div id="location-protected-content">
                {{ \App\Models\Setting::get('location_protected_message') }}
            </div>
        </div>
    @endif

    <div class="columns is-mobile">
        <div class="column is-background-info is-flex">
            <span>
                <button id="caveshow-action-sendmail" class="icon is-icon-wrapper bi-xl " title="{{ __('varcave.caveshow.informChange') }}" >
                    <i class="bi bi-envelope-at-fill has-text-link"></i>
                </button>   
            </span>
            @can('updateCave', $caveObj)     {{-- START OF `CAN' showAllCaveDetails FEATURES --}}
                <span id="caveshow-action-copy" class="icon is-icon-wrapper bi-xl" title="{{ __('varcave.caveshow.copy_cave') }}">
                    <a href="#" class="bi bi-copy"></a>
                </span>
                <span id="caveshow-action-update" class="icon is-icon-wrapper bi-xl" title="{{ __('varcave.caveshow.edit_cave') }}">
                    <a href="{{ route('varcave.caves.caveEditPage', ['uuid' => $caveObj->uuid ]) }}" class="bi bi-pencil-square"></a>
                </span>
            @endcan     {{-- END OF `CAN' showAllCaveDetails FEATURES --}}

            @can('showAllCaveDetails', $caveObj)     {{-- START OF `CAN' showAllCaveDetails FEATURES --}}
                <span id="caveshow-action-gpxdownload" class="icon is-icon-wrapper bi-xl" title="{{ __('varcave.caveshow.dl_gpx') }}">
                    <a href="{{ route('varcave.caves.gpx', ['uuid' => $caveObj->uuid ]) }}" class="bi bi-geo-alt-fill" ></a>
                </span>
                <span id="caveshow-action-setfav" class="icon is-icon-wrapper bi-xl" title="{{ __('varcave.caveshow.add_favorites') }}">
                    <div>
                        @if(auth()->user()->isBookmark($caveObj->uuid))
                            <a class="bi bi-star-fill"></a>
                        @else
                            <a class="bi bi-star"></a>
                        @endif
                        <progress id="progress" class="progress is-small is-primary" max="100" style="display:none">15%</progress>
                    </div>
                    
                </span>
                <span id="caveshow-action-pdfdownload" class="icon is-icon-wrapper bi-xl" title="{{ __('varcave.caveshow.dl_pdf') }}">
                    <a class="bi bi-file-pdf-fill" href="{{  route('varcave.caves.pdf', ['uuid' => $caveObj->uuid]) }}"></a>
                </span>
                <span class="mr-4">&nbsp;</span>
            @endcan     {{-- END OF `CAN' showAllCaveDetails FEATURES --}}
        </div>
    </div>
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
            <li>
                <a data-tabs-target="tab-cave-changehistory">
                    <span class="icon is-small">
                        <i class="bi bi-clock-history" aria-hidden="true"></i>
                    </span>
                    <span>{{ Str::ucfirst(__('varcave.caveshow.change_history')) }}</span>
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
                    <a data-tabs-target="tab-photos">
                        <span class="icon is-small">
                            <i class="bi bi-file-earmark-richtext" aria-hidden="true"></i>
                        </span>
                        <span>{{ Str::ucfirst(__('varcave.caveshow.photos')) }}</span>
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
                @can('showRescueInfo', $caveObj)
                <li>
                    <a data-tabs-target="tab-rescue-info">
                        <span class="icon is-small">
                            <i class="bi bi-file-earmark-richtext" aria-hidden="true"></i>
                        </span>
                        <span>{{ Str::ucfirst(__('varcave.caveshow.rescue_info')) }}</span>
                    </a>
                </li>
                @endcan
            @endcan    {{-- END OF `CAN' FEATURES --}}
        </ul>
    </div>

    {{-- start of form error message --}}
    @if (session('error') || $errors->any() || session('success'))
        <div @class([
                'notification',
                'mt-2',
                'is-danger' => $errors->any() || session('error'),
                'is-success' => session('success'),
            ])
        >
        <button class="delete notification-delete-button"></button>
            @if($errors->any())
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @elseif( session('error') )
                {{ session('error') }}
            @else
                {{ session('success') }}
            @endif
        </div>
    @endif
    {{-- end of form error message  --}}

    <div id="tabs-contents">
        <ul>
            <li>
                <div id="tab-cave-info" class="tab-content mx-2 mt-2">
                        <x-varcave.caveshow.tab-cave-info :caveData="$caveData"/>
                </div>
            </li>
            <li>
                <div id="tab-cave-changehistory" class="tab-content mx-2 mt-2">
                        <x-varcave.caveshow.tab-changehistory :changeHistory="$changeHistory" />
                </div>
            </li>
            @can('showAllCaveDetails', $caveObj)     {{-- START OF `CAN' FEATURES --}} 
                <li>
                    <div id="tab-cave-description" class="tab-content mx-2 mt-2">
                            <x-varcave.caveshow.tab-description :caveDescription="$caveDescription['attributes']" />
                    </div>
                </li>
                <li>
                    <div id="tab-cave-access" class="tab-content mx-2 mt-2">
                            <x-varcave.caveshow.tab-access :caveAccess="$caveAccess['attributes']" :caveCoords="$caveData['coordinates']" :caveName="$caveName" :crs="$crs"/>
                    </div>
                </li>
                <li>
                    <div id="tab-cave-maps" class="tab-content mx-2 mt-2">
                            <x-varcave.caveshow.tab-cave-maps :caveMaps="$caveDescription['caveFiles']['cave_maps'] ?? []"/>
                    </div>
                </li> 
                <li>
                    <div id="tab-photos" class="tab-content mx-2 mt-2">
                            <x-varcave.caveshow.tab-photos :photos="$caveDescription['caveFiles']['photos'] ?? []"/>
                    </div>
                </li>
                <li>
                    <div id="tab-bibliography" class="tab-content mx-2 mt-2">
                        <x-varcave.caveshow.tab-bibliography :bibliography="$caveBibliography['attributes']['data']['bibliography']" />
                    </div>
                </li>
                <li>
                    <div id="tab-documents" class="tab-content mx-2 mt-2">
                        <x-varcave.caveshow.tab-documents :caveDocsFiles="$caveDocsFiles" :caveDocsPhotos="$caveDocsPhotos"/>
                    </div>
                </li>
                @can('showRescueInfo', $caveObj)
                <li>
                    <div id="tab-rescue-info" class="tab-content mx-2 mt-2">
                        <x-varcave.caveshow.tab-rescue :rescueFiles="$rescueFiles"/>
                    </div>
                </li>
                @endcan
            @endcan  {{-- END OF CAN FEATURES --}} 
        </ul>
    </div>
</section>

<template id="tmpl-contact-form">
    <x-varcave.mail-contact-form 
        :subject=" __('varcave.caveshow.email_subject',['caveName' => $caveName ])"
        :messageDfltBody="__('varcave.caveshow.email_body_default_link') . ' ' .  route('varcave.caves.show', ['uuid' => $caveObj->uuid])"/>
</template>

@include('varcave.template.footer')