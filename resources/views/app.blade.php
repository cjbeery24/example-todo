<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Example Todo App</title>

    <link href="{{ mix('css/app.css') }}" rel="stylesheet">

    <script>
        window.appEnv = window.appEnv || {};
        window.appEnv.baseUrl = "{!! config('app.url') !!}";
    </script>
</head>
<body>
    <div class="flex-center position-ref full-height">
        <div id="todoApp">
            <router-view></router-view>
        </div>
    </div>

    <script src="{{ mix('/js/app.js') }}"></script>
</body>
</html>
