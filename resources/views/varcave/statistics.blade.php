@include('varcave.template.header')
@include('varcave.template.navbar')

<section class="section">
    <div class="container">
      <h1 class="title is-2">
        {{ __('varcave.statistics.pageTitle') }}
      </h1>
    </div>
    
</section>
<table class="table ml-6">
    <thead>
        <tr>
            <th><abbr title="{{ __('varcave.statistics.position') }}">{{ __('varcave.statistics.position') }}</abbr></th>
            <th><abbr title="{{ __('varcave.statistics.name') }}">{{ __('varcave.statistics.name') }}</abbr></th>
            <th><abbr title="{{ __('varcave.statistics.views') }}">{{ __('varcave.statistics.views') }}</abbr></th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th><abbr title="{{ __('varcave.statistics.position') }}">{{ __('varcave.statistics.position') }}</abbr></th>
            <th><abbr title="{{ __('varcave.statistics.name') }}">{{ __('varcave.statistics.name') }}</abbr></th>
            <th><abbr title="{{ __('varcave.statistics.views') }}">{{ __('varcave.statistics.views') }}</abbr></th>
        </tr>
    </tfoot>
    <tbody>
        @foreach($statistics as $stat)
            <tr>
                <th>{{ $loop->iteration }}</th>
                <td>
                    <a
                        href="{{ route('varcave.caves.show', ['uuid' => $stat->uuid]) }}"
                        title="{{ $stat->name }}"
                    >
                        {{ $stat->name }}
                    </a>
                </td>
                <td>{{ $stat->total_views }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@include('varcave.template.footer')
