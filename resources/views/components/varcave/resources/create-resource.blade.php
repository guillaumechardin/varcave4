@props([
    'role',
    'fileResourceByGroup',
    'roles',
    'errors',
])

    

    @if ($errors->upload->any())
        <div class="notification is-danger mt-2">
            @foreach ($errors->upload->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="add-wrapper">
        <h1  class="title is-4 show-add-res-wrapper">
            {{ Str::upper(__('varcave.resources.add_file')) }}
        </h1>
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
                            @checked(old('radio-group') === 'new-group')
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
                                <option value="{{ $role->name }}"
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


            