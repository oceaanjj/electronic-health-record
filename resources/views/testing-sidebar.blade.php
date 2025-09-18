<!-- connects the app layout (para magamit yung yield and section) 
@extends('layouts.app')

@section('title', 'Testing')


<!-- child nung yield('content') sa layouts/app.blade.php -->
@section('content')
    <div>
        <p>This is the content area.</p>
    </div>
@endsection




