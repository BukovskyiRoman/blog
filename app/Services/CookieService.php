<?php

namespace App\Services;

use App\Services\Interfaces\CookieServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CookieService implements CookieServiceInterface
{
    public function setGuestCookie($request): int
    {
        if ($request->hasCookie('guest')) {     //@todo logic check needed
            $id = $request->cookies->get('guest');
        } else {
            $id = DB::table('visitors')->max('visitor_id');

            if ($id === null) {
                $id = 1;
                DB::table('visitors')->updateOrInsert([
                    'visitor_id' => $id,
                ]);
            } else {
                $id++;
                DB::table('visitors')->updateOrInsert([
                    'visitor_id' => $id,
                ]);
            }
        }
        return $id;
    }
}
