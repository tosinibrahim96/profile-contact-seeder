<?php

namespace App\Jobs\Trengo;

use App\Jobs\RateLimitChecker;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use App\Services\TrengoService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;


class AddCustomFieldValuesToProfileJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, RateLimitChecker;

  protected $profileId, $profileCustomFields, $customFields;


  /**
   * The number of seconds the job can run before timing out.
   *
   * @var int
   */
  public $timeout = 120;


  /**
   * Create a new job instance.
   * 
   * @param int $profileId
   * @param array $profileCustomFields
   * @param \Illuminate\Support\Collection $customFields
   * @return void
   */
  public function __construct(int $profileId, array $profileCustomFields, Collection $customFields)
  {
    $this->profileId = $profileId;
    $this->profileCustomFields = $profileCustomFields;
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

    foreach ($this->customFields as $customField) {

      $profileId = $this->profileId;

      $response = $trengoService->sendPostRequest("profiles/{$profileId}/custom_fields", [
        "id" => $profileId,
        "custom_field_id" => $customField->id,
        "value" => $this->profileCustomFields[$customField->title]
      ]);

      $this->checkRateLimitAndSetNextRequestTime($response, 'trengo-api-limit');
    }
  }
}
