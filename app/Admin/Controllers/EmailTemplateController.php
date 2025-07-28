<?php

namespace App\Admin\Controllers;

use App\Models\EmailTemplate;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Form\Field\Summernote;

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
            return $body; // e()やnl2br()を使わず、HTMLとして表示
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
        $form->summernote('body', __('本文'))->required()->help(
            '差し込み項目: {{name}}, {{order_number}}, {{shipping_date}}, {{shipping_company}}, {{tracking_number}}, {{customer_name}}, {{customer_zip}}, {{customer_address}}, {{customer_phone}}, {{delivery_name}}, {{delivery_zip}}, {{delivery_address}}, {{delivery_phone}}, {{order_items}}, {{shipping}}, {{total_amount}}, {{footer}}'
        );

        $form->textarea('description', __('説明'));

        return $form;
    }
}
