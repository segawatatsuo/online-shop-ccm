<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
class CompanyInfo extends Model
{
    use HasFactory;

    protected $table = 'company_infos';

    protected $fillable = ['key', 'value', 'explanation'];


    /**
     * 特定のキーの値を取得
     */
    public static function getValue($key)
    {
        return self::where('key', $key)->value('value');
    }

    /**
     * 複数のキーの値を一度に取得
     */
    public static function getValues($keys)
    {
        return self::whereIn('key', $keys)->pluck('value', 'key')->toArray();
    }

    /**
     * フッター用のデータを取得
     */
    public static function getFooterData()
    {
        $keys = ['company-zip', 'company-address', 'company-tel', 'company-fax', 'Copyright'];
        $companyData = self::getValues($keys);

        // footer-address: 郵便番号 + 住所
        $zip = $companyData['company-zip'] ?? '';
        $address = $companyData['company-address'] ?? '';
        $footerAddress = trim($zip . ' ' . $address);

        // footer-contact: 電話 + FAX
        $tel = $companyData['company-tel'] ?? '';
        $fax = $companyData['company-fax'] ?? '';
        $contact = [];
        if ($tel) $contact[] = 'TEL: ' . $tel;
        if ($fax) $contact[] = 'FAX: ' . $fax;
        $footerContact = implode(' / ', $contact);

        return [
            'footer-address' => $footerAddress,
            'footer-contact' => $footerContact,
            'footer-copyright' => $companyData['Copyright'] ?? ''
        ];
    }


    // キャッシュ自動更新の実装
    protected static function booted()
    {
        // データが保存された時にキャッシュをクリア
        static::saved(function () {
            Cache::forget('company_footer_data');
        });

        // データが削除された時にキャッシュをクリア
        static::deleted(function () {
            Cache::forget('company_footer_data');
        });
    }
}
