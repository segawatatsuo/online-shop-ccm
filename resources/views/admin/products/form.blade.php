<div>
    <label>商品名</label><br>
    <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}">
</div>
<div>
    <label>価格</label><br>
    <input type="number" name="price" value="{{ old('price', $product->price ?? '') }}">
</div>
<div>
    <label>会員価格</label><br>
    <input type="number" name="member_price" value="{{ old('member_price', $product->member_price ?? '') }}">
</div>
<div>
    <label>カテゴリ</label><br>
    <select name="category_id">
        <option value="">選択してください</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" @if (old('category_id', $product->category_id ?? '') == $category->id) selected @endif>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
</div>
<div>
    <label>説明</label><br>
    <textarea name="description">{{ old('description', $product->description ?? '') }}</textarea>
</div>
<div>


    {{-- 複数画像のアップロード --}}
    <div>
        <label>画像（複数選択可）</label><br>
        <input type="file" name="images[]" multiple accept="image/*">
        @error('images.*')
            <div style="color:red;">{{ $message }}</div>
        @enderror
    </div>

    {{-- 既存画像の表示とメイン画像指定（編集時のみ） --}}
    {{-- 既存画像の表示とメイン画像指定 --}}
    @if (!empty($product) && $product->images)
        <div>
            <label>登録済み画像（クリックでメイン画像を指定）</label><br>
            @foreach ($product->images as $img)
                <div style="margin-bottom: 10px;">
                    <img src="{{ asset('storage/' . $img->filename) }}" style="max-width: 100px;"><br>
                    <label>
                        <input type="radio" name="main_image_id" value="{{ $img->id }}"
                            {{ $img->is_main ? 'checked' : '' }}>
                        メイン画像にする
                    </label>
                </div>
            @endforeach
        </div>
    @endif
    {{-- 既存画像の表示とメイン画像指定（編集時のみ） --}}



</div>
