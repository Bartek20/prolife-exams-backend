<?php

namespace App\Utils\PDF;

use Mpdf\Mpdf;

class CertificateStopTheBleed {
    private readonly Mpdf $pdf;
    private readonly array $field;
    public function __construct() {
        $this->pdf = new Mpdf([
            'format' => [215, 279],
            'orientation' => 'L',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'fontDir' => [__DIR__ . '/assets/fonts'],
            'fontdata' => [
                'centurygothic' => [
                    'R' => 'centurygothic.ttf',
                    'B' => 'centurygothic_bold.ttf',
                ],
            ],
            'default_font' => 'centurygothic',
            'useSubstitutions' => false,
            'subsetFont' => true,
            'compression' => 9,
            'img_dpi' => 72,
            'jpeg_quality' => 70,
            'repackageTTF' => true,
            'simpleTables' => true,
            'pdf_version' => '1.7',
            'exposeVersion' => false,
        ]);
        $this->pdf->SetCreator('PROLIFE Certification System');
        $this->pdf->SetAuthor('PROLIFE Certification System');
        $this->pdf->SetProtection(['print', 'print-highres'], '');
        $this->pdf->SetTextColor(0, 0, 0);

        $size = $this->importTemplate();

        $this->calculateField($size);
    }

    private function importTemplate() {
        $this->pdf->setSourceFile(__DIR__ . '/assets/certificate.pdf');
        $template = $this->pdf->ImportPage(1);
        $this->pdf->AddPageByArray([
            'orientation' => 'L',
            'margin-left' => 0,
            'margin-right' => 0,
            'margin-top' => 0,
            'margin-bottom' => 0,
            'resetpagenum' => '1',
            'supress' => false,
        ]);
        return $this->pdf->UseTemplate($template);
    }
    private function calculateField($size) {
        $fieldWidth = $size['width'] - 22;
        $left = 12;
        $this->field = [
            'left' => $left,
            'width' => $fieldWidth,
        ];
    }

    private function addText(bool $bold, int $size, int $y, int $h, string $text) {
        $this->pdf->SetFont('centurygothic', $bold ? 'B' : '', $size);
        $this->pdf->SetXY($this->field['left'], $y);
        $this->pdf->Cell($this->field['width'], $h, $text, 0, 0, 'C');
    }

    public function generateFile($data) {
        $this->pdf->SetTitle("Certyfikat ukończenia kursu Stop The Bleed - {$data['student_name']}");

        $this->addText(true, 24, 73, 14, 'PROLIFE SZKOLENIA MEDYCZNE');
        $this->addText(true, 32, 98, 16, $data['student_name']);
        $certificationDate = date('d.m.Y', strtotime($data['certificate_date']));
        $certificationExpiration = date('d.m.Y', strtotime($data['certificate_date'] . ' + 3 years'));
        $this->addText(false, 18, 132, 8, "$certificationDate / {$data['certificate_number']} / $certificationExpiration");
        $this->addText(true, 24, 141, 12, $data['instructors_name']);

        $this->pdf->Output("certyfikat_{$data['certificate_number']}.pdf", 'I');
    }
}
