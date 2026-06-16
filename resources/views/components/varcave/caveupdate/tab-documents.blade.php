@props([
    'caveDocsPhotos',
    'caveDocsFiles',
])

<div class="gallery grid">
    @foreach ($caveDocsFiles as $key => $doc)
       @if($doc['is_img'] )
            <div class="cell">
                <a href="{{ asset('storage/'. $doc['file_path']) }}" class="glightbox" data-glightbox="gallery2">
                    <img src="{{ asset('storage/'.$doc['file_path']) }}" />
                </a>
            </div>
        @else
         <div class="cell">
            <div>
                <a href="{{ asset('storage/'. $doc['file_path']) }}" class="filedoc"
                    style="display:flex;align-items:center;justify-content:center;height:auto;">
                <i class="bi bi-file-earmark-pdf" style="font-size:4rem;"></i>
                </a>
            </div>
            <div class="is-size-7 has-text-centered">{{ basename($doc['file_path']) }}</div> 
        </div> 
        @endif
    @endforeach
</div>
<script>
    const lightbox_docs = GLightbox({
        selector: '.glightbox',
        touchNavigation: true,
        loop: true,
        zoomable: true
    });
</script>