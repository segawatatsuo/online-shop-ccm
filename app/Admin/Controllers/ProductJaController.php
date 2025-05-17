<?php

namespace App\Admin\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductJa;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Grid;
use Encore\Admin\Form;
use Encore\Admin\Show;

// ↓ 追加
use App\Admin\Actions\Grid\DuplicateAction;

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
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ProductJa());

        $grid->column('id', 'ID')->sortable();
        $grid->column('product_code', __('商品コード'));
        $grid->column('price', __('価格'));
        $grid->column('classification', __('分類'));
        $grid->column('classification_ja', __('分類 ja'));
        $grid->column('color', __('色'));
        $grid->column('name', '商品名');

        // バッチアクション追加
        $grid->tools(function (Grid\Tools $tools) {
            $tools->batch(function (Grid\Tools\BatchActions $batch) {
                $batch->add('複製', new DuplicateAction());
            });
        });

        $grid->model()->orderBy('sort_order', 'asc');//並びをソート順に変更

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(ProductJa::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('category_id', __('Category id'));
        $show->field('name', '商品名');
        $show->field('description', __('Description'));
        $show->field('image', __('Image'));
        $show->field('price', __('Price'));
        $show->field('member_price', __('Member price'));
        $show->field('product_code', __('Product code'));
        $show->field('classification', __('Classification'));
        $show->field('classification_ja', __('Classification ja'));
        $show->field('kind', __('Kind'));
        $show->field('color', __('Color'));
        $show->field('color_map', __('Color map'));
        $show->field('title_header', __('Title header'));
        $show->field('stock', __('Stock'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new ProductJa());

        $form->number('category_id', __('Category id'));
        $form->text('name', __('商品名'));
        $form->textarea('description', __('Description'));
        $form->image('image', __('Image'));
        $form->number('price', __('Price'));
        $form->number('member_price', __('Member price'));
        $form->text('product_code', __('Product code'));
        $form->text('classification', __('Classification'));
        $form->text('classification_ja', __('Classification ja'));
        $form->text('kind', __('Kind'));
        $form->color('color', __('Color'));
        $form->text('color_map', __('Color map'));
        $form->text('title_header', __('Title header'));
        $form->number('stock', __('Stock'));

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
