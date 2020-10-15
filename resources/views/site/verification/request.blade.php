@extends('layouts.app')

@section('content')

    @isset ($userOwner)

        <div class="alert alert-success">
            {{ __("Verification completed") }}
        </div>

    @else

        <div class="card mb-3">
            <div class="card-body">
                {{ __('By verifying the site, you confirm that you are the owner of the site and get the opportunity to manage it on our site, as well as to contact users on behalf of your site.') }}
                {{ __('All users will see a mark in your comments that indicates the official source of information.') }}
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header ">
                {{ __('Verification using a file on the server') }}
            </div>
            <div class="card-body">

                <div class="mb-3">
                    {{ __('Create a file with the following name in the site root') }}

                    <a href="{{ route('sites.verification.check.file.download', $site) }}" class="text-info" target="_blank">
                        {{ $proof->file_path }}</a>

                    {{ __('and with the following content') }}:

                    <textarea class="form-control my-2">{{ $proof->file_code }}</textarea>

                    {{ __('Make sure that the file opens at') }}:
                    <a href="{{ $proof->getFileUrl() }}" class="text-info" target="_blank">
                        {{ $proof->getFileUrl() }}</a>
                    . {{ __("Click on this link and if you don't see any errors, it means that the file is placed correctly") }}
                </div>

                <div>
                    <a href="{{ route('sites.verification.check.file', $site) }}" class="btn btn-primary">
                        {{ __('Check') }}
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
                {{ __('Verification using DNS') }}
            </div>
            <div class="card-body">

                <div class="mb-3">

                    {{ __("Add to the domain's DNS :domain such a TXT record", ['domain' => $site->domain]) }}:

                    <textarea class="form-control my-2">{{ config('verification.dns_key_name') }}: {{ $proof->dns_code }}</textarea>

                    {{ __('Make sure that you have chosen the correct domain!') }}
                    {{ __('If the service has a domain with www, but you added an entry for a domain without www, the confirmation will not work.') }}
                    {{ __("Updating DNS records can take quite a long time.") }}
                    {{ __('Click on the "Check" button after the entries are updated.') }}
                </div>

                <div>
                    <a href="{{ route('sites.verification.check.dns', $site) }}" class="btn btn-primary">
                        {{ __('Check') }}
                    </a>
                </div>

                @if ($errors->check_dns->has('error'))
                    <div class="alert alert-danger mb-0 mt-2">
                        {{ $errors->check_dns->first('error') }}
                    </div>
                @endif

            </div>
        </div>

    @endisset

@endsection
