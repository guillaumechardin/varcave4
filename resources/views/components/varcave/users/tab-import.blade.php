@props([
    'user_cols',
    'expirationDate'
])
<div id="wrapper-users-import">
    <h1 class="title is-3">Paramètres d'import</h1>
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
        {{-- Disabled autoremoval
        <script>
            $(document).ready(function(){
                setTimeout(function() {
                    $('#upload-csv-success').hide(500, function() {
                        $(this).remove();
                    });
                }, 3000);
            });
        </script>
         --}}
    @endif

    <form id="import-data" action="{{ route('varcave.users.import') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="field">
            <label class="label">Sélsctionnez un Fichier</label>
            <div class="control">
                <input id="users-import-file" class="input" type="file"  name="csv-file"/>
            </div>
            <p class="help">
                Remplissez le formulaire, sélectionnez un fichier d'import et valider le formulaire
                pour ajouter des utilisateurs.<br/>
                Format du CSV:<br/>
                &nbsp;&nbsp;No header line<br/>
	            &nbsp;&nbsp;format (UTF-8) :    
            </p>
        </div>
        
        <div class="field">
            <label class="label">Date d'eXPiration des comptes</label>
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
                Submit
                </button>
            </p>
            <p class="control">
                <button type="reset" class="button is-light">
                Cancel
                </button>
            </p>
        </div>  
    </form>    
</div>