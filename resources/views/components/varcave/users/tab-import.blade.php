@props([
    'user_cols',
    'expirationDate'
])

<div id="wrapper-users-import">
    <h1 class="title is-3">{{ __('varcave.users.import_settings') }}</h1>
    @if ($errors->import->any())
        <div class="is-background-danger">
            <ul class="has-text-danger">
                @foreach ($errors->import->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('upload-csv-success'))
        <article id="upload-csv-success" class="message is-success">
            <div class="message-body content">
                {{ session('upload-csv-success') }}
            </div>
        </article>
    @endif

    <form id="import-data" action="{{ route('varcave.users.import') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="field">
            <label class="label">{{ __('varcave.users.select_file') }}</label>
            <div class="control">
                <input id="users-import-file" class="input" type="file"  name="csv-file"/>
            </div>
            <p class="help">
                {{ __('varcave.users.import_help') }}
            
                {{ __('varcave.users.csv_format') }}
                <ul class="help">
                    <li>{{ __('varcave.users.no_header') }}</li>
                    <li>{{ __('varcave.users.csv_encoding') }}</li>
                    <li>{{ __('varcave.users.field_format') }}&nbsp;&nbsp;: username;password;firstname;lastname;email;organisation</li>
                </ul>
            </p>
        </div>
        
        <div class="field">
            <label class="label">{{ __('varcave.users.accnt_expiration_date')}}</label>
            <div class="control">
                <input id="users-import-expiration-datepicker" class="input" name="import-expires-at" type="text" value="{{ $expirationDate}}">
            </div>
            {{--
            <div class="control">
                <label class="checkbox">
                    <input name="no-expiry" type="checkbox" >
                    {{ Str::ucfirst( __('varcave.users.no_expiry')) }}
                </label>
            </div>
            --}}
        </div>
        <div class="field is-grouped">
            <p class="control">
                <button class="button is-primary">
                {{ Str::ucfirst(__('varcave.general.send')) }}
                </button>
            </p>
            <p class="control">
                <button type="reset" class="button is-light">
                {{ Str::ucfirst(__('varcave.general.cancel')) }}
                </button>
            </p>
        </div>  
    </form>    
</div>