<?php




/**
 * Laravel-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Encore\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Encore\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */

Encore\Admin\Form::forget(['map', 'editor']);

Encore\Admin\Admin::css('/css/custom-admin.css');



Encore\Admin\Facades\Admin::menu(function (Encore\Admin\Widgets\Menu $menu) {
    $menu->add([
        [
            'title' => '商品管理',
            'icon'  => 'fa-box',
            'uri'   => 'product-ja', // このURLが /admin/product-ja に対応している必要があります
        ],
    ]);
});


