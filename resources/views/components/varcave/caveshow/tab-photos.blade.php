@props([
    'photos',
])

<div class="fixed-grid has-4-cols has-2-cols-mobile">
    <div class="grid">
        @foreach ($photos as  $photo)
            <div class="cell">
                <div class="box">
                    <a href="{{ asset('storage/'.$photo['file_path']) }}" class="glightbox" data-glightbox="gallery-photos">
                        <img src="{{ asset('storage/'.$photo['file_path']) }}" />
                    </a>
                    <p>{{ $photo['file_note'] }}</p>
                </div>
            </div>
            @endforeach
    </div>
    <script>
        const lightbox2 = GLightbox({
            selector: '.glightbox',
            touchNavigation: true,
            loop: true,
            zoomable: true
        });
    </script>
    
</div>
