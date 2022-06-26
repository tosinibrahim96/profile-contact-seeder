<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Cache;

trait RateLimitChecker
{

  public $maxExceptions = 3;


  public function checkNextRequestTime($cacheKey)
  {
    if ($timestamp = Cache::get($cacheKey)) {
      return $this->release($timestamp - time());
    }
  }



  public function checkRateLimitAndSetNextRequestTime($response, $cacheKey)
  {
    if ($response->failed() && $response->status() == 429) {
      $secondsRemaining = $response->header('Retry-After');

      Cache::put(
        $cacheKey,
        now()->addSeconds($secondsRemaining)->timestamp,
        $secondsRemaining
      );

      return $this->release(
        $secondsRemaining
      );
    }
  }
}
