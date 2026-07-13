@include('varcave.template.header')
@include('varcave.template.navbar')

<script>
    const caveUuid = "{{ $caveObj->uuid }}";
    <x-varcave.caveupdate.caveupdate-js :uuid="$caveObj->uuid" />
</script>

<section class="section">
    <section class="hero">
        <div class="hero-body">
            <p class="title is-italic">
            <a href="{{ route('varcave.caves.show', ['uuid' => $caveObj->uuid]) }}" >
                <span class="icon mr-2 is-link"><i class="bi bi-arrow-left-square"></i></span>
                {{ __('varcave.cave_update.editCave', ['caveName' => $caveData['attributes']['data']['name'], ]) }}
            </a>
            </p>
        </div>
    </section>
<section>
    <div id="caveupdate-tabs" class="tabs is-toggle is-toggle-rounded is-centered is-fullwidth" data-bulma="tabs">
        <ul>
            <li>
                <a data-tabs-target="tab-cave-info">
                    <span class="icon is-small">
                        <i class="bi bi-info-square" aria-hidden="true"></i>
                    </span>
                    <span>{{ Str::ucfirst(__('varcave.caveshow.informations')) }}</span>
                </a>
            </li>
            <li>
                <a data-tabs-target="tab-cave-access">
                    <span class="icon is-small">
                        <i class="bi bi-geo" aria-hidden="true"></i>
                    </span>
                    <span>{{ Str::ucfirst(__('varcave.caveshow.access')) }}</span>
                </a>
            </li>
            <li>
                <a data-tabs-target="tab-cave-files">
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

    {{-- start of form redirect message  --}}
    @if ($errors->upload->any())
        <div class="notification is-danger mt-2">
            @foreach ($errors->upload->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif
    {{-- end of form redirect message  --}}    

    {{-- start of form success message --}}
    @if (session('success'))
        <div class="notification is-success mt-2">
             {{ session('success') }}
        </div>
    @endif
    {{-- end of form success message  --}}

    {{-- start of form error message --}}
    @if (session('error'))
        <div class="notification is-danger mt-2">
             {{ session('error') }}
        </div>
    @endif
    {{-- end of form error message  --}} 
    

    <div id="tabs-contents">
        <ul>
            <li>
                <div id="tab-cave-info" class="tab-content mx-2 mt-2">
                        <x-varcave.caveupdate.tab-cave-info :caveData="$caveData" :caveDescription="$caveDescription" :caveObj="$caveObj"/>
                </div>
            </li>
            
            <li>
                <div id="tab-cave-access" class="tab-content mx-2 mt-2"> 
                         <x-varcave.caveupdate.tab-access :caveAccess="$caveAccess" />
                </div>
            </li>
            
            <li>
                <div id="tab-cave-files" class="tab-content mx-2 mt-2">
                    <x-varcave.caveupdate.tab-files :caveFiles="$caveFiles" :caveFileList="$caveFileList" :fileTypeList="$fileTypeList" :caveUuid="$caveObj->uuid"/>
                </div>
            </li>
            
            <li>
                <div id="tab-cave-changehistory" class="tab-content mx-2 mt-2">
                    <x-varcave.caveupdate.tab-changehistory :changelog="$changelog" />
                </div>
            </li>
        </ul>
    </div>
</section>

@include('varcave.template.footer')