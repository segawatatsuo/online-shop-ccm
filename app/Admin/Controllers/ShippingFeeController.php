<?php

namespace App\Admin\Controllers;

use App\Models\ShippingFee;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ShippingFeeController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'ShippingFee';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ShippingFee());

        $grid->column('id', __('Id'));
        $grid->column('prefecture', __('都道府県'));
        $grid->column('fee', __('送料'));

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
        $show = new Show(ShippingFee::findOrFail($id));

        $show->field('prefecture', __('都道府県'));
        $show->field('fee', __('送料'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new ShippingFee());

        $form->text('prefecture', __('都道府県'));
        $form->number('fee', __('送料'));

        return $form;
    }
}
