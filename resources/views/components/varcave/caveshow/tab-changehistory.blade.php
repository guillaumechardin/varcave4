@props([
    'changeHistory',
])


<div class="box">
    <p class="title is-5">{{ Str::ucfirst( __('varcave.caveshow.change_history')) }}&nbsp;: </p>

    Option 1
    <div class="test-cards">
        @foreach($changeHistory as $change)
            <div class="card">
                <header class="card-header">
                    
                    <p class="card-header-title">
                        <span class="icon is-small mr-2">
                            <i class="bi bi-calendar3" aria-hidden="true"></i>
                        </span>
                        {{ $change->created_at->format('Y-m-d H:i:s') }}
                    </p>
                </header>
                <div class="card-content">
                    <div class="content">
                        {{$change->modification_note }}
                    </div>
                </div>
                <footer class="card-footer">
                    <div class="m-3">{{ Str::ucfirst(__('varcave.caveshow.edited_by')) . ' '.   $change->author }}</div>
                </footer>
            </div>
        @endforeach
    </div>
    <hr>
    
    Option 2
    <div class="timeline is-small">
        @foreach($changeHistory as $change)
        <div class="timeline-item">
            <div class="timeline-marker is-info"></div>
            <div class="timeline-content">
                <p class="heading">{{ $change->created_at->format('Y-m-d') }}</p>
                <p>{{ $change->modification_note }}</p>
            </div>
        </div>
        @endforeach
    </div>
    <hr>

    Option 3
    <ul class="pl-2">
        @foreach($changeHistory as $change)
        <li  type="disc">
                {{ $change->created_at }}   {{ $change->modification_note }}
        </li>
        @endforeach
    </ul>
    <hr>

    Option 4
    @foreach($changeHistory as $change)
        <div class='block'>
            <span class="icon">
                <i class="bi bi-info-square"></i>
            </span>
            {{ $change->created_at }}   {{ $change->modification_note }} 
        </div>
    @endforeach
    
</div>
