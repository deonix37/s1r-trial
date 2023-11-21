<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>KB</title>
@vite('resources/css/app.css')
@vite('resources/js/lead-create.js')
</head>
<body>
    <div class="container mt-4">
        <h1>Информация о клиенте</h1>
        @if (session('lead_message'))
            <div class="alert {{ session('lead_success') ? 'alert-success' : 'alert-danger' }}">
                {{ session('lead_message') }}
            </div>
        @endif
        <form action="{{ route('leads.store') }}" method="post">
            @csrf
            <div class="mb-2">
                <label class="form-label" for="full_name">ФИО клиента</label>
                <input id="full_name" class="form-control" name="full_name" value="{{ old('full_name') }}" required>
                @error('full_name') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="mb-2">
                <label class="form-label" for="birth_date">Дата рождения клиента</label>
                <input id="birth_date" class="form-control" name="birth_date" value="{{ old('birth_date') }}" type="date" required>
                @error('birth_date') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="mb-2">
                <label class="form-label" for="phone">Телефон клиента</label>
                <input id="phone" class="form-control" name="phone" value="{{ old('phone') }}" required>
                @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="mb-2">
                <label class="form-label" for="email">Электронная почта клиента</label>
                <input id="email" class="form-control" name="email" value="{{ old('email') }}" type="email" required>
                @error('email') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="mb-2">
                <label class="form-label" for="comment">Комментарий</label>
                <textarea id="comment" class="form-control" name="comment">{{ old('comment') }}</textarea>
                @error('comment') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <button type="submit" class="btn btn-primary">Отправить</button>
        </form>
    </div>
</body>
</html>
