{{-- resources/views/admin/partials/footer.blade.php --}}
<style>
    /* hasManyの1項目（複数フォーム）全体を横並びにする */
    .has-many-items-form.fields-group {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-start;
        gap: 16px;
    }

    /* 各フィールド（form-group）を固定幅にする */
    .has-many-items-form .form-group {
        flex: none;
        width: auto;
        margin-bottom: 0;
    }

    /* ラベルの幅を固定（省スペース） */
    .has-many-items-form .form-group label.control-label {
        width: auto;
        min-width: 80px;
        white-space: nowrap;
    }

    /* 入力欄を詰める */
    .has-many-items-form .form-group .col-sm-8 {
        width: auto;
    }

    /* 全体を少し縮める */
    .has-many-items-form .form-control {
        width: 130px;
        display: inline-block;
    }

    /* input-groupの中でアイコンの間隔が詰まらないように */
    .has-many-items-form .input-group {
        width: auto;
    }
</style>

<style>
/* hasManyの1項目（複数フォーム）全体を横並びにする */
.has-many-items-form.fields-group {
    display: flex;
    flex-wrap: wrap; /* 画面幅が狭い場合に折り返す */
    justify-content: flex-start; /* 左寄せ */
    gap: 16px; /* 各フォーム間の隙間 */
    align-items: flex-start; /* 各フォームの上端を揃える */
}

/* 各フィールド（form-group）のFlexboxでの挙動を調整 */
.has-many-items-form .form-group {
    /* flex-basisで初期幅を指定し、flex-growで拡大、flex-shrinkで縮小を制御 */
    /* ここでは各項目が適切な幅になるように調整してください */
    flex: 0 0 auto; /* 拡大縮小せず、内容に応じた幅に */
    margin-bottom: 0; /* 下部のマージンをなくす */
}

/* Bootstrapのcol-sm-2とcol-sm-8のマージンやパディングをリセット */
.has-many-items-form .form-group .col-sm-2,
.has-many-items-form .form-group .col-sm-8 {
    width: auto; /* Bootstrapの固定幅を上書き */
    padding-left: 0;
    padding-right: 0;
}

/* ラベルの幅を固定（省スペース） */
.has-many-items-form .form-group label.control-label {
    width: auto; /* Bootstrapの幅を上書き */
    min-width: 80px; /* 最小幅を設定 */
    text-align: right; /* 必要であればラベルを右寄せ */
    padding-right: 10px; /* ラベルと入力欄の間に隙間 */
    white-space: nowrap; /* ラベルの折り返しを防ぐ */
}

/* 入力欄を詰める */
.has-many-items-form .form-group .input-group {
    width: auto; /* 必要に応じて調整 */
}

/* 全体を少し縮める */
.has-many-items-form .form-control {
    width: 130px; /* 入力欄の幅を固定 */
    /* display: inline-block; はFlexboxの子要素には不要な場合が多い */
}

/* input-groupの中でアイコンの間隔が詰まらないように */
.has-many-items-form .input-group-addon {
    white-space: nowrap; /* アイコンが折り返さないように */
}
</style>

<style>
/* 注文明細を横並びで表示するためのCSS */

/* hasManyフォームグループのコンテナ */
.inline-form-group .has-many-items .has-many-item {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: #f9f9f9;
}

/* 各フィールドのコンテナ */
.inline-form-group .has-many-items .has-many-item .form-group {
    flex: 1;
    margin-bottom: 0;
    margin-right: 10px;
}

/* 最後のフィールドのマージンを削除 */
.inline-form-group .has-many-items .has-many-item .form-group:last-child {
    margin-right: 0;
}

/* ラベルのスタイル調整 */
.inline-form-group .has-many-items .has-many-item .form-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
    font-size: 12px;
}

/* 入力フィールドのスタイル調整 */
.inline-form-group .has-many-items .has-many-item .form-group input {
    width: 100%;
    padding: 5px;
    border: 1px solid #ccc;
    border-radius: 3px;
}

/* アクションボタン（削除ボタンなど）の調整 */
.inline-form-group .has-many-items .has-many-item .btn-group {
    flex-shrink: 0;
    margin-left: 10px;
}

/* 商品番号フィールドの幅調整 */
.inline-form-group .has-many-items .has-many-item .form-group:nth-child(1) {
    flex: 0 0 120px;
}

/* 商品名フィールドの幅調整 */
.inline-form-group .has-many-items .has-many-item .form-group:nth-child(2) {
    flex: 2;
}

/* 単価フィールドの幅調整 */
.inline-form-group .has-many-items .has-many-item .form-group:nth-child(3) {
    flex: 0 0 100px;
}

/* 数量フィールドの幅調整 */
.inline-form-group .has-many-items .has-many-item .form-group:nth-child(4) {
    flex: 0 0 80px;
}

/* レスポンシブ対応（タブレット以下） */
@media (max-width: 768px) {
    .inline-form-group .has-many-items .has-many-item {
        flex-direction: column;
        align-items: stretch;
    }
    
    .inline-form-group .has-many-items .has-many-item .form-group {
        margin-right: 0;
        margin-bottom: 10px;
    }
    
    .inline-form-group .has-many-items .has-many-item .form-group:nth-child(1),
    .inline-form-group .has-many-items .has-many-item .form-group:nth-child(2),
    .inline-form-group .has-many-items .has-many-item .form-group:nth-child(3),
    .inline-form-group .has-many-items .has-many-item .form-group:nth-child(4) {
        flex: 1;
    }
}


</style>