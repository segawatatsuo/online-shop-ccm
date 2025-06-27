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
    protected $title = 'TopPage';

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
        $grid->column('head_copy', __('Head copy'));
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

        $show->field('id', __('Id'));
        $show->field('category', __('Category'));
        $show->field('hero_img', __('Hero img'));
        $show->field('head_copy', __('Head copy'));
        $show->field('section1_display_hide', __('Section1 display hide'));
        $show->field('section1_img', __('Section1 img'));
        $show->field('section1_head_copy', __('Section1 head copy'));
        $show->field('section1_copy', __('Section1 copy'));
        $show->field('section1_background_color', __('Section1 background color'));
        $show->field('section2_display_hide', __('Section2 display hide'));
        $show->field('section2_img', __('Section2 img'));
        $show->field('section2_head_copy', __('Section2 head copy'));
        $show->field('section2_copy', __('Section2 copy'));
        $show->field('section2_background_color', __('Section2 background color'));
        $show->field('section3_display_hide', __('Section3 display hide'));
        $show->field('section3_img', __('Section3 img'));
        $show->field('section3_head_copy', __('Section3 head copy'));
        $show->field('section3_copy', __('Section3 copy'));
        $show->field('section3_background_color', __('Section3 background color'));
        $show->field('section4_display_hide', __('Section4 display hide'));
        $show->field('section4_img', __('Section4 img'));
        $show->field('section4_head_copy', __('Section4 head copy'));
        $show->field('section4_copy', __('Section4 copy'));
        $show->field('section4_background_color', __('Section4 background color'));
        $show->field('movie_section_display_hide', __('Movie section display hide'));
        $show->field('movie_section', __('Movie section'));
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
        $form = new Form(new TopPage());

        //$form->text('category', __('Category'));
        $form->select('category', 'カテゴリ')->options(Category::all()->pluck('brand_name', 'id'));
        //$form->text('hero_img', __('Hero img'));
        $form->image('hero_img', 'トップバナー画像')->move('uploads/hero_images');
        $form->display('hero_img', '画像のパス');
        $form->text('head_copy', __('ヘッドコピー'));

        $form->html('<hr>');//罫線

        $form->number('section1_display_hide', __('Section1表示・非表示'));
        //$form->text('section1_img', __('Section1 img'));
        $form->image('section1_img', 'Section1 画像')->move('uploads/section1_img');
        $form->text('section1_head_copy', __('Section1 head copy'));
        $form->textarea('section1_copy', __('Section1 copy'));

        $form->html('<hr>');//罫線

        $form->number('section2_display_hide', __('Section2 display hide'));
        $form->text('section2_img', __('Section2 img'));
        $form->text('section2_head_copy', __('Section2 head copy'));
        $form->textarea('section2_copy', __('Section2 copy'));

        $form->html('<hr>');//罫線

        $form->number('section3_display_hide', __('Section3 display hide'));
        $form->text('section3_img', __('Section3 img'));
        $form->text('section3_head_copy', __('Section3 head copy'));
        $form->textarea('section3_copy', __('Section3 copy'));

        $form->html('<hr>');//罫線

        $form->number('section4_display_hide', __('Section4 display hide'));
        $form->text('section4_img', __('Section4 img'));
        $form->text('section4_head_copy', __('Section4 head copy'));
        $form->textarea('section4_copy', __('Section4 copy'));

        $form->html('<hr>');//罫線

        $form->number('movie_section_display_hide', __('Movie section display hide'));
        $form->text('movie_section', __('Movie section'));

        return $form;
    }
}
