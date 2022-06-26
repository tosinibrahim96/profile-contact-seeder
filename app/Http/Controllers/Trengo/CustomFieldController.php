<?php

namespace App\Http\Controllers\Trengo;

use App\Http\Actions\Trengo\CreateCustomFieldsAction;
use App\Http\Actions\Trengo\GetCustomFieldsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Trengo\CreateCustomFieldsRequest;
use Illuminate\Http\Request;
use App\Http\Response;

class CustomFieldController extends Controller
{

  protected $createCustomFieldsAction, $getCustomFieldsAction;

  /**
   * __construct
   *
   * @return void
   */
  public function __construct(
    CreateCustomFieldsAction $createCustomFieldsAction,
    GetCustomFieldsAction $getCustomFieldsAction

  ) {
    $this->createCustomFieldsAction = $createCustomFieldsAction;
    $this->getCustomFieldsAction = $getCustomFieldsAction;
  }


  /**
   * Create custom fields on Trengo
   * server
   *
   * @param  \Illuminate\Http\Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function index(Request $request)
  {
    $response = $this->getCustomFieldsAction
      ->execute()
      ->getResponse();

    return Response::send($response);
  }


  /**
   * Create custom fields on Trengo
   * server
   *
   * @param  \Illuminate\Http\Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function create(CreateCustomFieldsRequest $request)
  {
    $response = $this->createCustomFieldsAction
      ->execute($request->validated()['custom_fields'])
      ->getResponse();

    return Response::send($response);
  }
}
