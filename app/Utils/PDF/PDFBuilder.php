<?php

namespace App\Utils\PDF;

use Carbon\Carbon;
use Mpdf\Mpdf;

abstract class PDFBuilder {
    protected readonly Mpdf $pdf;
    protected array $images = [];
    public function __construct() {
        $this->pdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 5,
            'margin_bottom' => 5,
            'margin_header' => 5,
            'setAutoTopMargin' => 'pad',
            'margin_footer' => 5,
            'setAutoBottomMargin' => 'pad',
            'showWatermarkImage' => true,
            'fontDir' => [__DIR__ . '/assets/fonts'],
            'fontdata' => [
                'montserrat' => [
                    'R' => 'Montserrat-Regular.ttf',
                    'B' => 'Montserrat-Bold.ttf',
                    'I' => 'Montserrat-Italic.ttf',
                    'BI' => 'Montserrat-BoldItalic.ttf'
                ]
            ],
            'default_font' => 'montserrat',
            'exposeVersion' => false,
        ]);

        $date = Carbon::now('Europe/Warsaw')->format('d/m/Y H:i:s');
        $this->pdf->SetHTMLHeader(<<<EOD
<table width="100%" style="border-bottom: 1px solid black; padding-bottom: 8px">
    <tr>
        <td width="48px"><img src="var:logo" alt="" style="width: 48px"></td>
        <td><h1 style="font-size: 20px; line-height: 1">Platforma<br />Egzaminacyjna</h1></td>
        <td style="text-align: right; font-size: 14px"><p>Wygenerowano:<br>$date</p></td>
    </tr>
</table>
EOD
        );
        $this->pdf->SetHTMLFooter(<<<EOD
<table width="100%" style="border-top: 1px solid black; padding-top: 8px">
    <tr>
        <td style="text-align: center">{PAGENO}/{nbpg}</td>
</tr>
</table>
EOD
        );

        $this->pdf->SetWatermarkImage(__DIR__ . '/assets/PROLIFE.png', 0.05, 'F');
        $this->pdf->imageVars['logo'] = file_get_contents(__DIR__ . '/assets/logo.png');
        $this->pdf->setCreator('PROLIFE Platforma Szkoleniowa');
        $this->pdf->setAuthor('PROLIFE Platforma Szkoleniowa');
    }

    abstract protected function getFullHTML();

    public final function getFile($filename, $destination = 'I') {
        $html = $this->getFullHTML();

        foreach ($this->images as $uuid => $image) {
            $this->pdf->imageVars[$uuid] = file_get_contents($image);
        }

        $this->pdf->writeHTML($html);

        $this->pdf->Output($filename, $destination);
    }
}
