@props([
    'fileResourceByGroup',
    'roles',
])

@foreach($fileResourceByGroup as $rg)
    <div class="mt-6">
        <p>
            <h1 class="title is-3">
                {{ Str::upper($rg->name) }}
                <div class="mt-1 has-background-primary" style="height: 2px; width: 19%;"></div>
            </h1>
        </p>
    </div>
    
    {{-- PUT CARD INTO GRID CELL --}}
    <div class="fixed-grid has-2-cols-mobile has-4-cols-desktop">
        <div class="grid">
            @foreach($rg->fileResource as $file)
                @can('getResource', $file)
                    <div class="cell"> {{-- BEGIN CELL --}}
                        <div id="card-file-{{ $file->id }}" class="card">
                            <header class="card-header">
                                <p class="card-header-title">{{Str::ucfirst($file->name)}}</p>
                            </header>
                            <div class="card-content">
                                <div class="content">
                                    {{$file->description}}     
                                </div>
                                <div class="is-italic is-size-7">
                                    <span>{{Str::ucfirst(__('varcave.general.creation_date'))}}:</span>
                                    <time datetime="{{ $file->created_at }}">{{ \Carbon\Carbon::parse($file->created_at)->format('d/m/Y H:i:s') }}</time>
                                </div>

                                @can('isResourceAdmin', $file)
                                    <p class="is-responsive has-text-weight-bold show-admin-tools is-icon-clickable" >
                                        <span class="text has-text-warning">{{__('varcave.resources.rights_mgt')}}</span>
                                        <span class="icon is-small">
                                            <i class="bi bi-chevron-down"></i>
                                        </span>
                                    </p>
                                    <div class="admin-tools-wrapper  is-size-7 mt-4 is-hidden" >
                                        <form name="edit-file-{{ $file->id }}"
                                            action="{{ route('varcave.resource.update', $file->id) }}" 
                                            method="POST"
                                            enctype="multipart/form-data"
                                        >
                                        @method('PATCH')
                                            <div class="field">
                                                <div class="select is-multiple is-size-7" >
                                                    <select name="access_rights[]" style="width:100%"  multiple size="4">
                                                        <option disabled>{{__('varcave.resources.add_rem_group')}}</option>
                                                        @foreach($roles as $role)   
                                                            <option value="{{ $role->name }}" 
                                                                @if (in_array($role->name, $file->access_rights ))
                                                                    selected
                                                                @endif
                                                            >   @if( !empty(__('varcave.roles.'. $role->name)) ) 
                                                                    {{ __('varcave.roles.' . $role->name) }}
                                                                @else
                                                                    {{ $role->name }}
                                                                @endif
                                                            </option>     
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="field">
                                                <label class="label">{{ __('varcave.resources.sort_order')}}</label>
                                                <div class="control">
                                                    <input name="sort_order" class="input is-small" type="number" value="{{ $file->sort_order ?:0 }}" />
                                                </div>
                                            </div>
                                            <button class="button mt-2 is-size-7 is-link">{{Str::ucfirst(__('varcave.general.save'))}}</button>
                                        </form>
                                    </div>
                                
                                @endcan

                            </div>

                            <footer class="card-footer">
                                    <a href="{{ route('varcave.resource.download', $file->id)}}" class="card-footer-item button is-link" >{{Str::ucfirst(__('varcave.general.download'))}}</a>
                                @can('isResourceAdmin', $file)
                                    <a data-fileid="{{ $file->id }}" data-url="{{ route('varcave.resource.delete', $file->id) }}" class="card-footer-item button is-warning delete-file">{{Str::ucfirst(__('varcave.general.delete'))}}</a>
                                @endcan
                            </footer>
                        </div>
                        <progress id="progress-delete-{{ $file->id }}" class="progress is-hidden  is-info" max="100"></progress>
                    </div> {{-- END CELL --}}
                @endcan
            @endforeach    
        </div>{{-- END GRID --}}
    </div>{{-- END FIXED GRID --}}
    
@endforeach