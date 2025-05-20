<x-mail::message>
# You're Invited to Join KPIs Management System

Hello {{ $user->name }},

You have been invited to register as a representative of **{{ $user->company->name }}** on our system.

To get started, click the button below to complete your registration:

<x-mail::button :url="$url">
    Register Now
</x-mail::button>

If you did not expect this invitation, you can safely ignore this email.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
