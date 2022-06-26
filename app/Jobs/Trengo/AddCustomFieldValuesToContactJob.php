<?php

namespace App\Jobs\Trengo;

use App\Jobs\RateLimitChecker;
use App\Services\TrengoService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;


class AddCustomFieldValuesToContactJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, RateLimitChecker;

  protected $contactId, $contactCustomFields, $customFields;


  /**
   * The number of seconds the job can run before timing out.
   *
   * @var int
   */
  public $timeout = 120;


  /**
   * Create a new job instance.
   * 
   * @param int $contactId
   * @param array $contactCustomFields
   * @param \Illuminate\Support\Collection $customFields
   * @return void
   */
  public function __construct($contactId, $contactCustomFields, $customFields)
  {
    $this->contactId = $contactId;
    $this->contactCustomFields = $contactCustomFields;
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

      $contactId = $this->contactId;

      $response = $trengoService->sendPostRequest("contacts/{$contactId}/custom_fields", [
        "id" => $contactId,
        "custom_field_id" => $customField->id,
        "value" => $this->contactCustomFields[$customField->title]
      ]);

      $this->checkRateLimitAndSetNextRequestTime($response, 'trengo-api-limit');
    }
  }
}
