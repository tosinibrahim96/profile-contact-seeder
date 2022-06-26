<?php

namespace App\Http\Controllers\Trengo;

use App\Http\Actions\Trengo\CreateContactsAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Response;


class ContactController extends Controller
{

  protected $createContactsAction;

  /**
   * __construct
   *
   * @return void
   */
  public function __construct(
    CreateContactsAction $createContactsAction,

  ) {
    $this->createContactsAction = $createContactsAction;
  }




  /**
   * Create contacts on Trengo
   * server
   *
   * @param  \Illuminate\Http\Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function createContactsFromFile(Request $request)
  {
    /**
     * ID for E-mail channel. 
     * Just for the sake of the test. A better way will be fetching this
     * value from Trengo server in case this changes
     * 
     */
    $channelId = 901448;
    $rows = file(storage_path('contacts.csv'), FILE_IGNORE_NEW_LINES);

    $response = $this->createContactsAction
      ->execute($rows, 'file', $channelId)
      ->getResponse();

    return Response::send($response);
  }
}
