<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;
use Illuminate\Support\Facades\Http;

class WeatherWidget extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    //public $reloadTimeout = 10;

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        $response = Http::get('http://api.weatherstack.com/current?access_key=5f11e7cad598ae0033098a665014a354&query=Kirovograd');
        $data = $response->body();
        $data = json_decode($data);

//        dd(gettype($data->success));
        if ($data->success) {
            $data = $data->current;
        }

        return view('widgets.weather_widget', [
            'config' => $this->config,
            'weather' => $data,
        ]);
    }
}
