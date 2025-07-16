<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Follow;

class ProfileController extends Controller
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function show($id)
    {
        $user = $this->user->findOrFail($id);

        return view('users.profile.show')->with('user', $user);
    }

    public function edit()
    {
        $user = $this->user->findOrFail(Auth::user()->id);

        return view('users.profile.edit')->with('user', $user);
    }

    public function update(Request $request, $id)
    {
        # 1. validate
        $request->validate([
            'name' => 'required|max:50',
            'email' => 'required|email|max:50|unique:users,email,' . Auth::user()->id,
            'avatar' => 'mimes:jpeg,jpg,png,gif|max:1048',
        ]);

        # 2. update the post
        $user = $this->user->findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->introduction = $request->introduction;

        // If there is a new image...
        if ($request->avatar) {
            $user->avatar = 'data:image/' . $request->avatar->extension() . ';base64,' . base64_encode(file_get_contents($request->avatar));
        }

        $user->save();

        # 5. Redirect show post page
        return redirect()->route('profile.show', $id);

    }

    public function followers($id)
    {
        $user = $this->user->findOrFail($id);

        //$followers = $user->followers()->with('follower')->get();
        $followers = $user->followers()->with('follower')->get();

        return view('users.profile.followers')
            ->with('user', $user)
            ->with('followers', $followers);
    }

    public function following($id)
    {
        $user = $this->user->findOrFail($id);

        $following = $user->following()->with('following')->get();

        return view('users.profile.following')
            ->with('user', $user)
            ->with('following', $following);
    }
}
