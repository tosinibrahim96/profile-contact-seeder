<?php

namespace App\Http\Actions\Trengo;

use App\Services\TrengoService;
use Illuminate\Http\Client\Response;

class GetCustomFieldsAction
{

  protected $trengoService, $response = [];


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
   * Take all the steps involved in setting up a
   * new customer account
   * 
   *  
   * @param string $limit
   * @return \App\Http\Actions\Trengo\GetCustomFieldsAction
   */
  public function execute(string $limit = 'all', int $page = 1)
  {
    if (strtolower($limit) == 'one') {
      // $this->getOneCustomField();
    }

    $this->getAllCustomFields($page);

    return $this;
  }


  /**
   * Get all custom fields from
   * Trengo server
   *
   * @return array
   */
  public function getAllCustomFields($page)
  {
    $response = $this->trengoService->sendGetRequest("custom_fields?page=$page");

    $this->setResponse($response);
  }


  /**
   * Set response to send to the controller
   *
   * @param  \Illuminate\Http\Client\Response $response
   * @return void
   */
  private function setResponse(Response $response)
  {
    if ($response->status() != 200) {
      $this->$response = [
        'status' => false,
        'code' => $response->status(),
        'message' => json_decode($response->body())->message
      ];
    }

    $this->response = [
      'status' => true,
      'code' => $response->status(),
      'message' => "Custom field(s) retrieved successfully.",
      'data' => json_decode($response->body())->data
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
