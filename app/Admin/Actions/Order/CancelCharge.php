<?php

namespace App\Admin\Actions\Order;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Services\AmazonPayService;

class CancelCharge extends RowAction
{
    public $name = 'キャンセル';

    public function handle(Model $model, Request $request)
    {
        try {
            $amazonPay = app(AmazonPayService::class);

            if (!$model->amazon_charge_id) {
                return $this->response()->error('与信IDが存在しません。');
            }

            $response = $amazonPay->cancelCharge($model->amazon_charge_id);

            if (isset($response['statusDetails']['state']) && $response['statusDetails']['state'] === 'Canceled') {
                $model->status = \App\Models\Order::STATUS_CANCELED; // ステータス更新
                $model->save();

                return $this->response()->success('注文をキャンセルしました！')->refresh();
            }

            return $this->response()->error('キャンセルに失敗しました。');
        } catch (\Exception $e) {
            return $this->response()->error('エラー: ' . $e->getMessage());
        }
    }
}
