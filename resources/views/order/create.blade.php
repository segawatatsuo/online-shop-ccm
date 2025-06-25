@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/address_input.css') }}">
@endpush



@push('scripts')
    <script src="https://yubinbango.github.io/yubinbango/yubinbango.js" charset="UTF-8"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleCheckbox = document.getElementById('same_as_orderer');
            const deliverySection = document.getElementById('delivery_section');

            toggleCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    deliverySection.style.display = 'none';
                } else {
                    deliverySection.style.display = 'block';
                }
            });

            // 初期状態
            if (toggleCheckbox.checked) {
                deliverySection.style.display = 'none';
            }
        });
    </script>
@endpush

@section('content')
    <form method="POST" action="{{ route('order.confirm') }}" class="post-content">
        @csrf

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <main class="main">
            <h1>注文者情報入力</h1>

            <div class="h-adr">
                <span class="p-country-name" style="display:none;">Japan</span>
                <dl class="post-table flex-between">
                    <dt>姓</dt>
                    <dd><input type="text" name="order_sei" class="form-control" placeholder="姓"
                            value="{{ old('order_sei') }}" /></dd>
                </dl>
                <dl class="post-table flex-between">
                    <dt>名</dt>
                    <dd><input type="text" name="order_mei" class="form-control" placeholder="名"
                            value="{{ old('order_mei') }}" /></dd>
                </dl>
                <dl class="post-table flex-between">
                    <dt>電話番号</dt>
                    <dd><input type="text" name="order_phone" class="form-control" placeholder="090-999-0000"
                            value="{{ old('order_phone') }}" /></dd>
                </dl>
                <dl class="post-table flex-between">
                    <dt>メールアドレス</dt>
                    <dd><input type="text" name="order_email" class="form-control" placeholder="example@mail.com"
                            value="{{ old('order_email') }}" /></dd>
                </dl>

                <dl class="post-table flex-between">
                    <dt>郵便番号</dt>
                    <dd>
                        <input type="text" name="order_zip" class="p-postal-code form-control input-half"
                            placeholder="123-4567" value="{{ old('order_zip') }}" />
                        <a href="https://www.post.japanpost.jp/zipcode/" class="btn-01 small" target="_blank">郵便番号検索</a>
                    </dd>
                </dl>
                <dl class="post-table flex-between">
                    <dt>住所（都道府県）</dt>
                    <dd><input type="text" name="order_add01" class="p-region form-control" placeholder="○○県"
                            value="{{ old('order_add01') }}" /></dd>
                </dl>
                <dl class="post-table flex-between">
                    <dt>住所（市区町村）</dt>
                    <dd><input type="text" name="order_add02" class="p-locality p-street-address form-control"
                            placeholder="△△市□□町" value="{{ old('order_add02') }}" /></dd>
                </dl>
                <dl class="post-table flex-between">
                    <dt>市区町村以降の住所</dt>
                    <dd><input type="text" name="order_add03" class="p-extended-address form-control"
                            placeholder="マンション名など" value="{{ old('order_add03') }}" /></dd>
                </dl>




                <dl class="post-table flex-between">
                    <dt>お届け希望日</dt>
                    <dd><input type="date" name="delivery_date" class="p-extended-address form-control"
                            placeholder="本日より3営業日以降になります(土日祝を除く)" value="{{ old('delivery_date') }}" /></dd>
                </dl>


                <dl class="post-table flex-between">
                    <dt>お届け時間帯</dt>
                    <dd>
                        <select class="form-select" id="delivery_time" name="delivery_time">
                            @foreach ($deliveryTimes as $time)
                                <option value="{{ $time }}" {{ old('delivery_time') == $time ? 'selected' : '' }}>
                                    {{ $time }}
                                </option>
                            @endforeach
                        </select>
                        @error('delivery_time')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </dd>
                </dl>



                <dl class="post-table flex-between">
                    <dt>ご要望欄</dt>
                    <dd><input type="text" name="your_request" class="p-extended-address form-control" placeholder=""
                            value="{{ old('your_request') }}" /></dd>
                </dl>



            </div>

            <dl class="post-table flex-between same-address-block"
                style="padding: 1rem; background-color: #f0f8ff; border: 2px solid #007bff; border-radius: 8px; margin: 20px auto;">
                <dt style="font-weight: bold; font-size: 1.1em;">
                    お届け先は注文者と同じですか？
                </dt>
                <dd>
                    <label style="font-size: 1.1em;">
                        <input type="hidden" name="same_as_orderer" value="0"><!-- チェックをはずすと空欄になるのでその場合は0を送る -->
                        <input type="checkbox" id="same_as_orderer" name="same_as_orderer" value="1"
                            {{ old('same_as_orderer', '1') == '1' ? 'checked' : '' }}><!-- チェックした場合はこっちで上書きされる -->
                        はい（チェックを外すと別の住所を入力できます）
                    </label>
                </dd>
            </dl>



            <div id="delivery_section">
                <h1>お届け先入力</h1>
                <div class="h-adr">
                    <span class="p-country-name" style="display:none;">Japan</span>
                    <dl class="post-table flex-between">
                        <dt>姓</dt>
                        <dd><input type="text" name="delivery_sei" class="form-control" placeholder="姓"
                                value="{{ old('delivery_sei') }}" />
                        </dd>
                    </dl>
                    <dl class="post-table flex-between">
                        <dt>名</dt>
                        <dd><input type="text" name="delivery_mei" class="form-control" placeholder="名"
                                value="{{ old('delivery_mei') }}" />
                        </dd>
                    </dl>
                    <dl class="post-table flex-between">
                        <dt>電話番号</dt>
                        <dd><input type="text" name="delivery_phone" class="form-control" placeholder="090-999-0000"
                                value="{{ old('delivery_phone') }}" /></dd>
                    </dl>
                    <dl class="post-table flex-between">
                        <dt>メールアドレス</dt>
                        <dd><input type="text" name="delivery_email" class="form-control"
                                placeholder="mail@example.com" value="{{ old('delivery_email') }}" /></dd>
                    </dl>
                    <dl class="post-table flex-between">
                        <dt>郵便番号</dt>
                        <dd>
                            <input type="text" name="delivery_zip" class="p-postal-code form-control input-half"
                                placeholder="123-4567" value="{{ old('delivery_zip') }}" />
                            <a href="https://www.post.japanpost.jp/zipcode/" class="btn-01 small"
                                target="_blank">郵便番号検索</a>
                        </dd>
                    </dl>
                    <dl class="post-table flex-between">
                        <dt>住所（都道府県）</dt>
                        <dd><input type="text" name="delivery_add01" class="p-region form-control" placeholder="○○県"
                                value="{{ old('delivery_add01') }}" /></dd>
                    </dl>
                    <dl class="post-table flex-between">
                        <dt>住所（市区町村）</dt>
                        <dd><input type="text" name="delivery_add02" class="p-locality p-street-address form-control"
                                placeholder="△△市□□町" value="{{ old('delivery_add02') }}" /></dd>
                    </dl>
                    <dl class="post-table flex-between">
                        <dt>市区町村以降の住所</dt>
                        <dd><input type="text" name="delivery_add03" class="p-extended-address form-control"
                                placeholder="マンション名など" value="{{ old('delivery_add03') }}" /></dd>
                    </dl>
                </div>
            </div>
            <input type="hidden" class="p-country-name" value="Japan">


            <div style="margin-top: 20px;background-color:#ffffff;">
                <button type="submit" class="a-button" style="border: none">
                    ご注文内容確認
                </button>
            </div>

        </main>
    </form>
@endsection
