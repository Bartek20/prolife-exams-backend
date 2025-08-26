<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateQuestionRequest;
use App\Http\Requests\Admin\GenerateCertificateRequest;
use App\Services\Admin\CertificateService;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function __construct(private CertificateService $certificateService) {
    }

    public function create(GenerateCertificateRequest $request) {
        if ($request->certificate_type === 'StopTheBleed') {
            $this->certificateService->generateStopTheBleed($request->validated());
        }
    }
}
