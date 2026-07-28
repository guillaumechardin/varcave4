@include('varcave.template.header')
@include('varcave.template.navbar')
<section class="section">
    <h1 class="title is-1">
        {{ __('varcave.resources.pageTitle') }}
    </h1>

    <script>
        <x-varcave.resources.resources-js />
    </script>

    <div id="create-resource-tabs" class="tabs is-toggle is-toggle-rounded is-centered is-fullwidth" data-bulma="tabs">
        <ul>
            <li>
                <a  data-tabs-target="tab-documents">
                    <span class="icon is-small">
                        <i class="bi bi-file-earmark-text" aria-hidden="true"></i>
                    </span>
                    <span>{{ Str::ucfirst(__('varcave.resources.documents')) }}</span>
                </a>
            </li>
            @can('isResourceAdmin', App\Models\FileResource::class)
                <li>
                    <a  data-tabs-target="tab-create-resource">
                        <span class="icon is-small">
                            <i class="bi bi-file-earmark-plus" aria-hidden="true"></i>
                        </span>
                        <span>{{ Str::ucfirst(__('varcave.resources.create_file')) }}</span>
                    </a>
                </li>
            
                <li>
                    <a  data-tabs-target="tab-build-gpx">
                        <span class="icon is-small">
                            <i class="bi bi-pin-map" aria-hidden="true"></i>
                        </span>
                        <span>{{ Str::ucfirst(__('varcave.resources.build_gpx')) }}</span>
                    </a>
                </li>
            @endcan
        </ul>
    </div>
    @if(session('success'))
        <div class="notification is-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="notification is-danger mt-2">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div id="tabs-contents">
        <ul>
            <li>
                <div id="tab-documents" class="tab-content mx-2 mt-2">
                    <x-varcave.resources.documents 
                        :fileResourceByGroup="$fileResourceByGroup"
                        :roles="$roles"
                    />
                </div>
            </li>
            @can('isResourceAdmin', App\Models\FileResource::class)
                <li>
                    <div id="tab-create-resource" class="tab-content mx-2 mt-2">
                        <x-varcave.resources.create-resource 
                            :errors="$errors"
                            :fileResourceByGroup="$fileResourceByGroup"
                            :roles="$roles"
                        />
                    </div>
                </li>
            
                <li>
                    <div id="tab-build-gpx" class="tab-content mx-2 mt-2">
                            <x-varcave.resources.build-gpx :countAllCaves="$countAllCaves"/>
                    </div>
                </li>
            @endcan
        </ul>
    </div>  {{--  end  data-bulma="tabs" --}}
</section>

@include('varcave.template.footer')





