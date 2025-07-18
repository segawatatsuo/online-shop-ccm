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

        $grid->column('id', __('Id'));
        $grid->column('user_id', __('User id'));
        $grid->column('order_company_name', __('Order company name'));
        $grid->column('order_department', __('Order department'));
        $grid->column('order_sei', __('Order sei'));
        $grid->column('order_mei', __('Order mei'));
        $grid->column('order_phone', __('Order phone'));
        $grid->column('homepage', __('Homepage'));
        $grid->column('email', __('Email'));
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

        $show->field('id', __('Id'));
        $show->field('user_id', __('User id'));
        $show->field('order_company_name', __('Order company name'));
        $show->field('order_department', __('Order department'));
        $show->field('order_sei', __('Order sei'));
        $show->field('order_mei', __('Order mei'));
        $show->field('order_phone', __('Order phone'));
        $show->field('homepage', __('Homepage'));
        $show->field('email', __('Email'));
        $show->field('order_zip', __('Order zip'));
        $show->field('order_add01', __('Order add01'));
        $show->field('order_add02', __('Order add02'));
        $show->field('order_add03', __('Order add03'));
        $show->field('same_as_orderer', __('Same as orderer'));
        $show->field('delivery_company_name', __('Delivery company name'));
        $show->field('delivery_department', __('Delivery department'));
        $show->field('delivery_sei', __('Delivery sei'));
        $show->field('delivery_mei', __('Delivery mei'));
        $show->field('delivery_phone', __('Delivery phone'));
        $show->field('delivery_email', __('Delivery email'));
        $show->field('delivery_zip', __('Delivery zip'));
        $show->field('delivery_add01', __('Delivery add01'));
        $show->field('delivery_add02', __('Delivery add02'));
        $show->field('delivery_add03', __('Delivery add03'));
        $show->field('corporate_number', __('Corporate number'));
        $show->field('discount_rate', __('Discount rate'));
        $show->field('is_approved', __('Is approved'));
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

        $form->number('user_id', __('User id'));
        $form->text('order_company_name', __('Order company name'));
        $form->text('order_department', __('Order department'));
        $form->text('order_sei', __('Order sei'));
        $form->text('order_mei', __('Order mei'));
        $form->text('order_phone', __('Order phone'));
        $form->text('homepage', __('Homepage'));
        $form->email('email', __('Email'));
        $form->text('order_zip', __('Order zip'));
        $form->text('order_add01', __('Order add01'));
        $form->text('order_add02', __('Order add02'));
        $form->text('order_add03', __('Order add03'));
        $form->text('same_as_orderer', __('Same as orderer'));
        $form->text('delivery_company_name', __('Delivery company name'));
        $form->text('delivery_department', __('Delivery department'));
        $form->text('delivery_sei', __('Delivery sei'));
        $form->text('delivery_mei', __('Delivery mei'));
        $form->text('delivery_phone', __('Delivery phone'));
        $form->text('delivery_email', __('Delivery email'));
        $form->text('delivery_zip', __('Delivery zip'));
        $form->text('delivery_add01', __('Delivery add01'));
        $form->text('delivery_add02', __('Delivery add02'));
        $form->text('delivery_add03', __('Delivery add03'));
        $form->text('corporate_number', __('Corporate number'));
        $form->decimal('discount_rate', __('Discount rate'));
        $form->switch('is_approved', __('Is approved'))->default(1);

        return $form;
    }
}
