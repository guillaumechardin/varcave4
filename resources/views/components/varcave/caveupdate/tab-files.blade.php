@props([
    'caveFiles',
    'caveFileList',
    'fileTypeList',
    'caveUuid',
])
<div class="box">
     <p class="title is-5 is-icon-clickable"  id="show-add-file-form">
                {{ __('varcave.cave_update.add_new_file') }}
                <span class="is-icon-clickable">
                    <i class="bi bi-chevron-right"></i>
                </span>
            </p>
    <div id="add-file-wrapper" 
        @class([
            "is-active" => $errors->upload->any(),
            'is-hidden' => !$errors->upload->any(),
        ])
    >
        <form 
            name="add-file"
            action="{{ route('varcave.caves.file.create',['uuid' => $caveUuid]) }}" 
            method="post"
            enctype="multipart/form-data"
        >

            <div class="field">
                <label class="label">
                    {{ Str::ucfirst(__('varcave.general.chooseFile')) }}
                </label>
                <div class="control">
                    <div class="file is-info has-name">
                        <label class="file-label">
                            <input id="file-input" class="file-input" type="file" name="new-file"  />
                            <span class="file-cta">
                                <span class="file-icon">
                                    <i class="bi bi-upload"></i>
                                </span>
                                <span class="file-label">
                                    {{ __('varcave.general.choose_file_short') }}
                                </span>
                            </span>
                            <span class="file-name" id="selected-file-name">---</span>
                        </label>
                    </div>
                </div>
            </div>  

            <div class="field">
                <label class="label">
                    {{ __('varcave.cave_update.file_note') }}
                </label>
                <div class="control">
                    <input class="input" type="text"   name="file-note" style="width:25em" placeholder="{{ __('varcave.cave_update.add_file_note') }}" value="{{ old('file-note') }}">
                </div>
            </div>

            <div class="field">
                <label class="label">
                    {{ __('varcave.cave_update.choose_category') }}
                </label>
                <div class="control">
                    <div class="select is-info">
                        <select name="file-group" required>
                            <option value="" @selected(old('file-group', '') == '')>
                                --{{ Str::ucfirst(__('varcave.general.choose')) }}--
                            </option>
                            @foreach($fileTypeList as $filetype)
                                <option 
                                    value="{{ $filetype['value'] }}"
                                    @selected(old('file-group', '') == (int) $filetype['value'])
                                >   
                                    {{ Str::ucfirst(__($filetype['i18n_key'])) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>     

            <div class="field is-grouped">
                <div class="control">
                    <button class="button is-primary">
                        {{ Str::ucfirst(__('varcave.general.send')) }}
                    </button>
                </div>
            </div>
        </form>
    </div>
    <hr>
    <div class="block">
        <label class="checkbox">
            <input id="permit-file-deletion" type="checkbox" />
                {{ __('varcave.cave_update.unlock_del_files') }}
        </label>
    </div>
    @foreach($caveFileList as $li)
        <div class="fixed-grid has-4-cols">
            @php
                $currFiles = str_replace('varcave.cave_files.', '', $li->i18n_key);
            @endphp
            @if(isset($caveFiles[$currFiles]))
                <p class="title is-5"> {{ Str::ucfirst(__($li->i18n_key)) }}</p>
                <div class="grid">
                    @foreach($caveFiles[$currFiles] as $f)
                        <div class="cell">
                            <div class="card">
                                @if($f['is_img'])
                                    <div class="card-image">
                                        <figure class="image is-3by2">
                                            <img src="{{ asset('storage/'.$f['file_path']) }}" />
                                        </figure>
                                    </div>
                                    <div class="is-flex is-justify-content-space-between is-align-items-center m-1">
                                        <span class="is-icon-wrapper bi-md is-icon-clickable">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </span>

                                        <span class="is-icon-wrapper bi-md is-icon-clickable">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </span>
                                    </div>
                                @else
                                    <div class="card-image">
                                            <div class="has-text-centered is-flex is-justify-content-center is-align-items-center" style="height: 120px;">
                                            <span class="icon is-icon-wrapper bi-xxl">
                                                <a href="{{ asset('storage/'.$f['file_path']) }}" class="{{ $f['icon-class'] }}" ></a>
                                            </span>
                                        </div>
                                    </div>
                                @endif
                                    <div class="card-content">
                                        <p>
                                            
                                        </p>
                                        <div class="content">
                                            <input form="form-note-{{ $f['file_id'] }}" name="file-note" class="file-note" style="width:100%" value="{{ $f['file_note'] }}" placeholder="[{{ __('varcave.cave_update.add_file_note') }}]"/>
                                        </div>
                                    </div>
                                
                                <footer class="card-footer">
                                    <form id="form-note-{{ $f['file_id'] }}" method="POST" action="{{ route('varcave.caves.file.patch', ['uuid' => $caveUuid]) }}" style="display: contents;">
                                        @method('patch')
                                        <input type="hidden" name="fileId" value="{{ $f['file_id'] }}"/>
                                        <button type="submit" class="card-footer-item button has-text-link">
                                            {{ Str::ucfirst(__('varcave.general.save')) }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('varcave.caves.file.destroy', ['uuid' => $caveUuid]) }}" style="display: contents;">
                                        @method('delete')
                                        <input type="hidden" name="fileId" value="{{ $f['file_id'] }}"/>
                                        <button disabled type="submit" class="card-footer-item del-file-button button has-text-link">
                                            {{ Str::ucfirst(__('varcave.general.delete')) }}
                                        </button>
                                    </form>
                                </footer>
                            </div>
                        </div> {{-- end cell --}}
                    @endforeach
                </div>
            @endif    
        </div>
    @endforeach
</div>