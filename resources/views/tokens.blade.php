@extends('adminlte::layouts.app')

@section('htmlheader_title')
    Wizard
@endsection

@section('main-content')
    <div class="container-fluid spark-screen">
        <passport-clients></passport-clients>
        <passport-authorized-clients></passport-authorized-clients>
        <passport-personal-access-tokens></passport-personal-access-tokens>
    </div>
@endsection


