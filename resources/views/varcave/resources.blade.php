@include('varcave.template.header')
@include('varcave.template.navbar')
<section class="section">
    <h1 class="title is-2">
    {{ __('varcave.resources.pageTitle') }}
    </h1>

    <script>
        <x-varcave.resources.resources-js />
    </script>

@can('isResourceAdmin', App\Models\FileResource::class)
    <div class="columns mt-4">
        <div class="column is-one-quarter">
            
            <h1 id="show-add-res-wrapper" class="title is-4" >{{Str::upper('add resource')}}
                <span class="icon is-small">
                    <i class="bi bi-chevron-down"></i>
                </span>
            </h1>
            <div id="add-res-wrapper" class="is-hidden">
                <div class="control">
                    <label class="radio">
                        <input type="radio" name="radio-group" value="new-group"/>
                        Créer nouveau groupe
                    </label>
                </div>
                
                <div class="field">
                    <label class="label">Nouveau groupe</label>
                    <div class="control">
                        <input class="input" name="new-group-name" type="text" placeholder="Créer nouveau groupe" disabled>
                    </div>
                </div>

                <div class="control">
                    <label class="radio">
                        <input type="radio" name="radio-group" value="existing-group" checked/>
                        Utiliser  groupe existant
                    </label>
                </div>
                <div class="control">
                    <label class="label">Choisir groupe</label>
                    <div class="select">
                        <select name="group-name">
                            <option value="" selected disabled>choisir</option>
                            @foreach($fileResourceByGroup as $rg)
                                <option value="{{$rg->id}}">
                                {{ Str::upper($rg->name) }}
                                </option>                    
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="file has-name is-boxed mt-5">
                    <label class="file-label">
                        <input class="file-input" type="file" name="file-name" />
                        <span class="file-cta">
                            <span class="file-icon">
                                <i class="fas fa-upload"></i>
                            </span>
                            <span class="file-label">choisir fichier…</span>
                        </span>
                        <span class="file-name"></span>
                    </label>
                </div>
            </div>{{-- end wrapper --}}

        </div> {{-- first col --}}

    </div>
@endcan


@foreach($fileResourceByGroup as $rg)
    <div class=" mt-5">
        <p>
            <h1 class="title is-4">
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
                                <p class="card-header-title">{{$file->name}}</p>
                            </header>
                            <div class="card-content">
                                <div class="content">
                                    {{$file->description}}
                                    
                                </div>
                                <div class="is-italic is-size-7">
                                    <span>date de création:</span>
                                    <time datetime="{{ $file->created_at }}">{{ \Carbon\Carbon::parse($file->created_at)->format('d/m/Y H:i:s') }}</time>
                                </div>
                                @can('isResourceAdmin', $file)
                                <div class="admin-tools-wrapper  is-size-7 mt-4">
                                    <p class=" is-responsive has-text-weight-bold show-admin-tools" >
                                        <span class="text has-text-warning">Gestion des droits</span>
                                        <span class="icon is-small">
                                            <i class="bi bi-chevron-down"></i>
                                        </span>
                                    </p>
                                    <div id="right-save-status-{{$file->id}}" class="notification is-hidden">
                                    </div>
                                    <div class=" is-size-7 select is-multiple is-hidden admin-tools" >
                                        <select class="" style="width:100%"  multiple size="4">
                                            <option disabled>SELECT/REMOVE GROUP</option>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->id }}" 
                                                    @if (in_array($role->name, json_decode($file->access_rights) ))
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
                                        <button class="button mt-2 is-size-7 is-link" value="ok" name="" data-target-file="{{ $file->id }}">save</button>
                                    </div>
                                </div>
                                @endcan
                            </div>

                            <footer class="card-footer">
                                <a href="{{ Storage::disk('public')->url('file_resources/test.pdf')}}" class="card-footer-item">dowbload</a>
                                @can('isResourceAdmin', $file)
                                    <a href="#" class="card-footer-item">Delete</a>
                                @endcan
                            </footer>
                        </div>
                    </div> {{-- END CELL --}}
                @endcan
            @endforeach    
        </div>{{-- END GRID --}}
    </div>{{-- END FIXED GRID --}}
    
    
@endforeach
</section>

@include('varcave.template.footer')