@extends('adminlte::layouts.app')

@section('htmlheader_title')
Test component user-profile-photo
@endsection

@section('main-content')
<div class="container-fluid spark-screen">
    @if( isset($case))
        @switch($case)
            @case('with-user-id')
                <h3>Case with user_id: {{ $user_id }}</h3>
                <person-profile-photo :id="{{ $user_id }}"></person-profile-photo>
                @break
            @case('female')
                <h3>Case female:</h3>
                <person-profile-photo gender="female"></person-profile-photo>
                @break
            @default
                <h3>Default case (case: {{ $case }}): no props passed to component</h3>
                <person-profile-photo></person-profile-photo>
        @endswitch
    @else
        <h3>Default case 2: no props passed to component</h3>
        <person-profile-photo></person-profile-photo>
    @endif

</div>
@endsection