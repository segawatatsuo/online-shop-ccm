<?php

namespace App\Admin\Controllers;

use App\Models\CompanyInfo;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Faker\Provider\ar_EG\Company;

class CompanyInfoController extends AdminController
{
    protected $title = '会社情報';

    protected function grid()
    {
        $grid = new Grid(new CompanyInfo());
        $grid->column('id', __('Id'));
        //$grid->column('key', __('設定キー'));
        $grid->column('explanation', __('説明'));
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
        $form = new Form(new CompanyInfo());

        $form->display('id', __('Id'));
        $form->display('explanation', __('説明'));

        $form->textarea('value', __('設定値'))->rows(10);
        //$form->textarea('value', __('設定値'))->rows(10)->help('CC用メールアドレスなど');
        return $form;
    }
}
