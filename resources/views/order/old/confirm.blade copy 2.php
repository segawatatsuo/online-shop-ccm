@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/yubinbango.css') }}">
@endpush

@push('scripts')
    <script src="https://yubinbango.github.io/yubinbango/yubinbango.js" charset="UTF-8"></script>
@endpush

@section('content')



    <form method="POST" action="{{ route('order.complete') }}" class="h-adr post-content">
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



            <h1>お届け先入力</h1>

            <dl class="post-table flex-between">
                <dt>姓</dt>
                <dd><input type="text" name="sei" class="form-control" placeholder="姓" value="" />
                </dd>
            </dl>

            <dl class="post-table flex-between">
                <dt>名</dt>
                <dd><input type="text" name="mei" class="form-control" placeholder="名" value="" />
                </dd>
            </dl>

            <dl class="post-table flex-between">
                <dt>電話番号</dt>
                <dd><input type="text" name="phone" class="form-control" placeholder="090-999-0000" value="" />
                </dd>
            </dl>

            <dl class="post-table flex-between">
                <dt>メールアドレス</dt>
                <dd><input type="text" name="email" class="form-control" placeholder="yamada@example.com"
                        value="" />
                </dd>
            </dl>

            <dl class="post-table flex-between">

                <dt>郵便番号</dt>
                <dd>
                    <input type="text" name="input_zip" class="p-postal-code form-control input-half"
                        placeholder="123-4567" value="" /> <a href="https://www.post.japanpost.jp/zipcode/"
                        class="btn-01 small" target="_blank">郵便番号検索</a>
                </dd>


            </dl>
            <dl class="post-table flex-between">
                <dt>住所（都道府県）</dt>
                <dd><input type="text" name="input_add01" class="p-region form-control" placeholder="○○県"
                        value="" />
                </dd>
            </dl>
            <dl class="post-table flex-between">
                <dt>住所（市区町村）</dt>
                <dd><input type="text" name="input_add02" class="p-locality p-street-address form-control"
                        placeholder="△△市□□町" value="" /></dd>
            </dl>



            <dl class="post-table flex-between">
                <dt>市区町村以降の住所</dt>
                <dd><input type="text" name="input_add03" class="p-extended-address form-control"
                        placeholder="1丁目☆☆マンション101号室" value="" /></dd>
            </dl>

            <input type="hidden" class="p-country-name" value="Japan">


            <div style="margin-top: 20px;background-color:#ffffff;">
                <h1>ご注文内容</h1>
                <ul>
                    @foreach ($cart as $item)
                        <li>{{ $item['name'] }} x
                            {{ $item['quantity'] }}：¥{{ number_format($item['price'] * $item['quantity']) }}
                        </li>
                    @endforeach
                </ul>

                <button type="submit" class="btn btn-primary">注文を確定</button>
            </div>



        </main>

    </form>



@endsection
