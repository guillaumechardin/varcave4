<h1 class="title is-3">
    {{ __('varcave.spatial_search.title_resutls')  }}
</h1>
<table id="results-table" class="table is-fullwidth is-striped is-hoverable">
    <thead>
        <tr class="is-info">
            
            @foreach($datatablesFields as $key => $col)
                @continue($key === 'uuid')
                <th class="">{{ $col['i18n_label'] }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody></tbody>
    <tfoot>
        <tr class="is-info">
            @foreach($datatablesFields as $key => $col)
                @continue($key === 'uuid')
                <th class="">{{ $col['i18n_label'] }}</th>
            @endforeach
        </tr>
    </tfoot>
</table>