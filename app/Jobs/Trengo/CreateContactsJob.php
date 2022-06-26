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


class CreateContactsJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, RateLimitChecker;

  protected $contacts, $customFields, $channelId;


  /**
   * The number of seconds the job can run before timing out.
   *
   * @var int
   */
  public $timeout = 120;


  /**
   * Create a new job instance.
   *
   * @param mixed $contacts
   * @param \Illuminate\Support\Collection $customFields
   * @param int $channelId
   * @return void
   */
  public function __construct($contacts, Collection $customFields, int $channelId)
  {
    $this->contacts = $contacts;
    $this->customFields = $customFields;
    $this->channelId = $channelId;
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    $channelId = $this->channelId;

    $this->checkNextRequestTime('trengo-api-limit');

    $trengoService = app()->make(TrengoService::class);

    foreach ($this->contacts as $contact) {

      $response = $trengoService->sendPostRequest("channels/{$channelId}/contacts", [
        "identifier" => $contact['contact_email'],
        "channel_id" => $channelId,
        "name" => $contact['name']
      ]);

      $this->checkRateLimitAndSetNextRequestTime($response, 'trengo-api-limit');
      $contactId = json_decode($response->body())->id ?? null;

      if (!is_null($contactId)) {
        AddCustomFieldValuesToContactJob::dispatch($contactId, $contact, $this->customFields)
          ->onQueue('trengo')
          ->delay(now()->addMinute());
      }
    }
  }
}
