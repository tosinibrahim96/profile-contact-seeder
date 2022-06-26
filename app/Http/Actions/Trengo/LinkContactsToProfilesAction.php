<?php

namespace App\Http\Actions\Trengo;

use App\Jobs\Queues\Trengo\LinkContactsToProfilesQueue;
use App\Services\TrengoService;


class LinkContactsToProfilesAction
{

  protected $contactsCollection, $profilesCollection;


  /**
   * __construct
   *
   * @return void
   */
  public function __construct(TrengoService $trengoService)
  {
    $this->trengoService = $trengoService;
  }


  /**
   * Take all the steps involved in linking
   * profiles to contacts
   * 
   *  
   * @param array $contacts
   * @param array $profiles
   * @return \App\Http\Actions\Trengo\GetProfilesAction
   */
  public function execute(array $contacts, array $profiles)
  {
    $this->buildContactsCollection($contacts);
    $this->buildProfilesCollection($profiles);

    $this->LinkContactsToProfiles();

    return $this;
  }


  /**
   * Dispatch job that handles the process of
   * linking contacts to profiles 
   * 
   * @return void
   */
  public function linkContactsToProfiles()
  {
    $contactChunks = $this->contactsCollection->chunk(50);

    foreach ($contactChunks as $contactChunk) {
      LinkContactsToProfilesQueue::dispatch($contactChunk, $this->profilesCollection)
        ->onQueue('trengo');
    }

    $this->setResponse();
  }



  /**
   * Restructure the contacts data in a format that
   * will make it easy to process by the jobs
   * 
   * @param array $contacts
   * @return void
   */
  public function buildContactsCollection(array $contacts)
  {
    $formattedContacts = [];

    foreach ($contacts as $index => $contact) {

      if ($index == 0) {
        continue;
      }

      $contactInfo = explode(',', $contact);
      $formattedContacts[$index]['name'] = str_replace('"', '', $contactInfo[1]);
    }

    $this->contactsCollection = collect($formattedContacts);
  }



  /**
   * Restructure the profiles data in a format that
   * will make it easy to process by the jobs
   * 
   * @param array $profiles
   * @return void
   */
  public function buildProfilesCollection(array $profiles)
  {

    foreach ($profiles as $index => $profile) {

      if ($index == 0) {
        continue;
      }

      $companyIdAndName = explode(',', $profile, 2);

      $formattedProfiles[$index]['company_id'] = $companyIdAndName[0];
      $formattedProfiles[$index]['name'] = str_replace('"', '', $companyIdAndName[1]);
    }

    $this->profilesCollection = collect($formattedProfiles);
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
      'message' => "Profiles Linking to contacts started successfully.",
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
