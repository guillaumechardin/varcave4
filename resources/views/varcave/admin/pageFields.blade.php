@include('varcave.template.header')
@include('varcave.template.navbar')

<section class="section">
    <section class="hero">
        <div class="hero-body">
            <p class="title">{{ Str::ucfirst(__('varcave.page_fields.title')) }}</p>
        </div>
    </section>
    
    <script>
        <x-varcave.admin-pageFields-js :pagesName="$pagesName" />
    </script>

    @if (session('success'))
        <div class="notification is-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="notification is-danger">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="notification is-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="field">
        <label class="label"> FR Subject</label>
        <div class="control">
            <div class="select">
                <select id="select-pagename">
                    @foreach ($pageFields as $pageName => $pf)
                        <option $value="{{ $pageName }}">{{ $pageName }}</option>
                    @endforeach  
                </select>
            </div>
        </div>
    </div>
    
    @foreach ($pageFields as $pageName => $pageField)
        <div id="page-field-{{ $pageName }}"
             @class([
                'box',
                'is-hidden' => !$loop->first
            ])
        >
            <h1 class="title is-5">fr Champs page ['{{ $pageName }}']</h1>
            <div class="fixed-grid has-2-cols-mobile has-5-cols-desktop">
                <div class="grid grid-vertical" id="sortable-{{ $pageName }}">
                        @foreach ($pageField as  $pf)
                            <div class="cell" data-field-id="{{ $pf['id'] }}">
                                <div class="control">
                                    <div class="tags has-addons">
                                        <i @class([
                                            'tag',
                                            'sort-handle',
                                            'is-medium',
                                            'is-info' => $pf['is_visible'],
                                            'is-warning' => !$pf['is_visible'],
                                        ])>
                                            {{ $pf['i18n_name'] }}
                                        </i>
                                        <i @class([
                                            'tag',
                                            'is-medium',
                                            'bi',
                                            'toggle-visibility',
                                            'is-icon-clickable',
                                            'bi-eye' => !$pf['is_visible'],
                                            'bi-eye-slash' => $pf['is_visible'],
                                        ])>
                                        </i>
                                    </div>
                                </div>
                            </div>
                        @endforeach        
                </div>
            </div>
        </div>
    @endforeach

    <button id="save" class="button is-link">Save</button>
    <style>
        

    </style>
</section>

@include('varcave.template.footer')


