@extends('layout')

@section('stylesheets')
    {!! Minify::stylesheet([
        '/vendor/semantic/2.1.4/components/form.css',
        '/vendor/semantic/2.1.4/components/divider.css',
        '/vendor/semantic/2.1.4/components/label.css', ])->withFullUrl() !!}

    @parent
@stop

@section('main')
    <div class="ui page grid">
        <div class="eight wide column">
            <h2 class="ui header">
                Files ({{ number_format($count) }})
            </h2>
        </div>
        <div class="right aligned eight wide column">
            <div class="ui large header">{{ $size }}</div>
        </div>

        <div class="sixteen wide column">

            <form method="get" action="{{ Request::url() }}" class="ui form">
                <div class="fields">
                    <div class="four wide field">
                        <label>Search</label>
                        <input type="text" name="filters[search]" placeholder="Search" value="{{ $filters->search }}">
                    </div>
                    @if($filters->has('ip'))
                        <div class="field">
                            <input type="hidden" name="filters[ip]" value="{{ $filters->ip }}">

                            <label>IP</label>
                            <div class="ui big label">
                                {{ $filters->ip }}
                                <i class="delete icon"><a href="{{ $filters->url([ ], [ 'ip' ]) }}"></a></i>
                            </div>
                        </div>
                    @endif
                </div>

                <button class="ui primary button">Search</button>
                <a class="ui button" href="{{ Request::url() }}">Reset</a>
            </form>

            <div class="ui divider"></div>

            <table class="ui sortable celled table">
                <thead>
                    <tr>
                        <th>Original</th>
                        <th>File</th>
                        <th>Uploaded</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($files as $file)
                        <tr>
                            <td>
                                {{ $file->client_name }}
                            </td>
                            <td>
                                <a href="{{ $file->url() }}" target="_blank">{{ $file->generated_name }}</a>
                            </td>
                            <td>
                                {{ $file->created_at }}
                            </td>
                            <td>
                                <a href="{{ route('admin', [ 'filters' => [ 'ip' => $file->uploaded_by_ip ] ]) }}">{{ $file->uploaded_by_ip }}</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {!! $files->render() !!}
        </div>
    </div>
@stop