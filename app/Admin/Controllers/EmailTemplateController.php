<?php

namespace App\Admin\Controllers;

use App\Models\EmailTemplate;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;




class EmailTemplateController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'メールテンプレート';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new EmailTemplate());

        $grid->column('id', __('Id'));
        $grid->column('slug', __('スラッグ'))->width(150);
        $grid->column('subject', __('件名'));
        $grid->column('description', __('説明'));
        $grid->column('created_at', __('作成日時'));
        $grid->column('updated_at', __('更新日時'));

        /*
        $grid->disableCreateButton(); // スラッグを固定したいので新規作成は不可に
        $grid->actions(function ($actions) {
            $actions->disableDelete(); // 削除も不可に
        });
        $grid->disableRowSelector();
        $grid->disableColumnSelector();
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
        $show = new Show(EmailTemplate::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('slug', __('スラッグ'));
        $show->field('subject', __('件名'));
        $show->field('body', __('本文'))->unescape()->as(function ($body) {
            // ここでプレビュー表示なども可能
            return nl2br(e($body));
        });
        $show->field('description', __('説明'));
        $show->field('created_at', __('作成日時'));
        $show->field('updated_at', __('更新日時'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new EmailTemplate());

        $form->display('id', __('Id'));
        $form->text('slug', __('スラッグ'))->readonly(); // スラッグは編集不可
        $form->text('subject', __('件名'))->required();
        $form->textarea('body', __('本文'))->rows(20)->required()->help('差し込み項目: {{customer_name}}, {{customer_address}}, {{delivery_name}}, {{delivery_address}}, {{shipping_date}}, {{shipping_company}}, {{tracking_number}}, {{order_id}}, {{order_items}} (HTMLテーブル形式), {{footer}}');
        $form->textarea('description', __('説明'));

        $form->display('created_at', __('作成日時'));
        $form->display('updated_at', __('更新日時'));

        return $form;
    }
}