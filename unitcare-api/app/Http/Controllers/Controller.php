<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    /**
     * Standard success envelope: { Success, Message, Data }.
     */
    protected function ok($data = null, string $message = 'Request successful.', int $status = 200): JsonResponse
    {
        return response()->json([
            'Success' => true,
            'Message' => $message,
            'Data' => $data,
        ], $status);
    }

    /**
     * Standard failure envelope: { Success, Message, Error? }.
     */
    protected function fail(string $message, int $status = 400, ?string $error = null): JsonResponse
    {
        $payload = [
            'Success' => false,
            'Message' => $message,
        ];

        if ($error !== null) {
            $payload['Error'] = $error;
        }

        return response()->json($payload, $status);
    }
}
