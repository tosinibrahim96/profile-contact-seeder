<?php

namespace App\Http\Actions\Trengo;

use App\Jobs\Trengo\CreateCustomFieldJob;
use App\Jobs\Queues\Trengo\CreateCustomFieldsQueue;

class CreateCustomFieldsAction
{

  protected $response = [];


  /**
   * __construct
   *
   * @return void
   */
  public function __construct()
  {
  }


  /**
   * Take all the steps involved in creating
   * custom fields
   *  
   * @param array $requestData
   * @return \App\Http\Actions\Trengo\CreateCustomFieldsAction
   */
  public function execute(array $requestData)
  {
    $this->createCustomFields($requestData);

    return $this;
  }


  /**
   * Dispatch a job responsible to create
   * custom fields on Trengo server.
   *
   * @param array $customFields
   * @return void
   */
  private function createCustomFields(array $customFields)
  {
    if (count($customFields) == 1) {
      $title = $customFields[0]['title'] ?? null;
      $type = $customFields[0]['type'] ?? null;

      CreateCustomFieldJob::dispatch($title, $type)
        ->onQueue('trengo');
    } else {
      CreateCustomFieldsQueue::dispatch($customFields)
        ->onQueue('trengo');
    }

    $this->setResponse();
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
      'message' => "Custom fields creation started successfully."
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
