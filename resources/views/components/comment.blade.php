<div class="comment" data-id="{{ $comment->id }}">
    <div class="card mb-2">
        <div class="card-body d-flex flex-row py-2 px-3">
            <div class="mr-3 text-center flex-shrink-1" style="width:50px;">
                <x-user-avatar :user="$comment->create_user" width="50" height="50" quality="90"/>
            </div>
            <div class="w-100">
                <div class="mb-2 d-flex flex-row align-items-center">
                    <div class="d-flex flex-wrap align-items-center">
                        <div class="mr-2">
                            <x-user-name :user="$comment->create_user"/>
                        </div>

                        @if ($comment->isCreatorIsSiteOwner())
                            <span class="mr-2 badge badge-pill badge-secondary">{{ __('Owner') }}</span>
                        @endif
                    </div>
                    <div class="ml-auto small  text-right">
                        <x-time :time="$comment->created_at"/>
                    </div>
                </div>

                <div class="mb-1 mr-1">
                    {{ $comment->text }}
                </div>
            </div>
        </div>

        <div class="card-footer py-2 px-3 buttons">
            <div class="d-flex flex-row align-items-center">
                <a href="{{ route('comments.rate.up', $comment) }}"
                   class="rate_up btn btn-light btn-sm @if ($comment->getAuthUserVote() > 0) active @endif">
                    <i class="far fa-thumbs-up"></i>
                </a>

                <div class="px-1 small rating" style="@if ($comment->rating == 0) display:none; @endif">{{ $comment->rating }}</div>

                <a href="{{ route('comments.rate.down', $comment) }}"
                   class="rate_down btn btn-light btn-sm @if ($comment->getAuthUserVote() < 0) active @endif">
                    <i class="far fa-thumbs-down"></i>
                </a>

                @can('reply', $comment)
                    <a href="{{ route('comments.replies.create', $comment) }}" class="reply btn btn-light btn-sm">
                        {{ __('Reply') }}
                    </a>
                @endcan

                <a href="{{ route('comments.children', $comment) }}"
                   @if ($comment->children_count < 1) style="display: none;" @endif
                   class="toggle_children btn btn-light btn-sm">
                    <span class="show_children">{{ __('Show replies') }}</span>
                    <span class="hide_children" style="display: none;">{{ __('Hide replies') }}</span>
                    <span class="count">{{ $comment->children_count }}</span>
                </a>

                @can('edit', $comment)
                    <a href="{{ route('comments.edit', $comment) }}" class="btn btn-light btn-sm">
                        {{ __('Edit') }}
                    </a>
                @endcan

                <a href="{{ route('comments.destroy', $comment) }}" class="delete btn btn-light btn-sm"
                   style="@cannot('delete', $comment) display:none; @endcannot">
                    {{ __('Delete') }}
                </a>

                <a href="{{ route('comments.destroy', $comment) }}" class="restore btn btn-light btn-sm"
                   style="@cannot('restore', $comment) display:none; @endcannot">
                    {{ __('Restore') }}
                </a>

            </div>
        </div>
    </div>

    <div class="descendants pl-3">
        @if (!empty($descendants))
            @foreach ($descendants as $descendant)
                @if (preg_match('/\/'.$comment->id.'\/$/i', $descendant->tree))
                    <x-comment :comment="$descendant" :descendants="$descendants"/>
                @endif
            @endforeach
        @endif
    </div>
</div>

