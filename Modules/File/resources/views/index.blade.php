@extends('file::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('file.name') !!}</p>
@endsection
