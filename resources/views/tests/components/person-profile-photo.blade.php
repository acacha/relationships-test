@extends('adminlte::layouts.app')

@section('htmlheader_title')
Test component person-profile-photo
@endsection

@section('main-content')
<div class="container-fluid spark-screen">
    @if( isset($case))
        @switch($case)
            @case('with-user-id')
                <h3>Case with person_id: {{ $person_id }}</h3>
                <person-profile-photo :id="{{ $person_id }}"></person-profile-photo>
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

    <ul>
        <li>User id: @php echo Auth::user()->id@endphp</li>
    </ul>

    <h1>User</h1>
    <p>
        @php echo json_encode(Auth::user()); @endphp
    </p>

    <h1>Person</h1>
    <p>
        @php echo json_encode(Auth::user()->person); @endphp
    </p>

    <h1>Person Photos</h1>
    <p>
        @php
            if (Auth::user()->person) {
            echo json_encode(Auth::user()->person->photos);
            }
        @endphp
    </p>

</div>
@endsection