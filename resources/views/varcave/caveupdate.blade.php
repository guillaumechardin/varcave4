@include('varcave.template.header')
@include('varcave.template.navbar')

<script>
    const caveUuid = "{{ $caveObj->uuid }}";
    <x-varcave.caveupdate.caveupdate-js :uuid="$caveObj->uuid"/>
</script>

<section class="section">
    <section class="hero">
        <div class="hero-body">
            <p class="title is-italic">{{ __('varcave.cave_update.editCave', ['caveName' => $caveData['attributes']['data']['name'], ]) }}</p>
        </div>
    </section>
<section>
    <div id="caveupdate-tabs" class="tabs is-toggle is-toggle-rounded is-centered is-fullwidth" data-bulma="tabs">
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
                <a  data-tabs-target="tab-cave-access">
                    <span class="icon is-small">
                        <i class="bi bi-geo" aria-hidden="true"></i>
                    </span>
                    <span>{{ Str::ucfirst(__('varcave.caveshow.access')) }}</span>
                </a>
            </li>
            <li>
                <a  data-tabs-target="tab-cave-files">
                    <span class="icon is-small">
                        <i class="bi bi-file-earmark-richtext" aria-hidden="true"></i>
                    </span>
                    <span>{{ Str::ucfirst(__('varcave.cave_update.files')) }}</span>
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
        </ul>
    </div>

    <div id="tabs-contents">
        <ul>
            <li>
                <div id="tab-cave-info" class="tab-content mx-2 mt-2">
                        <x-varcave.caveupdate.tab-cave-info :caveData="$caveData" :caveDescription="$caveDescription" :caveObj="$caveObj"/>
                </div>
            </li>
            
            </li>
            <li>
                <div id="tab-cave-access" class="tab-content mx-2 mt-2"> 
                         <x-varcave.caveupdate.tab-access :caveAccess="$caveAccess" />
                </div>
            </li>
            <li>
                <div id="tab-cave-files" class="tab-content mx-2 mt-2">
                        <x-varcave.caveupdate.tab-files :caveFiles="$caveFiles" :caveFileList="$caveFileList"/>
                </div>
            </li>
            
            <li>
                <div id="tab-cave-changehistory" class="tab-content mx-2 mt-2">
                        <x-varcave.caveupdate.tab-changehistory :changelog="$changelog" />
                </div>
            </li>
        {{--
            <li>
                <div id="tab-cave-maps" class="tab-content mx-2 mt-2">
                        <x-varcave.caveupdate.tab-cave-maps :caveMaps="$caveDescription['caveFiles']['cave_maps'] ?? []"/>
                </div>
            </li> 
            <li>
                <div id="tab-photos" class="tab-content mx-2 mt-2">
                        <x-varcave.caveupdate.tab-photos :photos="$caveDescription['caveFiles']['photos'] ?? []"/>
                </div>
            </li>
            <li>
                <div id="tab-bibliography" class="tab-content mx-2 mt-2">
                    <x-varcave.caveupdate.tab-bibliography :bibliography="$caveBibliography['attributes']['data']['bibliography']" />
                </div>
            </li>
            <li>
                <div id="tab-documents" class="tab-content mx-2 mt-2">
                    <x-varcave.caveupdate.tab-documents :caveDocsFiles="$caveDocsFiles" :caveDocsPhotos="$caveDocsPhotos"/>
                </div>
            </li>
            <li>
                <div id="tab-rescue-info" class="tab-content mx-2 mt-2">
                    <x-varcave.caveupdate.tab-rescue :rescueFiles="$rescueFiles"/>
                </div>
            </li>
        --}}
        </ul>
    </div>

</section>

@include('varcave.template.footer')