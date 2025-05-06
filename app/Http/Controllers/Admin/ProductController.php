<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Models\ProductJa;
use App\Models\ProductImageJa;
use App\Models\Category;



class ProductController extends Controller
{
    /*
 * 商品一覧をページネーション付きで取得し、一覧画面を表示する。
 * カテゴリ情報と最新の商品情報を10件ずつ取得し、一覧画面に渡します。
 * @return \Illuminate\Http\Response メソッドが HTTP レスポンスオブジェクトを返す
 */
    public function index()
    {
        $products = ProductJa::with('category')->latest()->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    /*
 * 商品登録フォームを表示する。
 * カテゴリ一覧を取得し、登録フォームに渡します。
 * @return \Illuminate\Http\Response　メソッドが HTTP レスポンスオブジェクトを返す
 */
    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    /*
     * 新しく作成されたリソースをストレージに保存する
     * @param  \Illuminate\Http\Request  $requestを拡張したStoreProductRequestクラスを引数に取る（バリデーション済）
     * @return Illuminate\Http\RedirectResponse　メソッドが リダイレクトレスポンスオブジェクトを返す(returnとback)
     */
    public function store(StoreProductRequest $storeProductRequest)
    {
        $validated = $storeProductRequest->validated();/*バリデーション結果を入れる*/

        //'image'という名前のファイルがアップロードされたかどうかをチェック
        if ($storeProductRequest->hasFile('image')) {
            //そのファイルを'products'という名前のディレクトリに保存し、保存先のパスを返します。'public'は、ファイルが公開されるディレクトリに保存されることを意味します。
            //$validated['image']に、保存されたファイルのパスを格納します。
            $validated['image'] = $storeProductRequest->file('image')->store('products', 'public');
        }
        //データベースのトランザクションを開始します
        DB::beginTransaction();

        try {
            //検証済みのデータを使って、データベースの'products'テーブルに新しいレコード（商品情報）を作成します。
            $product = ProductJa::create($validated);
            //'images'という名前の複数ファイルがアップロードされた場合の処理
            if ($storeProductRequest->hasFile('images')) {
                foreach ($storeProductRequest->file('images') as $index => $image) {
                    //各ファイルを'products'ディレクトリに保存し、パスを取得します。
                    $path = $image->store('products', 'public');
                    //商品の複数画像情報を'images'テーブルに保存します。
                    $product->images()->create([
                        'filename' => $path,
                        'is_main'  => $index == $storeProductRequest->input('main_image'), //is_mainは、メイン画像かどうかを判定しています。
                    ]);
                }
            }
            //トランザクションを成功として確定し、データベースの変更を保存します。
            DB::commit();
            //商品の登録が成功した場合、商品一覧ページにリダイレクトし、成功メッセージを表示します。
            return redirect()->route('admin.products.index')->with('success', '商品を登録しました');
        } catch (\Exception $e) {
            //トランザクションを失敗として取り消し、データベースの変更を元に戻します。
            DB::rollBack();
            return back()->withErrors(['error' => '登録中にエラーが発生しました: ' . $e->getMessage()]);
        }
    }

    /*
     * 指定されたリソースを表示します。
     * @param  Productモデルのインスタンスが自動的にこの引数に渡されます。
     * @return \Illuminate\Http\Response
     * load()メソッドは、Eloquentモデルの関連データを遅延ロードする。必要な時にのみ関連データを取得する方式。
     * load()メソッドは、既に取得済みのモデルインスタンスに対して、後から関連データをロードする場合に便利です。
     */
    public function show(ProductJa $product)
    {
        $product->load(['images', 'mainImage']);
        $user = auth()->user(); // もし必要であれば

        return view('products.show', compact('product', 'user'));
    }

    /*
     * 指定されたリソースを編集するためのフォームを表示します。
     * @param  Productモデルのインスタンスが自動的にこの引数に渡されます。
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductJa $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /*
     * ストレージ内の指定されたリソースを更新します。 
     * @param  \Illuminate\Http\Request  $request
     * Product $product: Laravelのルートモデルバインディングにより、URLで指定された商品のIDに基づいて、対応するProductモデルのインスタンスが自動的にこの引数に渡されます。
     * UpdateProductRequest $updateProductRequest: これは、フォームから送信された更新データを受け取るための特別なオブジェクトです。このオブジェクトは、送信されたデータの検証（バリデーション）も行います。
     * @return \Illuminate\Http\Response
     */
    public function update(ProductJa $product, UpdateProductRequest $updateProductRequest)
    {
        //送信されたデータのうち、事前に定義されたルールに従って検証を通過したデータだけを配列として取得します。
        $validated = $updateProductRequest->validated();
        //データベースのトランザクションを開始します。
        DB::beginTransaction();

        try {
            //検証済みの更新データを使って、Productモデルの情報を更新します。
            $product->update($validated);

            // 新規画像のアップロード
            if ($updateProductRequest->hasFile('images')) {
                foreach ($updateProductRequest->file('images') as $imageFile) {
                    $path = $imageFile->store('products', 'public');
                    $product->images()->create([
                        'filename' => $path,
                        'is_main' => false,
                    ]);
                }
            }
            //既存の画像を全てis_main=falseに更新します。
            $product->images()->update(['is_main' => false]);

            // 'main_image_id'が送信されたかどうかをチェックします。
            if ($updateProductRequest->filled('main_image_id')) {
                $mainImage = $product->images()->where('id', $updateProductRequest->main_image_id)->first();
                if ($mainImage) {
                    $mainImage->is_main = true;
                    $mainImage->save();
                }
            }
            //トランザクションを成功として確定し、データベースの変更を保存します。
            DB::commit();
            return redirect()->route('admin.products.index')->with('success', '商品を更新しました');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => '更新中にエラーが発生しました: ' . $e->getMessage()]);
        }
    }

    /*
     * 指定されたリソースをストレージから削除します。
     * @param  $product: Laravelのルートモデルバインディングにより、URLで指定された商品のIDに基づいて、対応するProductモデルのインスタンスが自動的にこの引数に渡されます。
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductJa $product)
    {
        /*
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', '商品を削除しました');
        */
        return view('admin.products.confirm-delete', compact('product'));
    }

    public function destroyConfirmed(ProductJa $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', '商品を削除しました');
    }
}
