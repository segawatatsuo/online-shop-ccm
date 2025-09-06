<?php

namespace App\Admin\Actions\Order;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Services\AmazonPayService;

class CaptureCharge extends RowAction
{
    public $name = '売上確定';

    public function handle(Model $model, Request $request)
    {
        try {
            $amazonPay = app(AmazonPayService::class);

            // ✅ authorization_id をチェック
            if (!$model->authorization_id) {
                return $this->response()->error('与信IDが存在しません。');
            }

            // ✅ captureCharge では authorization_id を渡す
            $response = $amazonPay->captureCharge($model->authorization_id, $model->total_price);
            

            if (isset($response['statusDetails']['state']) && $response['statusDetails']['state'] === 'Captured') {
                $model->status = \App\Models\Order::STATUS_CAPTURED; // 売上確定
                $model->save();

                return $this->response()->success('売上を確定しました！')->refresh();
            }

            return $this->response()->error('売上確定に失敗しました。');
        } catch (\Exception $e) {
            return $this->response()->error('エラー: ' . $e->getMessage());
        }
    }
}
