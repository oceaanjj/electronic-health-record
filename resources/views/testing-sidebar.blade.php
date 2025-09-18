<!-- connects the app layout (para magamit yung yield and section) -->
@extends('layouts.app')

@section('title', 'Home')


<!-- child nung yield('content') sa layouts/app.blade.php -->
@section('content')
  <h1 class="text-2xl font-bold">Welcome to EHR</h1>
  <p>This is the home page.</p>
@endsection
