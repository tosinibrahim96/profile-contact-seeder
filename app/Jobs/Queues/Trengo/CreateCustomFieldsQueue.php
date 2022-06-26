<?php

namespace App\Jobs\Queues\Trengo;

use App\Jobs\Trengo\CreateCustomFieldJob;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;


class CreateCustomFieldsQueue implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  protected $customFields;


  /**
   * The number of seconds the job can run before timing out.
   *
   * @var int
   */
  public $timeout = 120;


  /**
   * Create a new job instance.
   *
   * @param array $customFields
   * @return void
   */
  public function __construct(array $customFields)
  {
    $this->customFields = $customFields;
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    foreach ($this->customFields as $customField) {
      CreateCustomFieldJob::dispatch($customField['title'], $customField['type'])
        ->onQueue('trengo')
        ->delay(now()->addSeconds(20));
    }
  }
}
