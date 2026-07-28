<div class="gpx-build-wrapper">
    
    <progress class="progress is-primary is-hidden" id="progress-build-gpx" value=""></progress>
    <div class="buttons">
        <button id="start-build-gpx" class="button is-link">{{ __('varcave.resources.start_build_gpx_file') }}</button>
    </div>
    <div id="estimate_time" class="">
        @php
            $seconds = $countAllCaves * 0.08;
            $minutes = floor($seconds / 60);
            $remainingSeconds = round($seconds % 60);
        @endphp
        Temps estimé : {{ $minutes }} min {{ $remainingSeconds }} s
    </div>
</div>
