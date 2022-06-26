<?php

namespace App\Jobs\Queues\Trengo;

use App\Jobs\Trengo\CreateContactsJob;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;

class CreateContactsQueue implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
   * @param \Illuminate\Support\Collection $contacts
   * @param \Illuminate\Support\Collection $customFields
   * @param int $channelId
   * @return void
   */
  public function __construct(Collection $contacts, Collection $customFields, int $channelId)
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
    $contactChunks = $this->contacts->chunk(3);

    foreach ($contactChunks as $contactChunk) {
      CreateContactsJob::dispatch($contactChunk, $this->customFields, $this->channelId)
        ->onQueue('trengo')
        ->delay(now()->addMinute());
    }
  }
}
