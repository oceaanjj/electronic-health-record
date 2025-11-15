@extends('layouts.app')

@section('title', 'Doctor Home')

@section('content')

    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        LOG OUT
    </a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <h1>DOCTOR HOME</h1>
</body>

</html>