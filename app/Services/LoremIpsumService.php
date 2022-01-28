<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class LoremIpsumService
{
    public function getLoremIpsumText(): string
    {
        $response = Http::get('https://loripsum.net/api');
        if ($response->status() == 200) {
            return $response->body();
        }
        return 'Lorem ipsum service are dead ;)';
    }
}
