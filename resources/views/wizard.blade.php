@extends('adminlte::layouts.app')

@section('htmlheader_title')
    Wizard
@endsection


@section('main-content')
    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">
                <user-profile-photo></user-profile-photo>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">

                <personal-data></personal-data>


            </div>
        </div>
    </div>
@endsection
