<?php

namespace App\Jobs\Trengo;

use App\Jobs\RateLimitChecker;
use App\Services\TrengoService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;

class CreateProfilesJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, RateLimitChecker;

  protected $profiles, $customFields;


  /**
   * The number of seconds the job can run before timing out.
   *
   * @var int
   */
  public $timeout = 120;


  /**
   * Create a new job instance.
   *
   * @param mixed $profiles
   * @param \Illuminate\Support\Collection $customFields
   * @return void
   */
  public function __construct($profiles, Collection $customFields)
  {
    $this->profiles = $profiles;
    $this->customFields = $customFields;
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    $this->checkNextRequestTime('trengo-api-limit');

    $trengoService = app()->make(TrengoService::class);

    foreach ($this->profiles as $profile) {

      $response = $trengoService->sendPostRequest("profiles", [
        "name" => $profile['name']
      ]);

      $this->checkRateLimitAndSetNextRequestTime($response, 'trengo-api-limit');
      $profileId = json_decode($response->body())->id ?? null;

      if (!is_null($profileId)) {
        AddCustomFieldValuesToProfileJob::dispatch($profileId, $profile, $this->customFields)
          ->onQueue('trengo')
          ->delay(now()->addMinute());
      }
    }
  }
}
