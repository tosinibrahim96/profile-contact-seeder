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

class LinkContactsToProfilesJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, RateLimitChecker;

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
    $this->checkNextRequestTime('trengo-api-limit');

    foreach ($this->contacts as $contact) {

      $contactDetails = $this->getContactDetails(urlencode($contact['name']));

      if (is_null($contactDetails)) {
        continue;
      }

      $contactCompanyId = $contactDetails->custom_field_data->contact_company_id ?? null;

      if (is_null($contactCompanyId)) {
        continue;
      }

      $profile = $this->profiles->where('company_id', $contactCompanyId)->first();

      if (is_null($profile)) {
        continue;
      }

      $profileDetails = $this->getProfileDetails(urlencode($profile['name']));

      if (is_null($profileDetails)) {
        continue;
      }

      LinkSingleContactToAprofileJob::dispatch($contactDetails->id, $profileDetails->id)
        ->onQueue('trengo')
        ->delay(now()->addMinute());
    }
  }



  /**
   * Get the details of a contact
   * via the contact name
   *
   * @param  string $contactName
   * @return mixed
   */
  private function getContactDetails(string $contactName)
  {
    $trengoService = app()->make(TrengoService::class);
    $getContactResponse = $trengoService->sendGetRequest("contacts?term={$contactName}");

    $this->checkRateLimitAndSetNextRequestTime($getContactResponse, 'trengo-api-limit');

    return json_decode($getContactResponse->body())->data[0] ?? null;
  }



  /**
   * Get the details of a profile
   * via the profile name
   *
   * @param  string $profileName
   * @return mixed
   */
  private function getProfileDetails(string $profileName)
  {
    $trengoService = app()->make(TrengoService::class);

    $getProfileResponse = $trengoService->sendGetRequest("profiles?term={$profileName}");
    $this->checkRateLimitAndSetNextRequestTime($getProfileResponse, 'trengo-api-limit');

    return json_decode($getProfileResponse->body())->data[0] ?? null;
  }
}
