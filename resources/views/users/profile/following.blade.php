@extends('layouts.app')

@section('title', 'Following')

@section('content')
    @include('users.profile.header')
    {{-- show all followers here --}}
    <div class="container" style="margin-top: 100px">
        @if ($following->isNotEmpty())
            <div class="row justify-content-center text-center">
                <div class="h2">Following</div>
                @foreach ($following as $follow)
                    @if ($follow->following->id !== Auth::user()->id)
                        <div class="row my-2">
                            <div class="col-4">
                                @if ($follow->following->avatar)
                                    <img src="{{ $follow->following->avatar }}" class="rounded-circle avatar-sm"
                                        alt="{{ $follow->following->name }}">
                                @else
                                    <i class="fa-solid fa-circle-user text-dark icon-sm"></i>
                                @endif
                            </div>

                            <div class="col-4">
                                <a href="{{ route('profile.show', $follow->following->id) }}"
                                    class="text-decoration-none text-start text-dark fw-bold">
                                    <p>{{ $follow->following->name }}</p>
                                </a>
                            </div>
                            {{-- If you are not the owner of the post, show an Unfollow button. --}}
                            <div class="col-4">
                                @if ($follow->following->isFollowed())
                                    <form action="{{ route('follow.destroy', $follow->following->id) }}" method="post"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="border-0 bg-transparent p-0 text-secondary">Following</button>
                                    </form>
                                @else
                                    <form action="{{ route('follow.store', $follow->following->id) }}" method="post">
                                        @csrf
                                        <button type="submit" class="border-0 bg-transparent p-0 text-dark">Follow</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @else
            <h3 class="text-muted text-center">No Following Yet</h3>
        @endif
    </div>

@endsection
