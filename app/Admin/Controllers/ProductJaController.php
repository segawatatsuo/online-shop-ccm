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
    protected $title = '商品';
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
        $grid->column('color', __('色'));
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

        //$show->field('id', __('Id'));
        //$show->field('category_id', __('カテゴリID'));
        $show->field('category.brand', 'ブランド');
        $show->field('name', '商品名');
        $show->field('description', __('説明文'));
        $show->field('image', __('Image'));
        $show->field('price', __('価格'));
        //$show->field('member_price', __('Member price'));
        $show->field('product_code', __('商品コード'));
        $show->field('classification', __('分類'));
        $show->field('classification_ja', __('分類名'));
        $show->field('kind', __('種類'));
        $show->field('color', __('色'));
        //$show->field('color_map', __('Color map'));
        $show->field('title_header', __('タイトルヘッダー'));
        $show->field('stock', __('在庫数'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

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
        $form->textarea('description', __('説明文'));
        $form->image('image', __('Image'));
        $form->switch('wholesale', __('法人商品'));
        $form->number('price', __('価格'));
        //$form->number('member_price', __('Member price'));
        $form->text('product_code', __('商品コード'));
        $form->text('classification', __('分類'));
        $form->text('classification_ja', __('分類名'));
        $form->text('kind', __('種類'));
        //$form->color('color', __('色'));
        //$form->text('color_map', __('Color map'));
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

            // 複製前に、後ろのsort_orderを+1してスペース確保
            \App\Models\ProductJa::where('sort_order', '>', $product->sort_order)
                ->increment('sort_order');

            $new = $product->replicate();
            $new->name = $product->name . '（複製）'; // 任意
            $new->sort_order = $product->sort_order + 1; //コピー元のレコードのソート番号+1。こうすることで、コピー元のレコードの下に並ばせる
            $new->save();
        }

        return response()->json(['message' => '複製完了']);
    }
}
