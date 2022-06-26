<?php

namespace Tests\Unit;

use App\Services\TrengoService;
use Mockery;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;

class TrengoServiceTest extends TestCase
{
  protected $trengoService;

  public function setup(): void
  {
    parent::setup();

    $this->trengoService = Mockery::mock(TrengoService::class);
  }


  /**
   * Test that  the trengo service sends a GET request.
   *
   * @return void
   */
  public function test_that_trengo_service_sends_get_request()
  {
    $this->trengoService->shouldReceive('sendGetRequest')
      ->andReturn(
        (object)[
          'status' => 200,
          'data' => (object) [
            "id" => 9468148,
            "name" => "Company B",
            "abbr" => "C",
            "created_at" => "2022-06-26 18:20:20",
            "color" => "#00bcd4",
            "user" => [
              "id" => 487943,
              "agency_id" => 225473,
              "first_name" => "Ibrahim",
              "last_name" => "Alausa",
              "name" => "Ibrahim Alausa",
              "full_name" => "Ibrahim Alausa",
              "email" => "worldclassibro@gmail.com",
              "abbr" => "I",
              "phone" => null,
              "color" => "#ff5722",
              "locale_code" => "en-GB",
              "status" => "ACTIVE",
              "text" => "Ibrahim Alausa",
              "is_online" => 1,
              "user_status" => "AWAY",
              "chat_status" => true,
              "voip_status" => null,
              "voip_device" => "WEB",
              "profile_image" => null,
              "authorization" => "OWNER",
              "is_primary" => 1,
              "timezone" => "Europe/Amsterdam",
              "created_at" => "2022-06-25 10:11:34",
              "two_factor_authentication_enabled" => false
            ],
            "notes" => [],
            "custom_fields" => [],
            "contacts" => [],
            "custom_field_values" => []
          ]
        ]
      );

    $response = $this->trengoService->sendGetRequest('profiles/9468148');

    assertEquals('9468148', $response->data->id);
    assertEquals(200, $response->status);
  }



  /**
   * Test that  the trengo service sends a POST request.
   *
   * @return void
   */
  public function test_that_trengo_service_sends_post_request()
  {
    $this->trengoService->shouldReceive('sendPostRequest')
      ->andReturn(
        (object)[
          "status" => 200,
          "data" => (object)[
            "agency_id" => 225473,
            "created_by" => 487943,
            "name" => "Profile Test",
            "color" => "#673ab7",
            "updated_at" => "2022-06-26T18:18:03.000000Z",
            "created_at" => "2022-06-26T18:18:03.000000Z",
            "id" => 9468533,
            "profile_image" => null,
            "abbr" => "P"
          ]
        ]
      );

    $response = $this->trengoService->sendPostRequest("profiles", ["name" => "Profile Test"]);

    assertEquals('Profile Test', $response->data->name);
    assertEquals(200, $response->status);
  }
}
