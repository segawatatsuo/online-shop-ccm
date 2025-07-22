<?php

namespace App\Admin\Controllers;

use App\Models\AdminNotice;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class AdminNoticeController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'AdminNotice';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new AdminNotice());

        $grid->column('id', __('Id'));
        $grid->column('title', __('title'));
        $grid->column('content', __('content'));
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
        $show = new Show(AdminNotice::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('title'));
        $show->field('content', __('content'));
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
        $form = new Form(new AdminNotice());

        $form->text('title', __('title'));
        $form->textarea('content', __('content'));


        return $form;
    }
}
