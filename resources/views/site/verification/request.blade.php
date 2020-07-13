@extends('layouts.app')

@section('content')

    <div class="card mb-3">
        <div class="card-header">
            Верификация с помощью файла на сервере
        </div>
        <div class="card-body">

            В корне сайта создайте файл с именем

            <a href="{{ route('sites.verification.check.file.download', $site) }}">{{ $proof->file_path }}</a>

            и со следующим содержимым:

            <textarea class="form-control my-2">{{ $proof->file_code }}</textarea>

            Убедитесь, что файл по адресу
            <a href="{{ $proof->getFileUrl() }}">{{ $proof->getFileUrl() }}</a>
            открывается

            <div>
                <a href="{{ route('sites.verification.check.file', $site) }}" class="btn btn-primary">
                    {{ __('verification.check') }}
                </a>
            </div>

            @if ($errors->check_file->has('error'))
                <div class="alert alert-danger mb-0 mt-2">
                    {{ $errors->check_file->first('error') }}
                </div>
            @endif

        </div>
    </div>

    <div class="card">
        <div class="card-header">
            Верификация с помощью DNS
        </div>
        <div class="card-body">

            Добавьте в DNS домена test.com такую TXT-запись:

            <textarea class="form-control my-2">{{ config('verification.dns_key_name') }}: {{ $proof->dns_code }}</textarea>

            Убедитесь, что правильно выбрали домен!
            Если в сервисе домен с www, а вы добавили запись для домена без www (или наоборот), то подтверждение не сработает.
            Обновление записей DNS может занимать достаточно долгое время. Нажмите на кнопку «Проверить» после того, как записи обновятся.

            <div>
                <a href="{{ route('sites.verification.check.dns', $site) }}" class="btn btn-primary">
                    {{ __('verification.check') }}
                </a>
            </div>

            @if ($errors->check_dns->has('error'))
                <div class="alert alert-danger mb-0 mt-2">
                    {{ $errors->check_dns->first('error') }}
                </div>
            @endif

        </div>
    </div>

@endsection
