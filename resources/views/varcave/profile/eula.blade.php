@include('varcave.template.header')
@include('varcave.template.navbar')

<section class="section">
    <h1 class="title is-1">
        {{ __('varcave.profile.show_eula_title') }}
        
    </h1>
    <h2 class="subtitle">
        Version du {{ ($eula->updated_at) ? $eula->updated_at : $eula->created_at }}
    </h2>
    @if ($errors->any())
        <div class="notification is-warning">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="notification is-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="box as-text-white">
        <div class="content">
            {!! $eula->content !!}
        </div>
    </div>
    
    <form action="{{ route('varcave.profile.eula.update') }}" name="accept-eula" method="POST">
        @method('PATCH')
        @csrf
        <div class="field">
            <div class="control">
                <label class="checkbox">
                    <input name="eula_accepted" type="checkbox" value="1">
                    {{ __('varcave.profile.accept_terms') }}
                </label>
            </div>
        </div>
        
        <div class="field">
            <div class="control">
                <button class="button is-link">{{ Str::ucfirst( __('varcave.general.send')) }}</button>
            </div>
        </div>
    </form>

</section>

@include('varcave.template.footer')