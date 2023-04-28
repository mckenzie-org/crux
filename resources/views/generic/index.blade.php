@extends('crux::layouts.app')

@section('head')
    <title>{{ ucfirst($model??"") }} | Dashboard</title>
    <meta name="description" content="">
    <meta name="keywords" content="">

    <meta property="og:type" content="website" />
    <meta property="og:description" content="">
    <meta property="og:image" content="">
    <meta property="og:url" content="">
    <meta property="og:title" content="">
    <link rel="canonical" href="" />
@endsection

@section('scripts')

@endsection

@section('content')
    <crux-dashboard>
        <crux-list :model="'{{$model??''}}'" defined_by="{{$definition??null}}" :action="'list'"></crux-list>
    </crux-dashboard>
@endsection

