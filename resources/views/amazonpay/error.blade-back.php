<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>決済エラー - CCMedico Shop</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8">
        <div class="text-center">
            <!-- エラーアイコン -->
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            
            <!-- タイトル -->
            <h1 class="text-2xl font-bold text-gray-900 mb-2">決済エラー</h1>
            
            <!-- エラーメッセージ -->
            <div class="mb-6">
                @if(session('error'))
                    <p class="text-red-600 mb-4">{{ session('error') }}</p>
                @endif
                
                <p class="text-gray-600 mb-2">
                    申し訳ございませんが、決済処理中にエラーが発生いたしました。
                </p>
                <p class="text-sm text-gray-500">
                    しばらく時間をおいて再度お試しいただくか、<br>
                    お困りの場合はお客様サポートまでお問い合わせください。
                </p>
            </div>

            <!-- デバッグ情報（開発環境のみ） -->
            @if(config('app.debug') && request()->has('debug'))
                <div class="bg-gray-100 border border-gray-300 rounded p-4 mb-6 text-left">
                    <h3 class="font-semibold text-gray-700 mb-2">デバッグ情報:</h3>
                    <div class="text-sm text-gray-600 space-y-1">
                        <p><strong>URL:</strong> {{ request()->fullUrl() }}</p>
                        <p><strong>時刻:</strong> {{ now()->format('Y-m-d H:i:s') }}</p>
                        @if(request()->query())
                            <p><strong>パラメータ:</strong></p>
                            <pre class="bg-white p-2 rounded border text-xs overflow-auto">{{ json_encode(request()->query(), JSON_PRETTY_PRINT) }}</pre>
                        @endif
                    </div>
                </div>
            @endif

            <!-- アクションボタン -->
            <div class="space-y-3">
                <a href="{{ route('amazon-pay.payment') }}" 
                   class="w-full inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                    もう一度決済する
                </a>
                
                <a href="{{ route('products.index') }}" 
                   class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                    ホームに戻る
                </a>
            </div>

            <!-- サポート情報 -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <p class="text-sm text-gray-500 mb-2">お困りの場合は</p>
                <div class="space-y-1">
                    <p class="text-sm">
                        <strong>Email:</strong> 
                        <a href="mailto:support@ccmedico.dev" class="text-blue-600 hover:text-blue-800">
                            support@ccmedico.dev
                        </a>
                    </p>
                    <p class="text-sm">
                        <strong>営業時間:</strong> 平日 9:00-18:00
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>