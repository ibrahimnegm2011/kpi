<x-mail::message>
# Welcome to {{ config('app.name') }}!

Hi {{ $user->name }},

Your account has been created as an agent for the account **{{ $account->name }}**.

## Your Assignments

<table style="width:75%; margin: 16px auto; border-collapse: collapse;">
    <thead>
    <tr>
        <th style="border:1px solid #dddddd; padding:8px; text-align:left;">Company</th>
        <th style="border:1px solid #dddddd; padding:8px; text-align:left;">Department</th>
    </tr>
    </thead>
    <tbody>
    @foreach($user->agent_assignments as $assignment)
        <tr>
            <td style="border:1px solid #dddddd; padding:8px;">{{ $assignment->company->name }}</td>
            <td style="border:1px solid #dddddd; padding:8px;">{{ $assignment->department->name }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

To activate your account and set your password, please click the button below:

<x-mail::button :url="$url">
    Set Your Password
</x-mail::button>

If you did not expect this invitation, you can safely ignore this email.

Thank you,<br>
{{ config('app.name') }}
</x-mail::message>
