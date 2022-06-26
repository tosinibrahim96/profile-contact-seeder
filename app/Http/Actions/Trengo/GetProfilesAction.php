<?php

namespace App\Http\Actions\Trengo;

use App\Services\TrengoService;
use Illuminate\Http\Client\Response;


class GetProfilesAction
{
  protected $trengoService, $response = [];


  /**
   * __construct
   *
   * @param \App\Services\TrengoService $trengoService
   * @return void
   */
  public function __construct(TrengoService $trengoService)
  {
    $this->trengoService = $trengoService;
  }


  /**
   * Take all the steps involved in retrieving
   * a profile from Trengo's server
   * 
   *  
   * @param string $limit
   * @param int $page
   * @return \App\Http\Actions\Trengo\GetProfilesAction
   */
  public function execute(string $limit = 'all', $page = 1)
  {
    if (strtolower($limit) == 'one') {
      // $this->getOneProfile();
    }

    $this->getAllProfiles($page);

    return $this;
  }


  /**
   * Get all profiles from
   * Trengo server
   *
   * @param int $page
   * @return void
   */
  public function getAllProfiles($page)
  {
    $response = $this->trengoService->sendGetRequest("profiles?page=$page");

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
        'message' => json_decode($response->body())->message,
        'data' => []
      ];
    }

    $this->response = [
      'status' => true,
      'code' => $response->status(),
      'message' => "Profile(s) retrieved successfully.",
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
