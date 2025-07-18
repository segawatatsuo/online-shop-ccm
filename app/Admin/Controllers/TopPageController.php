<?php

namespace App\Admin\Controllers;

use App\Models\TopPage;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\Models\Category;

class TopPageController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'トップページ';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new TopPage());

        $grid->column('id', __('Id'));
        $grid->column('category', __('Category'));

        $grid->column('hero_img', __('Hero img'));
        $grid->column('hero_head_copy', __('hero_head_copy'));
        $grid->column('hero_lead_copy', __('hero_lead_copy'));
        /*
        $grid->column('section1_display_hide', __('Section1 display hide'));
        $grid->column('section1_img', __('Section1 img'));
        $grid->column('section1_head_copy', __('Section1 head copy'));
        $grid->column('section1_copy', __('Section1 copy'));
        $grid->column('section1_background_color', __('Section1 background color'));
        $grid->column('section2_display_hide', __('Section2 display hide'));
        $grid->column('section2_img', __('Section2 img'));
        $grid->column('section2_head_copy', __('Section2 head copy'));
        $grid->column('section2_copy', __('Section2 copy'));
        $grid->column('section2_background_color', __('Section2 background color'));
        $grid->column('section3_display_hide', __('Section3 display hide'));
        $grid->column('section3_img', __('Section3 img'));
        $grid->column('section3_head_copy', __('Section3 head copy'));
        $grid->column('section3_copy', __('Section3 copy'));
        $grid->column('section3_background_color', __('Section3 background color'));
        $grid->column('section4_display_hide', __('Section4 display hide'));
        $grid->column('section4_img', __('Section4 img'));
        $grid->column('section4_head_copy', __('Section4 head copy'));
        $grid->column('section4_copy', __('Section4 copy'));
        $grid->column('section4_background_color', __('Section4 background color'));
        $grid->column('movie_section_display_hide', __('Movie section display hide'));
        $grid->column('movie_section', __('Movie section'));
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
        $show = new Show(TopPage::findOrFail($id));

        $show->field('category', __('カテゴリー'))->using(Category::all()->pluck('brand_name', 'id')->toArray());;
        $show->field('hero_img', __('メイン画像'))->image();
        $show->field('hero_head_copy', __('メイン画像上の見出し'));
        $show->field('hero_lead_copy', __('メイン画像上のコピー'));

        $show->field('section1_display_hide', __('セクション1表示'))->using([1 => '表示', 0 => '非表示']);
        $show->field('section1_img', __('セクション1画像'))->image();
        $show->field('section1_head_copy', __('セクション1見出し'));
        $show->field('section1_copy', __('セクション1コピー'));

        $show->field('section2_display_hide', __('セクション2表示'))->using([1 => '表示', 0 => '非表示']);
        $show->field('section2_img', __('セクション2画像'))->image();
        $show->field('section2_head_copy', __('セクション2見出し'));
        $show->field('section2_copy', __('セクション2コピー'));

        $show->field('section3_display_hide', __('セクション3表示'))->using([1 => '表示', 0 => '非表示']);
        $show->field('section3_img', __('セクション3画像'))->image();
        $show->field('section3_head_copy', __('セクション3見出し'));
        $show->field('section3_copy', __('セクション3コピー'));

        $show->field('movie_section_display_hide', __('ムービー表示'))->using([1 => '表示', 0 => '非表示']);
        $show->field('movie_section_url', __('動画URL'));

        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form 編集画面
     */
    protected function form()
    {
        $form = new Form(new TopPage());

        $form->select('category', 'カテゴリ')->options(Category::all()->pluck('brand_name', 'id'))->setLabelClass(['class1','asterisk']);

        $form->image('hero_img', __('メイン画像')) // フォームのフィールド名
            ->disk('admin')      // 定義したディスク名 (例: 'uploads' または 'public')
            ->move('images')     // uploads/images/ に保存
            ->removable()        // 削除ボタンつき
            ->uniqueName()      // 同じ名前のファイルを上書きしない
            ->setLabelClass(['class1','asterisk']);//アスタリスク記号はpublic/css/admin.cssに内容が記載されている

        $form->text('hero_head_copy', __('メイン画像上の見出し'));
        $form->color('hero_head_copy_color', __('文字色'));
        $form->text('hero_lead_copy', __('メイン画像上のコピー'));
        $form->color('hero_lead_copy_color', __('文字色'));

        $form->switch('section1_display_hide', __('セクション1表示'));
        $form->image('section1_img', __('セクション1画像')) // フォームのフィールド名
            ->disk('admin')      // 定義したディスク名 (例: 'uploads' または 'public')
            ->move('images')     // uploads/images/ に保存
            ->removable()        // 削除ボタンつき
            ->uniqueName();      // 同じ名前のファイルを上書きしない
        $form->text('section1_head_copy', __('セクション1見出し'));
        $form->textarea('section1_copy', __('セクション1コピー'))->rows(4);

        $form->switch('section2_display_hide', __('セクション2表示'));
        $form->image('section2_img', __('セクション2画像')) // フォームのフィールド名
            ->disk('admin')      // 定義したディスク名 (例: 'uploads' または 'public')
            ->move('images')     // uploads/images/ に保存
            ->removable()        // 削除ボタンつき
            ->uniqueName();      // 同じ名前のファイルを上書きしない
        $form->text('section2_head_copy', __('セクション2見出し'));
        $form->textarea('section2_copy', __('セクション2コピー'))->rows(4);


        $form->switch('section3_display_hide', __('セクション3表示'));
        $form->image('section3_img', __('セクション3画像')) // フォームのフィールド名
            ->disk('admin')      // 定義したディスク名 (例: 'uploads' または 'public')
            ->move('images')     // uploads/images/ に保存
            ->removable()        // 削除ボタンつき
            ->uniqueName();      // 同じ名前のファイルを上書きしない
        $form->text('section3_head_copy', __('セクション3見出し'));
        $form->textarea('section3_copy', __('セクション3コピー'))->rows(4);

        $form->switch('movie_section_display_hide', __('ムービーセクション表示'));
        $form->text('movie_section', __('動画URL'));




        return $form;
    }
}
