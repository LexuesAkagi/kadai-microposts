
@if (isset($microposts))
    <ul class="list-none">
        @foreach ($microposts as $micropost)
            <li class="flex items-center gap-x-2 mb-4">
                {{-- ユーザのメールアドレスをもとにGravatarを取得して表示 --}}
                <div class="avatar">
                    <div class="w-12 rounded">
                        <img src="{{ Gravatar::get($micropost->user->email) }}" alt="" />
                    </div>
                </div>
                <div>
                    <div>
                        {{-- 投稿の所有者のユーザ詳細ページへのリンク --}}
                        <a class="link link-hover text-info" href="{{ route('users.show', $micropost->user->id) }}">{{ $micropost->user->name }}</a>
                        <span class="text-muted text-gray-500">posted at {{ $micropost->created_at }}</span>
                    </div>
                    <div>
                        {{ $micropost->content }}
                    </div>
                </div>
                @include('favorites.favorites_button')
            </li>
        @endforeach
    </ul>
    {{-- ページネーションのリンク --}}
    {{ $microposts->links() }}
@endif