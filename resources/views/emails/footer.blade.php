<!-- Salutation -->
<p style="{{ $style['paragraph'] }}">
        Gracias.<br /><br />

        Equipo <a href="{{ env('APP_URL') }}">{{ Config::get('app.name') }}</a>
</p>

</td>
</tr>
</table>
</td>
</tr>

<!-- Footer -->
<tr>
<td style="background-color: #343434;">
<table style="{{ $style['email-footer'] }}" align="center" width="570" cellpadding="0" cellspacing="0">
<tr>
<td style="{{ $fontFamily }} {{ $style['email-footer_cell'] }}">
<p style="{{ $style['paragraph-sub-footer'] }}">
    @Lang('messages.Copyright')
</p>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</body>
</html>
