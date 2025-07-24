<?php

namespace App\Admin\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductJa;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Grid;
use Encore\Admin\Form;
use Encore\Admin\Show;

use Encore\Admin\Widgets\Form as WidgetForm;
use Encore\Admin\Form\NestedForm;


// ↓ 追加
use App\Admin\Actions\Grid\DuplicateAction;
use App\Models\Category;

class ProductJaController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    //protected $title = 'ProductJa';
    protected $title = '商品情報';
    /**
     * Make a grid builder.
     *
     * @return Grid　一覧
     */
    protected function grid()
    {
        $grid = new Grid(new ProductJa());

        $grid->column('id', 'ID')->sortable();
        $grid->column('not_display', '非表示');

        // リレーション経由でカテゴリ名を表示
        $grid->column('category.brand', 'ブランド');
        $grid->column('product_code', __('商品コード'));
        $grid->column('wholesale', __('法人商品'));
        $grid->column('price', __('価格'));
        $grid->column('classification', __('分類'));
        $grid->column('classification_ja', __('分類名'));
        $grid->column('kind', __('種類'));
        $grid->column('name', '商品名');

        // バッチアクション追加
        $grid->tools(function (Grid\Tools $tools) {
            $tools->batch(function (Grid\Tools\BatchActions $batch) {
                $batch->add('複製', new DuplicateAction());
            });
        });

        // ✅ ここがフィルターの設定場所
        $grid->filter(function ($filter) {
            $filter->like('name', '商品名');

            // カテゴリ名で絞り込み（セレクトボックス）
            $filter->equal('category_id', 'カテゴリ')->select(
                \App\Models\Category::all()->pluck('name', 'id')
            );
        });


        $grid->model()->orderBy('sort_order', 'asc'); //並びをソート順に変更

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show　表示
     */
    protected function detail($id)
    {
        $show = new Show(ProductJa::findOrFail($id));

        $show->field('category.brand', 'ブランド');
        $show->field('name', '商品名');

        $show->field('description_1_heading', '商品見出し1');
        $show->field('description_1', __('説明文1'));
        $show->field('description_2_heading', '商品見出し2');
        $show->field('description_2', __('説明文2'));

        $show->field('wholesale', __('法人商品'));
        $show->field('price', __('価格'));

        $show->field('not_display',__('非表示'));

        $show->field('product_code', __('商品コード'));
        $show->field('classification', __('分類'));
        $show->field('classification_ja', __('分類名'));
        $show->field('kind', __('種類'));

        $show->field('title_header', __('タイトルヘッダー'));
        $show->field('stock', __('在庫数'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        // ✅ 追加：複数画像表示
        $show->images('商品画像')->as(function ($images) {
            $html = '';
            foreach ($images as $img) {
                $html .= "<img src='/uploads/{$img['image_path']}' style='max-width:120px;margin:5px;border:1px solid #ccc;' />";
            }
            return $html;
        })->unescape();

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form　編集画面
     */
    protected function form()
    {
        $form = new Form(new ProductJa());

        //$form->number('category_id', __('カテゴリID'));
        $form->text('category.brand', 'ブランド');
        $form->text('name', __('商品名'));
        
        $form->text('description_1_heading', '商品見出し1');
        $form->textarea('description_1', __('説明文1'));
        $form->text('description_2_heading', '商品見出し2');
        $form->textarea('description_2', __('説明文2'));



        $form->switch('wholesale', __('法人商品'));
        $form->switch('not_display',__('非表示'));
        $form->number('price', __('価格'));
        $form->text('product_code', __('商品コード'));
        $form->text('classification', __('分類'));
        $form->text('classification_ja', __('分類名'));
        $form->text('kind', __('種類'));
        $form->text('title_header', __('タイトルヘッダー'));
        $form->number('stock', __('在庫数'));

        // 複数画像登録（画像に制限なし）
        $form->hasMany('images', '商品画像', function (Form\NestedForm $form) {

            $form->image('image_path', '画像')->removable(); // 画像削除ボタン付き
            $form->hidden('order')->default(0); // 並び順

            $form->radio('is_main', 'メイン画像')->options([
                1 => 'メインにする',
                0 => 'しない',
            ])->default(0);
        })->useTable();


        return $form;
    }


    //レコード複製
    public function duplicate(Request $request)
    {
        $ids = $request->input('ids');

        if (!$ids || !is_array($ids)) {
            return response()->json(['message' => '無効なIDです'], 400);
        }

        foreach (\App\Models\ProductJa::findMany($ids) as $product) {
            // sort_order が null でないことを確認してからクエリを実行
            if ($product->sort_order !== null) {
                // 複製前に、後ろのsort_orderを+1してスペース確保
                \App\Models\ProductJa::where('sort_order', '>', $product->sort_order)
                    ->increment('sort_order');
            } else {
                // sort_order が null の場合の処理（例: デフォルト値を設定するか、ログに警告を出すなど）
                // ここでは仮に、最も大きなsort_orderの次に来るように設定します
                $maxSortOrder = \App\Models\ProductJa::max('sort_order');
                $product->sort_order = ($maxSortOrder !== null) ? $maxSortOrder : 0; // null の場合は0を設定
            }
            
            $new = $product->replicate();
            $new->name = $product->name . '（複製）'; // 任意
            
            // 新しいレコードのsort_orderは、元のレコードのsort_order+1
            // 上のif文で $product->sort_order が null だった場合に備えて、値を設定しておく
            $new->sort_order = $product->sort_order + 1; 
            
            $new->save();
        }

        return response()->json(['message' => '複製完了']);
    }
}