<?php

namespace App\Http\Controllers;

use App\Events\UserLike;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function likePost(Request $request)
    {
        if ($request->cookies->has('guest')) {
            $visitorId = $request->cookies->get('guest');

            if (
                !(Like::where([
                ['visitor_id', '=', $visitorId],
                ['post_id', '=', $request->post_id]
                ])->exists())
            ) {
                Like::create([
                    'visitor_id' => $visitorId,
                    'post_id' => $request->post_id
                ]);
            }
        } else {
            if (
                !(Like::where([
                ['user_id', '=', $request->user_id],
                ['post_id', '=', $request->post_id]
                ])->exists())
            ) {
                Like::create([
                    'user_id' => $request->user_id,
                    'post_id' => $request->post_id
                ]);
            }
        }
    }

    public function likeComment(Request $request)
    {
        Auth::check() ? $userForm = 'user_id' : $userForm = 'visitor_id';
        Auth::check() ? $id = $request->user_id : $id = $request->cookies->get('guest');

        if (
            !(Like::where([
            [$userForm, '=', $id],
            ['comment_id', '=', $request->comment_id]
            ])->exists())
        ) {
            $user = Like::create([
                $userForm => $id,
                'comment_id' => $request->comment_id
            ]);

            event(new UserLike($user));
        }
    }
}
