<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

        <title>Madokami.com &middot; Kawaii File Hosting</title>

        <link href="{{ asset('favicon.ico') }}" rel="shortcut icon" type="image/x-icon">

        @section('stylesheets')
            <link href="{{ asset('/vendor/semantic/2.1.4/components/icon.min.css') }}" rel="stylesheet" type="text/css">

            {!! Minify::stylesheet([
                '/vendor/semantic/2.1.4/components/site.css',
                '/vendor/semantic/2.1.4/components/reset.css',
                '/vendor/semantic/2.1.4/components/grid.css',
                '/vendor/semantic/2.1.4/components/header.css',
                '/vendor/semantic/2.1.4/components/message.css',
                '/vendor/semantic/2.1.4/components/button.css',
                '/vendor/semantic/2.1.4/components/table.css',
                '/vendor/semantic/2.1.4/components/progress.css',
                '/css/madokami.css', ])->withFullUrl() !!}
        @show
    </head>
    <body ng-app="app">
        @yield('main')

        {!! Minify::javascript([
            '/vendor/angular/1.4.6/angular.js',
            '/vendor/ng-file-upload/5.0.9/ng-file-upload.js',
            '/js/madokami.js', ])->withFullUrl() !!}

        {{-- @include('partials.analytics') --}}
    </body>
</html>