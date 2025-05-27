<x-mail::message>
# Your {{ config('app.name') }} Account Is Ready

Hello {{ $user->name }},

An account has been created for you on the {{ config('app.name') }} as an account administrator.

To activate your account and set your password, please click the button below:

<x-mail::button :url="$url">
    Set Your Password
</x-mail::button>

If you did not request this account or believe this message is a mistake, you can safely ignore this email.

Thank you,<br>
{{ config('app.name') }}
</x-mail::message>
