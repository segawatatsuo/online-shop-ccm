{{-- resources/views/admin/email_templates/index.blade.php --}}
@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h1>メールテンプレート管理</h1>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>スラッグ</th>
                    <th>件名</th>
                    <th>ステータス</th>
                    <th>更新日</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach($templates as $template)
                <tr>
                    <td>{{ $template->slug }}</td>
                    <td>{{ Str::limit($template->subject, 50) }}</td>
                    <td>
                        @if($template->is_active)
                            <span class="badge badge-success">有効</span>
                        @else
                            <span class="badge badge-secondary">無効</span>
                        @endif
                    </td>
                    <td>{{ $template->updated_at->format('Y/m/d H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.email-templates.show', $template->id) }}" class="btn btn-sm btn-info">詳細</a>
                        <a href="{{ route('admin.email-templates.edit', $template->id) }}" class="btn btn-sm btn-primary">編集</a>
                        <a href="{{ route('admin.email-templates.preview', $template->id) }}" class="btn btn-sm btn-secondary" target="_blank">プレビュー</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

{{-- resources/views/admin/email_templates/edit.blade.php --}}
@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h1>メールテンプレート編集</h1>
    
    <form method="POST" action="{{ route('admin.email-templates.update', $template->id) }}">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="slug">スラッグ</label>
            <input type="text" class="form-control" value="{{ $template->slug }}" readonly>
        </div>
        
        <div class="form-group">
            <label for="subject">件名 <span class="text-danger">*</span></label>
            <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" 
                   value="{{ old('subject', $template->subject) }}" required>
            @error('subject')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="body">本文 <span class="text-danger">*</span></label>
            <textarea name="body" class="form-control @error('body') is-invalid @enderror" 
                      rows="20" required>{{ old('body', $template->body) }}</textarea>
            @error('body')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-check">
            <input type="checkbox" name="is_active" class="form-check-input" 
                   @if(old('is_active', $template->is_active)) checked @endif>
            <label class="form-check-label">有効</label>
        </div>
        
        <div class="mt-3">
            <button type="submit" class="btn btn-primary">更新</button>
            <a href="{{ route('admin.email-templates.index') }}" class="btn btn-secondary">戻る</a>
            <a href="{{ route('admin.email-templates.preview', $template->id) }}" class="btn btn-info" target="_blank">プレビュー</a>
        </div>
    </form>
    
    <div class="mt-4">
        <h3>使用可能な変数</h3>
        <div class="row">
            <div class="col-md-6">
                <ul>
                    <li><code>{{customer_name}}</code> - 顧客名</li>
                    <li><code>{{order_id}}</code> - 注文ID</li>
                    <li><code>{{order_number}}</code> - 注文番号</li>
                    <li><code>{{order_date}}</code> - 注文日時</li>
                    <li><code>{{order_items_table}}</code> - 注文商品テーブル</li>
                </ul>
            </div>
            <div class="col-md-6">
                <ul>
                    <li><code>{{total_price}}</code> - 商品合計</li>
                    <li><code>{{shipping_fee}}</code> - 送料</li>
                    <li><code>{{grand_total}}</code> - 合計金額</li>
                    <li><code>{{delivery_name}}</code> - 配送先名前</li>
                    <li><code>{{delivery_postal_code}}</code> - 配送先郵便番号</li>
                    <li><code>{{delivery_address}}</code> - 配送先住所</li>
                    <li><code>{{delivery_phone}}</code> - 配送先電話番号</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection