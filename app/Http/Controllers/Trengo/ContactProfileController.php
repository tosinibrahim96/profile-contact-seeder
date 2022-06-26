<?php

namespace App\Http\Controllers\Trengo;


use App\Http\Actions\Trengo\LinkContactsToProfilesAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Response;
use App\Services\TrengoService;

class ContactProfileController extends Controller
{

  protected $trengoService, $linkContactsToProfilesAction;

  /**
   * __construct
   *
   * @return void
   */
  public function __construct(
    TrengoService $trengoService,
    LinkContactsToProfilesAction $linkContactsToProfilesAction

  ) {
    $this->trengoService = $trengoService;
    $this->linkContactsToProfilesAction = $linkContactsToProfilesAction;
  }


  /**
   * Create contacts on Trengo
   * server
   *
   * @param  \Illuminate\Http\Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function linkContactsToProfiles(Request $request)
  {
    $contacts = file(storage_path('contacts.csv'), FILE_IGNORE_NEW_LINES);
    $profiles = file(storage_path('companies.csv'), FILE_IGNORE_NEW_LINES);

    $response =  $this->linkContactsToProfilesAction
      ->execute($contacts, $profiles)
      ->getResponse();

    return Response::send($response);
  }
}
