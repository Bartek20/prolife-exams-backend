<?php

namespace App\Services\Admin;

use App\Utils\PDF\CertificateStopTheBleed;

class CertificateService {
    public function generateStopTheBleed($data) {
        $template = new CertificateStopTheBleed();
        $template->generateFile($data);
    }
}
