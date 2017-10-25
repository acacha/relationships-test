@extends('adminlte::layouts.app')

@section('htmlheader_title')
Test component personal-data-form
@endsection

@section('main-content')
<div class="container-fluid spark-screen">

    <identifier-input></identifier-input>

    <identifier-select selected="NIF"></identifier-select>

    @php
    use Acacha\Relationships\Models\Identifier;
    var_dump(Identifier::all()->toJson())
    @endphp
</div>
@endsection