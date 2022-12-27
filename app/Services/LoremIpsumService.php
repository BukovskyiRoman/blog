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

//        $data = ["apikey" => "Zl0WwpI4RBzQrgGZGQBrCT1aWb4Vu2rAU", "calledMethod" => "get"];
//        $data_string = json_encode ($data, JSON_UNESCAPED_UNICODE);
//
//        $curl = curl_init("https://api.endorphone.com.ua/1.0/stock");
//
//        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
//        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
//
//        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
//                'Content-Type: application/json',
//                'Content-Length: ' . strlen($data_string))
//        );
//        $result = curl_exec($curl);
//        curl_close($curl);
//
//        dd($result);
    }
}
