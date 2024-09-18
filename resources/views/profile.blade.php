@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Profile</h1>
    <p><strong>Name:</strong> {{ $user->first_name }} {{ $user->last_name }}</p>
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <!-- Add more profile details as needed -->
</div>
@endsection
