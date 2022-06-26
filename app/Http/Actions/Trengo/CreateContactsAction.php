<?php

namespace App\Http\Actions\Trengo;

use App\Jobs\Queues\Trengo\CreateContactsQueue;

class CreateContactsAction
{

  protected $getCustomFieldsAction;

  protected $contacts, $customFields, $response = [];


  /**
   * __construct
   *
   * @param GetCustomFieldsAction $getCustomFieldsAction
   * @return void
   */
  public function __construct(GetCustomFieldsAction $getCustomFieldsAction)
  {
    $this->getCustomFieldsAction = $getCustomFieldsAction;
  }


  /**
   * Take all the steps involved in creating 
   * contacts on trengo server
   * 
   *  
   * @param array $contacts
   * @param string $dataSource
   * @param int $channelId
   * @return \App\Http\Actions\Trengo\CreateContactsAction
   */
  public function execute(array $contacts, string $dataSource, int $channelId)
  {
    $dataSource = ucfirst(strtolower($dataSource));

    $this->{"createContactsFrom{$dataSource}"}($contacts, $channelId);

    return $this;
  }



  /**
   * Create contacts from data coming 
   * from a file
   *
   * @param  array $contacts
   * @param int $channelId
   * @return void
   */
  private function createContactsFromFile(array $contacts, int $channelId)
  {
    $this->setContactsFromFiledata($contacts)
      ->createContacts($channelId);

    $this->setResponse();
  }


  /**
   * Set contacts to be sent to the Trengo
   * server using the data from the file
   *
   * @param  array $contacts
   * @return \App\Http\Actions\Trengo\CreateContactsAction
   */
  private function setContactsFromFiledata(array $contacts)
  {
    foreach ($contacts as $index => $contact) {

      if ($index == 0) {
        continue;
      }

      $contactInfo = explode(',', $contact);

      $formattedContacts[$index]['contact_id'] = $contactInfo[0];
      $formattedContacts[$index]['name'] = str_replace('"', '', $contactInfo[1]);
      $formattedContacts[$index]['contact_company_id'] = $contactInfo[2];
      $formattedContacts[$index]['contact_email'] =  str_replace('_', '-', filter_var($contactInfo[3], FILTER_SANITIZE_EMAIL));
      $formattedContacts[$index]['contact_phone'] = $contactInfo[4];
      $formattedContacts[$index]['contact_date_of_birth'] = $contactInfo[5];
    }

    $this->contacts = collect($formattedContacts);
    $this->setCustomFieldsForContacts();

    return $this;
  }



  /**
   * Set all the custom fields that belongs
   * to the CONTACT category
   *
   * @return void
   */
  private function setCustomFieldsForContacts()
  {
    $customFieldsOnServer = $this->getCustomFieldsAction
      ->execute()
      ->getResponse()['data'];

    $contactsCustomFields = collect($customFieldsOnServer)->whereIn('type', 'CONTACT');

    $this->customFields = $contactsCustomFields;
  }


  /**
   * Dispatch job that handles the contacts creation
   * process on Trengo server 
   * 
   * @param int $channelId
   * @return void
   */
  private function createContacts(int $channelId)
  {
    $contactChunks = $this->contacts->chunk(50);

    foreach ($contactChunks as $contactChunk) {
      CreateContactsQueue::dispatch(
        $contactChunk,
        $this->customFields,
        $channelId
      )->onQueue('trengo');
    }
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
      'message' => "Contacts creation started successfully.",
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
