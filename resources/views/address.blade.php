@extends('layouts.app')

@section('title', 'トップページ')



@push('styles')

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sameAsOrdererCheckbox = document.getElementById('same_as_orderer');
            const deliverySection = document.getElementById('delivery_section');

            if (sameAsOrdererCheckbox && deliverySection) {
                sameAsOrdererCheckbox.addEventListener('change', () => {
                    deliverySection.style.display = sameAsOrdererCheckbox.checked ? 'none' : 'block';
                });

                // 初期状態の表示調整
                deliverySection.style.display = sameAsOrdererCheckbox.checked ? 'none' : 'block';
            }
        });
    </script>

    {{-- _responsive.cssは本当は共通CSSだがtop-page.cssの後に読み込まないと崩れるため --}}
    <link rel="stylesheet" href="{{ asset('css/address-page.css') }}">
    <link rel="stylesheet" href="{{ asset('css/_responsive.css') }}">
@endpush

@section('content')
    <main class="container">
        <form method="POST" action="https://shop.ccmedico.com/order/confirm" class="post-content container">
            <input type="hidden" name="_token" value="4XthwELzA1TLmYf2NT77AIeUBiv3n32JxSg44FvVy1PTd">
            <main class="main">
                <h1>注文者情報入力</h1>
                <div class="h-adr">
                    <span class="p-country-name" style="display:none;">Japan</span>
                    <dl class="post-table flex-between">
                        <dt>姓</dt>
                        <dd><input type="text" name="order_sei" class="form-control" placeholder="姓" value="" /></dd>
                    </dl>
                    <dl class="post-table flex-between">
                        <dt>名</dt>
                        <dd><input type="text" name="order_mei" class="form-control" placeholder="名" value="" /></dd>
                    </dl>
                    <dl class="post-table flex-between">
                        <dt>電話番号</dt>
                        <dd><input type="text" name="order_phone" class="form-control" placeholder="090-999-0000"
                                value="" /></dd>
                    </dl>
                    <dl class="post-table flex-between">
                        <dt>メールアドレス</dt>
                        <dd><input type="text" name="order_email" class="form-control" placeholder="example@mail.com"
                                value="" /></dd>
                    </dl>

                    <dl class="post-table flex-between">
                        <dt>郵便番号</dt>
                        <dd>
                            <input type="text" name="order_zip" class="p-postal-code form-control input-half"
                                placeholder="123-4567" value="" />
                            <a href="https://www.post.japanpost.jp/zipcode/" class="btn-01 small"
                                target="_blank">郵便番号検索</a>
                        </dd>
                    </dl>
                    <dl class="post-table flex-between">
                        <dt>住所（都道府県）</dt>
                        <dd><input type="text" name="order_add01" class="p-region form-control" placeholder="○○県"
                                value="" /></dd>
                    </dl>
                    <dl class="post-table flex-between">
                        <dt>住所（市区町村）</dt>
                        <dd><input type="text" name="order_add02" class="p-locality p-street-address form-control"
                                placeholder="△△市□□町" value="" /></dd>
                    </dl>
                    <dl class="post-table flex-between">
                        <dt>市区町村以降の住所</dt>
                        <dd><input type="text" name="order_add03" class="p-extended-address form-control"
                                placeholder="マンション名など" value="" /></dd>
                    </dl>



                    <dl class="post-table flex-between">
                        <dt>お届け希望日</dt>
                        <dd><input type="date" name="delivery_date" class="p-extended-address form-control"
                                placeholder="本日より3営業日以降になります(土日祝を除く)" value="" /></dd>
                    </dl>

                    <dl class="post-table flex-between">
                        <dt>お届け時間帯</dt>
                        <dd>
                            <select class="form-select" id="delivery_time" name="delivery_time">
                                <option value="なし">
                                    なし
                                </option>
                                <option value="午前中（8:00～12:00）">
                                    午前中（8:00～12:00）
                                </option>
                                <option value="14:00～16:00">
                                    14:00～16:00
                                </option>
                                <option value="16:00～18:00">
                                    16:00～18:00
                                </option>
                                <option value="18:00～20:00">
                                    18:00～20:00
                                </option>
                                <option value="19:00～21:00">
                                    19:00～21:00
                                </option>
                            </select>
                        </dd>
                    </dl>


                    <dl class="post-table flex-between">
                        <dt>ご要望欄</dt>
                        <dd><input type="text" name="your_request" class="p-extended-address form-control"
                                placeholder="" value="" /></dd>
                    </dl>


                </div>
                <dl class="post-table flex-between same-address-block"
                    style="padding: 1rem; background-color: #f0f8ff; border: 2px solid #007bff; border-radius: 8px; margin: 20px auto;">
                    <dt style="font-weight: bold; font-size: 1.1em;">
                        お届け先は注文者と同じですか？
                    </dt>
                    <dd>
                        <label style="font-size: 1.1em;">
                            <input type="hidden" name="same_as_orderer" value="0"><input type="checkbox"
                                id="same_as_orderer" name="same_as_orderer" value="1" checked>はい（チェックを外すと別の住所を入力できます）
                        </label>
                    </dd>
                </dl>


                <div id="delivery_section">
                    <h1>お届け先入力</h1>
                    <div class="h-adr">
                        <span class="p-country-name" style="display:none;">Japan</span>
                        <dl class="post-table flex-between">
                            <dt>姓</dt>
                            <dd><input type="text" name="delivery_sei" class="form-control" placeholder="姓" value="" />
                            </dd>
                        </dl>
                        <dl class="post-table flex-between">
                            <dt>名</dt>
                            <dd><input type="text" name="delivery_mei" class="form-control" placeholder="名" value="" />
                            </dd>
                        </dl>
                        <dl class="post-table flex-between">
                            <dt>電話番号</dt>
                            <dd><input type="text" name="delivery_phone" class="form-control" placeholder="090-999-0000"
                                    value="" /></dd>
                        </dl>
                        <dl class="post-table flex-between">
                            <dt>メールアドレス</dt>
                            <dd><input type="text" name="delivery_email" class="form-control"
                                    placeholder="mail@example.com" value="" /></dd>
                        </dl>
                        <dl class="post-table flex-between">
                            <dt>郵便番号</dt>
                            <dd>
                                <input type="text" name="delivery_zip" class="p-postal-code form-control input-half"
                                    placeholder="123-4567" value="" />
                                <a href="https://www.post.japanpost.jp/zipcode/" class="btn-01 small"
                                    target="_blank">郵便番号検索</a>
                            </dd>
                        </dl>
                        <dl class="post-table flex-between">
                            <dt>住所（都道府県）</dt>
                            <dd><input type="text" name="delivery_add01" class="p-region form-control" placeholder="○○県"
                                    value="" /></dd>
                        </dl>
                        <dl class="post-table flex-between">
                            <dt>住所（市区町村）</dt>
                            <dd><input type="text" name="delivery_add02"
                                    class="p-locality p-street-address form-control" placeholder="△△市□□町" value="" />
                            </dd>
                        </dl>
                        <dl class="post-table flex-between">
                            <dt>市区町村以降の住所</dt>
                            <dd><input type="text" name="delivery_add03" class="p-extended-address form-control"
                                    placeholder="マンション名など" value="" /></dd>
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
    </main>

@endsection
