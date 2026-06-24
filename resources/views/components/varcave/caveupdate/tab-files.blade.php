@props([
    'caveFiles',
    'caveFileList',
])

<div class="box">
    @foreach($caveFileList as $li)
        <div class="fixed-grid has-4-cols">
            @php
                $currFiles = str_replace('varcave.cave_files.', '', $li->i18n_key);
            @endphp
            @if(isset($caveFiles[$currFiles]))
                <p class="title is-5"> {{ Str::ucfirst(__($li->i18n_key)) }}</p>
                <div class="grid">
                    
                
                    
                    @foreach($caveFiles[$currFiles] as $f)
                    {{-- dd($f) --}}
                        <div class="cell">

                            <div class="card">
                                <div class="card-image">
                                    <figure class="image is-4by3">
                                        <img src="{{ asset('storage/'.$f['file_path']) }}" />
                                    </figure>
                                </div>
                                <div class="card-content">
                                    {{-- 
                                    <div class="media">
                                        <div class="media-left">
                                            <figure class="image is-48x48">
                                            <img
                                                src="https://bulma.io/assets/images/placeholders/96x96.png"
                                                alt="Placeholder image"
                                            />
                                            </figure>
                                        </div>
                                        <div class="media-content">
                                            <p class="title is-4">John Smith</p>
                                            <p class="subtitle is-6">@johnsmith</p>
                                        </div>
                                    </div>
                                     --}}

                                    <div class="content">
                                        <input class="file-note" data-file-id="{{ $f['file_id'] }}" style="width:100%" value="{{ $f['file_note'] }}"/>
                                    <br />
                                    {{-- <timedatetime="">{{ $f['created_at']?:'???' }} </time>--}}
                                    </div>

                                     <footer class="card-footer">
                                        <a href="#" class="card-footer-item">FR enregistrer</a>
                                        
                                        <a href="#" class="card-footer-item">FR supprimer</a>
                                    </footer>
                                </div>
                            </div>








                            @if($f['is_img'])
                                
                            @else
                                <div >
                                    <a href="{{ asset('storage/'.$f['file_path']) }}"> {{$f['file_note'] }}</a>
                                </div>
                            @endif
                            <div class="file-action">
                                <span>
                            </div>
                        </div>
                    @endforeach
                    
                @endif
            </div>
        </div>
    @endforeach
</div>
{{-- 
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
 --}}