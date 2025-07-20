<?php

namespace App\Admin\Controllers;

use App\Models\Customer;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CustomerController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '顧客情報';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Customer());

        //$grid->column('id', __('Id'));
        $grid->column('sei', __('姓'));
        $grid->column('mei', __('名'));
        $grid->column('email', __('メールアドレス'));
        $grid->column('phone', __('電話番号'));
        $grid->column('zip', __('郵便番号'));
        $grid->column('input_add01', __('住所1'));
        $grid->column('input_add02', __('住所2'));
        $grid->column('input_add03', __('住所3'));
        /*
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
        */

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
        $show = new Show(Customer::findOrFail($id));

        //$show->field('id', __('Id'));
        $show->field('sei', __('姓'));
        $show->field('mei', __('名'));
        $show->field('email', __('メールアドレス'));
        $show->field('phone', __('電話番号'));
        $show->field('zip', __('郵便番号'));
        $show->field('input_add01', __('住所1'));
        $show->field('input_add02', __('住所2'));
        $show->field('input_add03', __('住所3'));
        /*
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        */

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Customer());

        $form->text('sei', __('姓'));
        $form->text('mei', __('名'));
        $form->email('email', __('メールアドレス'));
        $form->mobile('phone', __('電話番号'));
        $form->text('zip', __('郵便番号'));
        $form->text('input_add01', __('住所1'));
        $form->text('input_add02', __('住所2'));
        $form->text('input_add03', __('住所3'));

        return $form;
    }
}
