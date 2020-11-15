@php
    $column['text'] = Illuminate\Mail\Markdown::parse($entry->{$column['name']} ?? '');
    $column['escaped'] = $column['escaped'] ?? false;
    if (!$column['escaped'] && config('settings.column_limit') && $column['text']) {
        $column['text'] = Str::limit($column['text'], config('settings.column_limit'), '[...]');
    }
@endphp

@includeWhen(!empty($column['wrapper']), 'crud::columns.inc.wrapper_start')
    @if($column['escaped'])
        {{ $column['text'] }}
    @else
        {!! $column['text'] !!}
    @endif
@includeWhen(!empty($column['wrapper']), 'crud::columns.inc.wrapper_end')
