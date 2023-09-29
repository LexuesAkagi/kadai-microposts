@if (Auth::user()->is_favorite($micropost->id))
    <form method="POST" action="{{ route('favorite.unfavorite', $micropost->id) }}">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-error btn-block normal-case" >お気に入り取消</button>
    </form>
@else
    {{-- フォローボタンのフォーム --}}
    <form method="POST" action="{{ route('favorite.favorite', $micropost->id) }}">
        @csrf
        <button type="submit" class="btn btn-primary btn-block normal-case">お気に入り</button>
    </form>
@endif
