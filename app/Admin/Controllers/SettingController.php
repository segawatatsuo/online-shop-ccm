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
        $grid->column('key', __('設定キー'));
        $grid->column('value', __('設定値'));
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
        $form->text('key', __('設定キー'));

        $form->textarea('value', __('設定値'))->rows(10)->help('メールフッターなどの共通設定を編集します。');
        return $form;
    }
}