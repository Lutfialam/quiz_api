<?php

namespace App\Repository;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public function uploadImage(Request $request)
    {
        $imageName = 'default.png';
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension();

            $imageName = "USER-" . Carbon::now()->timestamp . Str::random(8) . '.' . $extension;
            $image->move(public_path('images'), $imageName);
        }

        return $imageName;
    }

    public function create(Request $request)
    {
        $imageName = $this->uploadImage($request);

        $user = User::create([
            'image'     => $imageName,
            'name'      => $request->name,
            'email'     => $request->email,
            'level'     => $request->level,
            'password'  => Hash::make($request->password),
        ]);

        return $user;
    }
}
