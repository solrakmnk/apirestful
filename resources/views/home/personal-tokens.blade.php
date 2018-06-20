@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            My Tokens
            <passport-personal-access-tokens></passport-personal-access-tokens>
            Authorized Clients
            <passport-authorized-client></passport-authorized-client>
            My Clients
            <passport-client></passport-client>
        </div>
    </div>
@endsection
