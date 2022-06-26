<?php

namespace App\Http\Controllers\Trengo;

use App\Http\Actions\Trengo\CreateProfilesAction;
use App\Http\Actions\Trengo\GetProfilesAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Response;


class ProfileController extends Controller
{

  protected $createProfilesAction, $getProfilesAction;

  /**
   * __construct
   * 
   * @param \App\Http\Actions\Trengo\CreateProfilesAction $createProfilesAction
   * @param \App\Http\Actions\Trengo\GetProfilesAction $getProfilesAction
   *
   * @return void
   */
  public function __construct(
    CreateProfilesAction $createProfilesAction,
    GetProfilesAction $getProfilesAction

  ) {
    $this->createProfilesAction = $createProfilesAction;
    $this->getProfilesAction = $getProfilesAction;
  }


  /**
   * Get profiles on Trengo
   * server
   *
   * @param  \Illuminate\Http\Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function index(Request $request)
  {
    $response = $this->getProfilesAction
      ->execute()
      ->getResponse();

    return Response::send($response);
  }




  /**
   * Create profiles on Trengo
   * server
   *
   * @param  \Illuminate\Http\Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function createProfilesFromFile(Request $request)
  {
    $rows = file(storage_path('companies.csv'), FILE_IGNORE_NEW_LINES);

    $response = $this->createProfilesAction
      ->execute($rows, 'file')
      ->getResponse();

    return Response::send($response);
  }
}
