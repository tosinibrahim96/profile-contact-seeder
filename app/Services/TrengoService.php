<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TrengoService
{

  protected $baseUrl, $apiKey;


  /**
   * __construct
   *
   * @return void
   */
  public function __construct()
  {
  }


  /**
   * setup
   *
   * @return void
   */
  public function setup()
  {
    $this->baseUrl = config('services.trengo.base-url');
    $this->apiKey = config('services.trengo.key');
  }


  /**
   * Send a POST request to Trengo 
   * server
   *
   * @param  string $path
   * @param  array $data
   * @return \Illuminate\Http\Client\Response
   * 
   */
  public function sendPostRequest(string $path, array $data)
  {
    $this->setup();

    $response = Http::withHeaders([
      'Accept' => 'application/json',
      'Authorization' => "Bearer $this->apiKey",
      'Content-Type' => 'application/json',
    ])->post("$this->baseUrl/$path", $data);

    return $response;
  }




  /**
   * Send a GET request to Trengo 
   * server
   *
   * @param  string $path
   * @return \Illuminate\Http\Client\Response
   * 
   */
  public function sendGetRequest(string $path)
  {
    $this->setup();

    $response = Http::withHeaders([
      'Accept' => 'application/json',
      'Authorization' => "Bearer $this->apiKey",
      'Content-Type' => 'application/json',
    ])->get("$this->baseUrl/$path");

    return $response;
  }
}
