<!DOCTYPE html>
<html lang="en" class="default-style bg-stone-900">
<head>
    @include('crux::common.head')
    @yield('head')
</head>
<body>
{{ csrf_field() }}
<div id="app">
    @yield('content')
</div>
@include('crux::common.scripts')
@yield('scripts')
</body>
</html>
