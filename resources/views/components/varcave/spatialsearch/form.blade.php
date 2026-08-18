@props([
    //'errors',
])
<h1 class="title is-3">
    {{ __('varcave.spatial_search.title_load_file')  }}
</h1>
<h1 class="subtitle is-5">
    {{ __('varcave.spatial_search.subtitle_load_file', ['exts' => implode('/', array_keys(\App\Services\SpatialFileService::getPermitedMimeTypes()) )])  }}
</h1>
@if ($errors->any())
    <div class="notification is-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form   id="form-spatial-load-file"
        name="form-spatial-load-file"
        method="POST" 
        enctype="multipart/form-data"
        action="{{ route('varcave.caves.spatialSearch') }}"
>
    <div class="field">
        <label class="label">{{ __('varcave.general.choose_file_type') }}</label>
        <div class="select">
            <select required id="user-selected-file-type" name="user-selected-file-type">
                <option selected disabled value="">--{{ __('varcave.general.choose') }}--</option>
                @foreach(array_keys(\App\Services\SpatialFileService::getPermitedMimeTypes()) as $ext)
                    <option value="{{ $ext }}">{{ $ext }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="field">
        <div class="file has-name is-boxed">
            <label class="file-label">
                @csrf
                <input required id="spatial-file" class="file-input" type="file" name="spatial-file" />
                <span class="file-cta">
                    <span class="file-icon">
                        <i class="bi bi-upload"></i>
                    </span>
                    <span class="file-label">
                        {{ __('varcave.general.choose_file_short')  }}
                    </span>
                </span>
                <span class="file-name">
                    {{ Str::ucfirst(__('varcave.general.noFileSelected')) }}
                </span>
            </label>
        </div>
    </div>
    <div class="field">
        <div class="control">
            <button class="button is-link">{{ Str::ucfirst(__('varcave.general.send')) }}</button>
        </div>
    </div>
</form>