<?php

namespace App\Jobs\Trengo;

use App\Jobs\RateLimitChecker;
use App\Services\TrengoService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CreateCustomFieldJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, RateLimitChecker;

  protected $title, $type;


  /**
   * The number of seconds the job can run before timing out.
   *
   * @var int
   */
  public $timeout = 120;


  /**
   * Create a new job instance.
   *
   * @param string $title
   * @param string $type
   * @return void
   */
  public function __construct(string $title, string $type)
  {
    $this->title = $title;
    $this->type = $type;
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
    $response = $trengoService->sendPostRequest("custom_fields", [
      "title" => $this->title,
      "type" => $this->type
    ]);

    $this->checkRateLimitAndSetNextRequestTime($response, 'trengo-api-limit');
  }
}
