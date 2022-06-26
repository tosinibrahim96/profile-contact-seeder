<?php

namespace App\Http\Actions\Trengo;

use App\Jobs\Queues\Trengo\CreateProfilesQueue;

class CreateProfilesAction
{

  protected $getProfilesAction, $getCustomFieldsAction;

  protected $profiles, $customFields, $response = [];


  /**
   * __construct
   *
   * @param \App\Http\Actions\Trengo\GetProfilesAction $getProfilesAction
   * @param \App\Http\Actions\Trengo\GetCustomFieldsAction $getCustomFieldsAction
   * 
   * @return void
   */
  public function __construct(
    GetProfilesAction $getProfilesAction,
    GetCustomFieldsAction $getCustomFieldsAction
  ) {
    $this->getProfilesAction = $getProfilesAction;
    $this->getCustomFieldsAction = $getCustomFieldsAction;
  }


  /**
   * Take all the steps involved in creating
   * profile on Trengo server
   *  
   * @param array $profiles
   * @param string $dataSource
   * @return \App\Http\Actions\Trengo\CreateProfilesAction
   */
  public function execute(array $profiles, string $dataSource)
  {
    $dataSource = ucfirst(strtolower($dataSource));

    $this->{"createProfilesFrom{$dataSource}"}($profiles);

    return $this;
  }



  /**
   * Create profiles from data coming 
   * from a file
   *
   * @param  array $profiles
   * @return void
   */
  private function createProfilesFromFile(array $profiles)
  {
    $this->setProfilesFromFiledata($profiles)
      ->createProfiles();

    $this->setResponse();
  }


  /**
   * Set profiles to be sent to the Trengo
   * server using the data from the file
   *
   * @param  array $profiles
   * @return \App\Http\Actions\Trengo\CreateProfilesAction
   */
  private function setProfilesFromFiledata(array $profiles)
  {
    foreach ($profiles as $index => $profile) {

      if ($index == 0) {
        continue;
      }

      $companyIdAndName = explode(',', $profile, 2);

      $formattedProfiles[$index]['company_id'] = $companyIdAndName[0];
      $formattedProfiles[$index]['name'] = str_replace('"', '', $companyIdAndName[1]);
    }

    $this->profiles = collect($formattedProfiles);
    $this->setCustomFieldsForProfiles();

    return $this;
  }



  /**
   * Set all the custom fields that belongs
   * to the PROFILE category
   *
   * @return void
   */
  private function setCustomFieldsForProfiles()
  {
    $customFieldsOnServer = $this->getCustomFieldsAction
      ->execute()
      ->getResponse()['data'];

    $profileCustomFiels = collect($customFieldsOnServer)->whereIn('type', 'PROFILE');

    $this->customFields = $profileCustomFiels;
  }




  /**
   * Dispatch job that handles the profile creation
   * process on Trengo server 
   * 
   * 
   * @return void
   */
  private function createProfiles()
  {
    CreateProfilesQueue::dispatch(
      $this->profiles,
      $this->customFields
    )->onQueue('trengo');
  }



  /**
   * Set response to send to the controller
   *
   * @return void
   */
  private function setResponse()
  {
    $this->response = [
      'status' => true,
      'code' => 200,
      'message' => "Profiles creation started successfully.",
      'data' => []
    ];
  }


  /**
   * Get the response from the last request
   *
   * @return array
   */
  public function getResponse()
  {
    return $this->response;
  }
}
