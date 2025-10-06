@extends('layouts.app')

@section('title', 'Patient Vital Signs')

@section('content')
<div class="relative min-h-screen bg-white overflow-hidden pt-[120px]">
    <img 
        src="{{ asset('img/bg-design-right.png') }}" 
        alt="Top right design"
        class="absolute top-[120px] right-0 w-[320px] object-contain opacity-90 select-none pointer-events-none"
    >

    <img 
        src="{{ asset('img/bg-design-left.png') }}" 
        alt="Bottom left design"
        class="absolute bottom-0 left-0 w-[320px] object-contain opacity-90 select-none pointer-events-none"
    >

    
</div>


@endsection
