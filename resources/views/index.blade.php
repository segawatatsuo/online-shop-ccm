@extends('layouts.app')

@section('head')
<style>
    .line-up {
    display: flex;
    flex-direction: column;     /* 縦に並べる */
    align-items: center;        /* 中央揃え */
    max-width: 1200px;
    margin: 75px auto;
    padding: 0 20px;
    gap: 30px;                  /* 画像の間隔 */
}

.line-up a {
    display: block;
    text-align: center;
}

.line-up img.pic {
    max-width: 100%;
    height: auto;
    display: block;
}
</style>
@endsection

@section('content')


    <main class="main">
        <div class="line-up">
            <a href="{{ asset('product/wax') }}"><img src="{{ asset('images/shop-top/bikyaku.jpg') }}" alt="" class="pic"></a>
            <a href="{{ asset('product/airstocking') }}"><img src="{{ asset('images/shop-top/daimond.jpg') }}" alt="" class="pic"></a>
            <a href="{{ asset('product/gelnail') }}"><img src="{{ asset('images/shop-top/3in1Lineup1560-600.jpg') }}" alt="" class="pic"></a>
        </div>


    </main>

@endsection