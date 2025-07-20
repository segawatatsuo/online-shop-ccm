<?php

namespace App\Admin\Controllers;

use App\Models\CorporateCustomer;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CorporateCustomerController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '法人顧客';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CorporateCustomer());

        /*
        $grid->column('id', __('Id'));
        $grid->column('user_id', __('User id'));
        */
        $grid->column('order_company_name', __('注文者会社名'));
        $grid->column('order_department', __('注文者部署名'));
        $grid->column('order_sei', __('注文者姓'));
        $grid->column('order_mei', __('注文者名'));
        $grid->column('order_phone', __('注文者電話番号'));
        $grid->column('homepage', __('ホームページ'));
        $grid->column('email', __('注文者メールアドレス'));
        /*
        $grid->column('order_zip', __('Order zip'));
        $grid->column('order_add01', __('Order add01'));
        $grid->column('order_add02', __('Order add02'));
        $grid->column('order_add03', __('Order add03'));
        $grid->column('same_as_orderer', __('Same as orderer'));
        $grid->column('delivery_company_name', __('Delivery company name'));
        $grid->column('delivery_department', __('Delivery department'));
        $grid->column('delivery_sei', __('Delivery sei'));
        $grid->column('delivery_mei', __('Delivery mei'));
        $grid->column('delivery_phone', __('Delivery phone'));
        $grid->column('delivery_email', __('Delivery email'));
        $grid->column('delivery_zip', __('Delivery zip'));
        $grid->column('delivery_add01', __('Delivery add01'));
        $grid->column('delivery_add02', __('Delivery add02'));
        $grid->column('delivery_add03', __('Delivery add03'));
        $grid->column('corporate_number', __('Corporate number'));
        $grid->column('discount_rate', __('Discount rate'));
        $grid->column('is_approved', __('Is approved'));
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
        $show = new Show(CorporateCustomer::findOrFail($id));
        /*
        $show->field('id', __('Id'));
        $show->field('user_id', __('User id'));
        */
        $show->field('order_company_name', __('注文者会社名'));
        $show->field('order_department', __('注文者部署名'));
        $show->field('order_sei', __('注文者姓'));
        $show->field('order_mei', __('注文者名'));
        $show->field('order_phone', __('注文者電話'));
        $show->field('homepage', __('ホームページ'));
        $show->field('email', __('注文者メールアドレス	'));
        $show->field('order_zip', __('注文者郵便番号'));
        $show->field('order_add01', __('注文者住所1'));
        $show->field('order_add02', __('注文者住所2'));
        $show->field('order_add03', __('注文者住所3'));
        //$show->field('same_as_orderer', __('注文者と送信先が同じ'));
        $show->field('delivery_company_name', __('配送先会社名'));
        $show->field('delivery_department', __('配送先部署名'));
        $show->field('delivery_sei', __('配送先姓'));
        $show->field('delivery_mei', __('配送先名'));
        $show->field('delivery_phone', __('配送先電話'));
        $show->field('delivery_email', __('配送先メールアドレス'));
        $show->field('delivery_zip', __('配送先郵便番号'));
        $show->field('delivery_add01', __('配送先住所1'));
        $show->field('delivery_add02', __('配送先住所2'));
        $show->field('delivery_add03', __('配送先住所3'));
        $show->field('corporate_number', __('法人番号'));
        $show->field('discount_rate', __('割引率'));
        $show->field('is_approved', __('承認状態'));
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
        $form = new Form(new CorporateCustomer());
        /*
        $form->number('user_id', __('User id'));
        */
        $form->text('order_company_name', __('注文者会社名'));
        $form->text('order_department', __('注文者部署名'));
        $form->text('order_sei', __('注文者姓'));
        $form->text('order_mei', __('注文者名'));
        $form->text('order_phone', __('注文者電話'));
        $form->text('homepage', __('ホームページ'));
        $form->email('email', __('注文者メールアドレス'));
        $form->text('order_zip', __('注文者郵便番号'));
        $form->text('order_add01', __('注文者住所1'));
        $form->text('order_add02', __('注文者住所2'));
        $form->text('order_add03', __('注文者住所3'));
        //$form->text('same_as_orderer', __('Same as orderer'));
        $form->text('delivery_company_name', __('配送先会社名'));
        $form->text('delivery_department', __('配送先部署名'));
        $form->text('delivery_sei', __('配送先姓'));
        $form->text('delivery_mei', __('配送先名'));
        $form->text('delivery_phone', __('配送先電話'));
        $form->text('delivery_email', __('配送先メールアドレス'));
        $form->text('delivery_zip', __('配送先郵便番号'));
        $form->text('delivery_add01', __('配送先住所1'));
        $form->text('delivery_add02', __('配送先住所2'));
        $form->text('delivery_add03', __('配送先住所3'));
        $form->text('corporate_number', __('法人番号'));
        $form->decimal('discount_rate', __('割引率'));
        $form->switch('is_approved', __('承認状態'))->default(1);

        return $form;
    }
}
