@extends('layouts.app')

@section('content')


    <main class="main">
        <div class="line-up">
            <a href="{{ asset('product/wax') }}"><img src="{{ asset('images/shop-top/bikyaku.jpg') }}" alt="" class="pic"></a>
            <a href="{{ asset('product/airstocking') }}"><img src="{{ asset('images/shop-top/daimond.jpg') }}" alt="" class="pic"></a>
            <a href="{{ asset('product/gelnail') }}"><img src="{{ asset('images/shop-top/3in1Lineup1560-600.jpg') }}" alt="" class="pic"></a>
        </div>


    </main>

@endsection