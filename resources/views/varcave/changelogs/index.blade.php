@include('varcave.template.header')
@include('varcave.template.navbar')

<section class="section">
    <section class="hero">
        <div class="hero-body">
            <p class="title">{{ __('varcave.change_logs.title', ['nbr' => $nbr])}}</p>
        </div>
    </section>

    <div id="changes-wrapper" class="box">
            @foreach($chgLogs as $change)
                <div class='block'>
                    <span class="icon">
                        <i class="bi bi-info-square"></i>
                    </span>
                    <a href="{{ route('varcave.caves.show', ['uuid' => $change->cave->uuid] ) }}" target="_blank">
                        <span class="modification_content ml-3">
                            {{ \Carbon\Carbon::parse($change->created_at)->format('d/m/Y') }} » {{ $change->cave->name }} » {{ $change->modification_note }}
                        </span>
                    </a>
                </div>
            @endforeach
    </div>
    
</section>

@include('varcave.template.footer')