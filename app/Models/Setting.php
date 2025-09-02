<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';

    protected $fillable = [
        'key',
        'name',
        'value',
    ];


    public static function getValue(string $key, $default = null)
    {
        //モデルのクラスメソッド内でそのモデル自身（または継承先のモデル）を指す場合は、モデル名を直接書くよりも static:: を使うのがベストプラクティスです。
        //これは主に、PHPの遅延静的バインディング (Late Static Bindings) という機能と、オブジェクト指向プログラミングにおける継承の概念に関係しています。
        //ただ、この場合は、Setting::where('key', $key)->value('value')でも可ですが
        return static::where('key', $key)->value('value') ?? $default;
    }

}
