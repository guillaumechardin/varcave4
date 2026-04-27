@include('varcave.template.header')
@include('varcave.template.navbar')
<section class="section">
    <h1 class="title is-1">
    {{ __('varcave.resources.pageTitle') }}
    </h1>

    <script>
        <x-varcave.resources.resources-js />
    </script>

@can('isResourceAdmin', App\Models\FileResource::class)
    <div class="columns mt-4">
        <div class="column is-one-quarter">
            
            <h1  class="title is-4 show-add-res-wrapper" >{{Str::upper(__('varcave.resources.add_file'))}}
                <span class="icon is-small">
                    <i class="bi bi-chevron-down"></i>
                </span>
            </h1>
            <h1 class="show-add-res-wrapper subtitle  is-6" >{{__('varcave.resources.show_more')}}</h1>
            <div class="mt-1 has-background-primary" style="height: 2px; width: 19%;"></div>
            @if ($errors->upload->any())
                <div class="notification is-danger mt-2">
                    @foreach ($errors->upload->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
            <div id="add-res-wrapper"   @class([
                                            'is-hidden' => !$errors->upload->any(),
                                        ])
            >
                <form id="add-res-form" 
                    action="{{ route('varcave.resource.store') }}" 
                    method="post"
                    enctype="multipart/form-data"
                >
                    @csrf

                    <div class="field">
                        <label class="label">{{__('varcave.resources.file_title')}}</label>
                        <div class="control">
                            <input class="input"
                                name="file-title-name" 
                                type="text" 
                                placeholder="{{__('varcave.resources.file_title_phldr')}}"
                                value="{{old('file-title-name')}}" 
                            />
                        </div>
                    </div>

                    <div class="field">
                        <div class="control">
                            <label class="radio">
                                <input type="radio" 
                                    name="radio-group" 
                                    value="new-group" 
                                    @checked(old('radio-group')  === 'new-group')
                                />
                                {{__('varcave.resources.create_group')}}
                            </label>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label">{{__('varcave.resources.new_group')}}</label>
                        <div class="control">
                            <input class="input"
                                name="new-group" 
                                type="text" 
                                placeholder="{{__('varcave.resources.new_group_phldr')}}" 
                                value="{{old('new-group')}}" 
                                @disabled(old('radio-group')  === 'existing-group' || old('radio-group') == null)
                            />
                        </div>
                    </div>

                    <div class="field">
                        <div class="control">
                            <label class="radio">
                                <input type="radio" name="radio-group" value="existing-group"  
                                    @checked(old('radio-group')  === 'existing-group' || old('radio-group') == null )
                                />
                                {{__('varcave.resources.use_existing_group')}}
                            </label>
                        </div>
                    </div>
                    
                    <div class="field">
                        <div class="control">
                            <label class="label">{{__('varcave.resources.choose_group')}}</label>
                            <div class="select">
                                <select name="group"
                                    @disabled(old('radio-group')  === 'new-group')
                                >
                                    <option value="" selected disabled>{{__('varcave.resources.choose')}}</option>
                                    @foreach($fileResourceByGroup as $rg)
                                        <option value="{{$rg->id}}"
                                            @selected(old('group') == $rg->id)
                                        >
                                            {{ Str::upper($rg->name) }}
                                        </option>                    
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="field">
                        <div class="control">
                            <label class="label">{{__('varcave.resources.select_rights')}}</label>
                            <div class="select is-multiple" >
                                <select name="access_rights[]" style="width:100%"  multiple size="4">
                                    <option disabled>{{__('varcave.resources.add_rem_group')}}</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}"
                                            @selected( in_array($role->id, (array)old('access_rights')) )
                                        >   
                                            @if( !empty(__('varcave.roles.'. $role->name)) ) 
                                                {{ __('varcave.roles.' . $role->name) }}
                                            @else
                                                {{ $role->name }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="field">
                        <div class="control">
                            <label class="label">{{__('varcave.resources.description')}}</label>
                            <textarea name="description" 
                                class="textarea has-fixed-size" 
                                maxlength="255"
                                rows="5" >{{old('description')}}</textarea>
                        </div>
                    </div>
                    
                    <div class="field">
                        <div class="file has-name is-boxed mt-5">
                            <label class="file-label">
                                <input class="file-input" type="file" name="file" />
                                <span class="file-cta">
                                    <span class="file-icon">
                                        <i class="bi bi-cloud-arrow-up"></i>
                                    </span>
                                    <span class="file-label">{{Str::ucfirst( __('varcave.general.choose_file_short') ) }}...</span>
                                </span>
                                <span class="file-name"></span>
                            </label>
                        </div>
                    </div>

                    <div class="field">
                        <p class="control">
                            <button id="save-file" class="button is-link">{{Str::ucfirst(__('varcave.general.save'))}}</button>
                        </p>
                    </div>
                </form>
            </div>{{-- end wrapper --}}
        </div> {{-- first col --}}

    </div>
@endcan

@if(session('success'))
    <div class="notification is-success">
        {{ session('success') }}
    </div>
@endif

@foreach($fileResourceByGroup as $rg)
    <div class=" mt-6">
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
                                    <p class="is-responsive has-text-weight-bold show-admin-tools" >
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
                                                            <option value="{{ $role->id }}" 
                                                                @if (in_array($role->id, $file->access_rights ))
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
                                                <label class="label">ordre de tri</label>
                                                <div class="control">
                                                    <input name="sort_order" class="input is-small" type="number" value="{{ $file->sort_order }}" />
                                                </div>
                                            </div>
                                            <button class="button mt-2 is-size-7 is-link">{{Str::ucfirst(__('varcave.resources.general.save'))}}</button>
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
</section>

@include('varcave.template.footer')