<?php

namespace App\Http;

use Illuminate\Http\JsonResponse;

class Response
{
  /**
   * Return a successful JSON response with mixed(object|JsonSerializable) data
   *
   * @param array $response
   * @return JsonResponse
   */
  public static function send(array $response): JsonResponse
  {
    $response = [
      'status' => $response['status'] ?? null,
      'message' => $response['message'] ?? null,
      'data' => $response['data'] ?? null
    ];

    return response()->json($response, $response['code'] ?? 500);
  }
}
