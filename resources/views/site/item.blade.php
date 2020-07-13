<div class="card mb-2">
    <div class="card-body px-3 py-2 d-flex flex-row">

        <div class="flex-shrink-1 text-center mr-3 mb-3" style="width:110px;">
            <x-site-preview :site="$site" width="100" height="100" url="0" showImageUpdateSoonText="0" />
        </div>

        <div class="w-100">

            <a class="h6" href="{{ route('sites.show', $site->domain) }}">{{ $site->title }} | {{ $site->domain }}</a> <br/>

            {{ $site->description }}

            <div class="d-flex flex-row align-items-center">
                <div class="mr-3 ">
                    <x-site-rating :rating="$site->rating"/>
                </div>

                <div class="mr-3">
                    {{ $site->rating }}/5
                </div>

                <div class="small">
                    {{ $site->number_of_reviews }}
                    {{ trans_choice('site.reviews', $site->number_of_reviews) }}
                </div>
            </div>
        </div>
    </div>
</div>
