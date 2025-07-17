<x-mail::message>
Hello {{ $user->name }},

You have KPIs assigned for <strong>{{ \Carbon\Carbon::create()->month($month)->format('F') }} {{ $year }}</strong> ready to be submitted for the following accounts:

<table style="width:75%; margin: 16px auto; border-collapse: collapse;">
    <thead>
    <tr>
        <th scope="col" style="border:1px solid #dddddd; padding:8px; text-align:left;">Account</th>
        <th scope="col" style="border:1px solid #dddddd; padding:8px; text-align:left;">KPIs Count</th>
    </tr>
    </thead>
    <tbody>
    @foreach($accounts as $data)
        <tr>
            <td style="border:1px solid #dddddd; padding:8px;">{{ $data['account'] }}</td>
            <td style="border:1px solid #dddddd; padding:8px;">{{ $data['count'] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<x-mail::button :url="config('app.url')">
    Log in & Submit
</x-mail::button>

Please review your forecasts as soon as possible.

Thank you,<br>
{{ config('app.name') }}
</x-mail::message>
