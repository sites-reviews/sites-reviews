<tr>
    <td class="header">
        <table style="width: auto; margin:auto;">
            <tr>
                <td>
                    <a href="{{ $url }}" style="display: block; vertical-align: middle; text-align: right">
                        <img class="logo_svg" src="{{ asset('/img/logo.svg') }}" />
                    </a>
                </td>
                <td>
                    <a href="{{ $url }}" style="display: block; vertical-align: middle; text-align: center; margin-bottom: 0.2rem;">
                        {{ $slot }}
                    </a>
                </td>
            </tr>
        </table>
    </td>
</tr>
