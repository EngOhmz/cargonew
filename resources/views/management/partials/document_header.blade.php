<div>
    <h4 align="center" style="margin-top: 0%;" class="fontS">{{ $docHeaderLine1 ?? 'TUNASAFRISHA MIZIGO KUTOKA DAR ES SALAAM' }}</h4>
    <h4 align="center" class="" style="margin-top: 0%; font-size:9px;" class="fontS">{{ $docHeaderLine2 ?? 'KWENDA NZEGA,KAHAMA,ISAKA,MASUMBWE, NA LUGUNGA' }}</h4>
    @if(!empty($tinNumber))
        <p align="center" style="margin-top: 0%; font-size:10px; font-weight: bold;">TIN: {{ $tinNumber }}</p>
    @endif
    <table style=" border: none !important; font-family: 'Courier New', Courier, monospace !important; font-size:10px; font-weight: bold;">
        <tbody>
            <tr>
                <td align="">{{ $docOffice1Label ?? 'HEAD OFFICE' }}</td>
                <td align="">{{ $docOffice2Label ?? 'BRANCH' }}</td>
                <td align="">{{ $docOffice3Label ?? 'BRANCH' }}</td>
            </tr>
            <tr>
                <td align="">{{ $docOffice1Place ?? 'DAR ES SALAAM' }}</td>
                <td align="">{{ $docOffice2Place ?? 'KAHAMA' }}</td>
                <td align="">{{ $docOffice3Place ?? 'NZEGA' }}</td>
            </tr>
            <tr>
                <td align="" class="phone-number">{{ $docOffice1Contact ?? '07xx xxx xxx' }}</td>
                <td align="" class="phone-number">{{ $docOffice2Contact ?? '07xx xxx xxx' }}</td>
                <td align="" class="phone-number">{{ $docOffice3Contact ?? '07xx xxx xxx' }}</td>
            </tr>
            <tr>
                <td align="" class="phone-number">{{ $docOffice1Contact2 ?? '' }}</td>
                <td align=""></td>
                <td align=""></td>
            </tr>
            <tr>
                <td align="" class="phone-number">{{ $docOffice1Contact3 ?? '' }}</td>
                <td align=""></td>
                <td align=""></td>
            </tr>
        </tbody>
    </table>
</div>
