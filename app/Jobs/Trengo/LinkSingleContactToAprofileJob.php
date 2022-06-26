<?php

namespace App\Jobs\Trengo;

use App\Jobs\RateLimitChecker;
use App\Services\TrengoService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;


class LinkSingleContactToAprofileJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, RateLimitChecker;

  protected $contactId, $profileId, $type = 'EMAIL';


  /**
   * The number of seconds the job can run before timing out.
   *
   * @var int
   */
  public $timeout = 120;

  public $tries = 1;


  /**
   * Create a new job instance.
   *
   * @param int $contactId
   * @param int $profileId
   * @return void
   */
  public function __construct($contactId, $profileId)
  {
    $this->contactId = $contactId;
    $this->profileId = $profileId;
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

    $response = $trengoService->sendPostRequest("profiles/{$this->profileId}/contacts", [
      "contact_id" => $this->contactId,
      "type" => $this->type
    ]);

    $this->checkRateLimitAndSetNextRequestTime($response, 'trengo-api-limit');
  }
}
