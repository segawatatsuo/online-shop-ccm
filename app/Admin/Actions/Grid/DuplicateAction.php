<?php

namespace App\Admin\Actions\Grid;

use Encore\Admin\Grid\Tools\BatchAction;
use Illuminate\Database\Eloquent\Collection;

class DuplicateAction extends BatchAction
{
    protected $selector = '.duplicate-selected'; // オプションでボタンにクラス名をつけられます

    public function script()
    {
        return <<<SCRIPT
            $('.duplicate-selected').on('click', function () {
                var ids = $.admin.grid.selected();
                if (ids.length === 0) {
                    return alert('レコードを選択してください');
                }

                $.post('/admin/product/duplicate', {
                    _token: LA.token,
                    ids: ids
                }, function (data) {
                    $.pjax.reload('#pjax-container');
                    toastr.success('複製が完了しました');
                });
            });
        SCRIPT;
    }

    public function render()
    {
        return "<a class='btn btn-sm btn-default duplicate-selected'>複製</a>";
    }
}
