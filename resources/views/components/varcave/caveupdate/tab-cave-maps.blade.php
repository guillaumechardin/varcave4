 @props([
    'caveMaps',
 ])
 <div class="gallery grid">
    @foreach ($caveMaps as  $map)
        <div class="cell">
            <a href="{{ asset('storage/'.$map['file_path']) }}" class="glightbox" data-glightbox="gallery1">
                <img src="{{ asset('storage/'.$map['file_path']) }}" />
            </a>
        </div>    
    @endforeach
</div>
<script>
    const lightbox = GLightbox({
        selector: '.glightbox',
        touchNavigation: true,
        loop: true,
        zoomable: true
    });
</script>