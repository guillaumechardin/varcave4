@props([
    'rescueFiles',
])
<h1>{{ __('varcave.caveshow.rescue_documents') }}</h1>
<div class="gallery grid">
    @foreach ($rescueFiles as $key => $doc)
        <div class="cell">
            <div>
                <a href="{{ asset('storage/'. $doc['file_path']) }}" class="filedoc"
                    style="display:flex;align-items:center;justify-content:center;height:auto;">
                <i class="bi bi-file-earmark-pdf" style="font-size:4rem;"></i>
                </a>
            </div>
            <div class="is-size-7 has-text-centered">{{ basename($doc['file_path']) }}</div> 
        </div> 
    @endforeach
</div>