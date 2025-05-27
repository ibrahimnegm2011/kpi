@props(['url'])
<tr>
<td class="header" valign="middle" align="center">
<a href="{{ $url }}" style="vertical-align: middle; display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
@else
{{ $slot }}

<table width="240" style="margin: 0px auto; font-family: inheret">
<tr>
<td height="50" align="center" valign="middle">
<a href="{{ $url }}"><img src="{{url('images/logo.png')}}" style="vertical-align: middle;" height="50" width="114" valign="middle"/></a>
</td>
</tr>
<tr>
<td align="center" valign="middle"><a href="{{ $url }}">{{ config('app.name') }}</a></td>
</tr>
</table>
@endif
</a>
</td>
</tr>
