<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Rules\MatchOldPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function changeStatus(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $user->moderator = $request->status;
        $user->save();

        return response()->json(['message' => 'User status updated successfully.']);
    }


    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show()
    {
        $user = User::where('id', Auth::user()->id)->with('image', 'posts')->first();
        $users = User::all();
        return view('profile', compact('user', 'users'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => ['required', new MatchOldPassword()],
            'new_password' => ['required'],
            'confirm_new_password' => ['same:new_password']
        ]);

        User::find(auth()->user()->id)->update(['password' => Hash::make($request->new_password)]);
        return redirect()->back();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeUserName(Request $request)
    {
        $request->validate([
           'name' => 'required|max:30|alpha_dash'
        ]);

        User::find(auth()->user()->id)->update([
            'name' => $request->name
        ]);

        return redirect()->back();
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function showAdminPanel()
    {
        $users = User::all();
        $user = User::where('id', Auth::user()->id)->with('image', 'posts')->first();

        return view('admin-panel', compact('users', 'user'));
    }
}
