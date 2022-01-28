
<h1>Widget widget</h1>
<table class="table table-striped">
    <tr>
        <td class="table-primary">Temperature</td>
        <td class="table-secondary">Fills like</td>
        <td class="table-success">Wind speed</td>
    </tr>
    <tr>
        <td class="table-primary">{{$weather->success ? $weather->temperature : 'n/a'}}</td>
        <td class="table-secondary">{{$weather->success ? $weather->feelslike : 'n/a'}}</td>
        <td class="table-success">{{$weather->success ? $weather->wind_speed : 'n/a '}}m/s</td>
    </tr>
</table>


