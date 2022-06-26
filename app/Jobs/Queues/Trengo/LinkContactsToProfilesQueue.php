<?php

namespace App\Jobs\Queues\Trengo;

use App\Jobs\Trengo\LinkContactsToProfilesJob;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;

class LinkContactsToProfilesQueue implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  protected $contacts, $profiles;


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
   * @param \Illuminate\Support\Collection $profiles
   * @return void
   */
  public function __construct(Collection $contacts, Collection $profiles)
  {
    $this->contacts = $contacts;
    $this->profiles = $profiles;
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
      LinkContactsToProfilesJob::dispatch($contactChunk, $this->profiles)
        ->onQueue('trengo')
        ->delay(now()->addMinute());
    }
  }
}
