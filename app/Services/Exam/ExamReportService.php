<?php

namespace App\Services\Exam;

use App\Models\Exam;

class ExamReportService {
    public function getPublicKey($id): string {
        $key = Exam::where('id', $id)->pluck('public_key');
        return base64_encode($key[0]);
    }

    public function handleReport($request) {
        $response = $request->attributes->get('response');
        $uuid = $response->uuid;
        $exam = $response->exam_id;

        $payload = array_merge(
            $request->validated(),
            ['uuid' => $uuid]
        );
        return $this->signRequest($exam, $payload);
    }
    private function signRequest($id, $data): string {
        $key = Exam::where('id', $id)->pluck('private_key');

        $data = json_encode($data);
        $signature = sodium_crypto_sign_detached($data, $key[0]);

        return base64_encode($signature);
    }
}
