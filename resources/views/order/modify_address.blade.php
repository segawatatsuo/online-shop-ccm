{{--}}
@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ asset('css/change_of_delivery_address.css') }}">
@endpush
--}}


@extends('layouts.app')

@section('title', 'トップページ')

@push('styles')
    {{-- _responsive.cssは本当は共通CSSだがtop-page.cssの後に読み込まないと崩れるため --}}
    <link rel="stylesheet" href="{{ asset('css/change_of_delivery_address.css') }}">
    <link rel="stylesheet" href="{{ asset('css/_responsive.css') }}">
@endpush



@section('content')
    <main class="main">
        <div class="form-container">

            @if ($type == 'delivery')
                <h1 class="form-title">お届け先の変更</h1>
            @else
                <h1 class="form-title">ご注文者の変更</h1>
            @endif

            <form id="my-form" action="" method="POST">
                @csrf
                <div class="form-section">
                    <h2 class="form-section-title">基本情報</h2>

                    <div class="form-group">
                        <label for="postalCode" class="form-label">郵便番号</label>
                        <input type="text" id="delivery_zip" name="delivery_zip" class="form-input"
                            placeholder="例: 123-4567"
                            value="{{ old('delivery_zip', Auth::user()->corporateCustomer->delivery_zip ?? '') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="name" class="form-label">会社名</label>
                        <input type="text" id="delivery_company_name" name="delivery_company_name" class="form-input"
                            placeholder="例: 山田 太郎"
                            value="{{ old('delivery_company_name', Auth::user()->corporateCustomer->delivery_company_name ?? '') }}"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="address" class="form-label">住所</label>
                        <input type="text" id="address" name="address" class="form-input"
                            placeholder="例: 東京都世田谷区桜新町1-1-1 〇〇マンション101" value="東京都世田谷区桜新町1-1-1 〇〇マンション101" required>
                    </div>

                    <div class="form-group">
                        <label for="phoneNumber" class="form-label">電話番号</label>
                        <input type="tel" id="phoneNumber" name="phoneNumber" class="form-input"
                            placeholder="例: 090-1234-5678" value="090-1234-5678" required>
                    </div>
                </div>

                <div class="form-actions">
                    {{-- 保存ボタン --}}
                    <a href="#" class="a-button"
                        onclick="event.preventDefault(); document.getElementById('my-form').submit();">
                        変更を保存
                    </a>

                    {{-- キャンセルボタン --}}
                    <a href="{{ url()->previous() }}" class="b-button">
                        キャンセル
                    </a>
                </div>
            </form>
        </div>
    </main>
@endsection
