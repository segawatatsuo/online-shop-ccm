<?php

namespace App\Admin\Controllers;

use App\Models\Setting;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class SettingController extends AdminController
{
    protected $title = '共通設定';

    protected function grid()
    {
        $grid = new Grid(new Setting());
        $grid->column('id', __('Id'));
        $grid->column('name', __('名称'));
        $grid->column('value', __('内容'));
        /*
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            $actions->disableDelete();
        });
       */

        return $grid;
    }

    protected function form()
    {
        $form = new Form(new Setting());

        $form->text('id', __('Id'));
        $form->text('name', __('名称'));

        $form->textarea('value', __('内容'))->rows(10)->help('共通設定を編集します。');
        return $form;
    }


    protected function detail($id)
    {
        $show = new Show(Setting::findOrFail($id));
        $show->field('id', __('Id'));
        $show->field('name', __('名称'));
        $show->field('value', __('内容'));
        return $show;
    }

}