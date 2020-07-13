@extends('layouts.app')

@section('content')

    <div>
        <x-site-rating :rating="0"/>
    </div>
    <div>
        <x-site-rating :rating="0.5"/>
    </div>
    <div>
        <x-site-rating :rating="1"/>
    </div>
    <div>
        <x-site-rating :rating="1.5"/>
    </div>
    <div>
        <x-site-rating :rating="2"/>
    </div>
    <div>
        <x-site-rating :rating="2.5"/>
    </div>
    <div>
        <x-site-rating :rating="3"/>
    </div>
    <div>
        <x-site-rating :rating="3.5"/>
    </div>
    <div>
        <x-site-rating :rating="4"/>
    </div>
    <div>
        <x-site-rating :rating="4.5"/>
    </div>
    <div>
        <x-site-rating :rating="5"/>
    </div>

@endsection
