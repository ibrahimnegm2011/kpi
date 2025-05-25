<x-mail::message>
# You're Invited to Join {{ config('app.name') }}

Hello {{ $user->name }},

You have been invited to register as an agent for Account **{{ $account->name }}**.
Below is a list of all your current assignments for this account:

@foreach($user->agent_assignments as $assignment)
- **Company:** {{ $assignment->company->name }} | **Department:** {{ $assignment->department->name }}
@endforeach

To get started, click the button below to complete your registration:

<x-mail::button :url="$url">
    Register Now
</x-mail::button>

If you did not expect this invitation, you can safely ignore this email.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
