<?php

namespace App\Admin\Actions;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

/**
 * 発送メール送信カスタムアクション
 * グリッドの各行に表示され、特定の注文に対して発送メール送信をシミュレートします。
 */
class SendShippingMail extends RowAction
{
    // アクションボタンの表示名
    public $name = '発送メール送信';

    /**
     * アクションが実行された際の処理
     *
     * @param Model $model 現在の行のモデルインスタンス（この場合はOrderモデル）
     * @return \Encore\Admin\Actions\Response
     */
    public function handle(Model $model)
    {
        // ここに実際のメール送信ロジックを記述します。
        // 例: Mail::to($model->customer->email)->send(new ShippingMail($model));
        // データベースのステータス更新などを行うこともできます。
        // $model->update(['status' => '発送済み']);

        // 成功メッセージとページのリフレッシュを返す
        return $this->response()->success('発送メールを送信しました: ' . $model->order_number)->refresh();
    }

    /**
     * アクション実行前の確認ダイアログを設定
     * ユーザーが誤ってアクションを実行しないように確認を促します。
     */
    public function dialog()
    {
        $this->question('確認', 'この注文の発送メールを送信してもよろしいですか？');
    }
}
