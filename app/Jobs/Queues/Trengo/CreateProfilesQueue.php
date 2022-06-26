<?php

namespace App\Jobs\Queues\Trengo;

use App\Jobs\Trengo\CreateProfilesJob;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;

class CreateProfilesQueue implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
   * @param \Illuminate\Support\Collection $profiles
   * @param \Illuminate\Support\Collection $customFields
   * @return void
   */
  public function __construct(Collection $profiles,  Collection $customFields)
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
    $profileChunks = $this->profiles->chunk(20);

    foreach ($profileChunks as $profileChunk) {
      CreateProfilesJob::dispatch($profileChunk, $this->customFields)
        ->onQueue('trengo')
        ->delay(now()->addMinute());
    }
  }
}
