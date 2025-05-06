@extends('layouts.app')

@section('content')
<div class="container">
    <h2>プロフィール編集</h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('mypage.update') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">氏名</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            @error('name') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">住所</label>
            <input type="text" name="address" class="form-control" value="{{ old('address', $user->address) }}">
            @error('address') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">電話番号</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
            @error('phone') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-primary">更新</button>
    </form>
</div>
@endsection
