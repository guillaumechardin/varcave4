@include('varcave.template.header')
@include('varcave.template.navbar')

<section class="section">
    <section class="hero">
        <div class="hero-body">
            <p class="title">{{ Str::ucfirst(__('varcave.eula.edit_eula_title')) }}</p>
        </div>
    </section>

    <script src="/lib/trumbowyg/2.1.31/dist/trumbowyg.min.js"></script>
    <script type="text/javascript" src="/lib/trumbowyg/2.1.31/dist/langs/{{ app()->getLocale() }}.min.js"></script>
    <link   rel="stylesheet" href="/lib/trumbowyg/2.1.31/dist/ui/trumbowyg.min.css">
    
    <script>
        <x-varcave.admin-eula-js />
    </script>

    @if(session('error'))
        <div class="notification is-danger">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('varcave.eula.update') }}" method="post" id="eula-form">
        @method('put')


        <div class="field">
            <label class="label">FR lang</label>
            <div class="control">
                <div class="select">
                    <select id="eula-select-lang">
                        <option value="null" @selected(app()->getLocale() == '')>--SELECT EULA--</option>
                    @foreach($eulas as $eula)
                        <option value="{{ $eula->lang }}">{{ $eula->lang }}</option>
                    @endforeach
                    @if(session('error'))

                    @endif
                    </select>
                </div>
            </div>
        </div>
        <div class="field">
            <div class="control">
                <label class="label">FR Contenu EULA</label>
                <div style="max-width:75%">
                    <textarea id="eula-editor" name="eula-content" form="eula-form"  class="is-hidden">
                    </textarea>
                </div>
            </div>
        </div>           
            
        <div class="field">
            <div class="control"> 
                <button class="button is-primary">{{ Str::ucfirst(__('varcave.general.save')) }}</button>
            </div>
        </div>
        <input id="eula-id" type="hidden" name="eula-id" value="@old(session('eula-id'))"/>

    </form>

    @foreach($eulas as $eula)
        <template id="eula-data-{{ $eula->lang }}" data-eula-id="{{ $eula->id }}">
            {!! $eula->content !!}
        </template>
    @endforeach

</section>

@include('varcave.template.footer')


