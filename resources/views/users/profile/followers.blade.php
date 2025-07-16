@extends('layouts.app')

@section('title', 'Followers')

@section('content')
    @include('users.profile.header')
    {{-- show all followers here --}}
    <div class="container" style="margin-top: 100px">
        @if ($followers->isNotEmpty())
            <div class="row justify-content-center text-center">
                <div class="h2">Followers</div>
                @foreach ($followers as $follow)
                    <div class="row my-2 align-items-center">

                        <div class="col-4">
                            @if ($follow->follower->avatar)
                                <img src="{{ $follow->follower->avatar }}" class="rounded-circle avatar-sm"
                                    alt="{{ $follow->follower->name }}">
                            @else
                                <i class="fa-solid fa-circle-user text-dark icon-sm"></i>
                            @endif
                        </div>

                        <div class="col-4">
                            <a href="{{ route('profile.show', $follow->follower->id) }}"
                                class="text-decoration-none text-start text-dark fw-bold">
                                <p>{{ $follow->follower->name }}</p>
                            </a>
                        </div>
                        {{-- If you are not the owner of the post, show an Unfollow button. --}}
                        <div class="col-4">
                            @if ($follow->follower->id !== Auth::user()->id)
                                @if ($follow->follower->isFollowed())
                                    <form action="{{ route('follow.destroy', $follow->follower->id) }}" method="post"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="border-0 bg-transparent p-0 text-secondary">Following</button>
                                    </form>
                                @else
                                    <form action="{{ route('follow.store', $follow->follower->id) }}" method="post">
                                        @csrf
                                        <button type="submit" class="border-0 bg-transparent p-0 text-dark">Follow</button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <h3 class="text-muted text-center">No Followers Yet</h3>
        @endif
    </div>
@endsection
