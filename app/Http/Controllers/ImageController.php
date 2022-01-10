<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'avatar' => 'required|file|image|mimes:jpeg,png,gif,jpg|max:2048'
        ]);


        $path = $validated['avatar']->store('avatars/' . $request->user()->id, 's3');
        Storage::disk('s3')->setVisibility($path, 'public');

        Image::create([
            'name' => basename($path),
            'url' => Storage::disk('s3')->url($path),
            'user_id' => $request->user()->id,
        ]);

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        dd('show image');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        dd($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        Storage::disk('s3')->delete('avatars/' . $request->user()->id . '/' . $request->user()->image->name);

        $path = $request->file('avatar')->store('avatars/' . $request->user()->id, 's3');

        Image::where('id', $id)->update([
            'name' => basename($path),
            'url' => Storage::disk('s3')->url($path),
            'user_id' => $request->user()->id,
        ]);

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
