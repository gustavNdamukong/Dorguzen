<?php
namespace Dorguzen\Controllers;

use Dorguzen\Core\DGZ_Controller;
use Dorguzen\Core\DGZ_Request;

class TestController extends DGZ_Controller
{

    public function __construct()
    {
        parent::__construct();
    }


    public function getDefaultAction()
    {
        return 'defaultAction';
    }


    public function defaultAction()
    {

    }


    public function ping()
    {
        response()->json(['status' => 'ok'])->send();
    }


    /**
     * The response being sent back, after conversion into json
     * by send() and the formatter is expected to be:
     *
     *  {
     *      "data": {
     *        "message": "hello"
     *      }
     *  }
     *
     */
    public function echo()
    {
        $message = $_REQUEST['message'] ?? null;

        return response()
            ->setData([
                'data' => [
                    'message' => $message,
                ],
            ])
            ->send();
    }

    public function echoJson()
    {
        $request = container(DGZ_Request::class);
        $data = $request->getJson();

        return response()
            ->setData([
                'data' => [
                    'name' => $data['name'],
                ],
            ])
            ->send();
    }


    /**
     * The data expected in a request sent here contains a user object
     * that holds data like this:
     *
     *  {"email":"test@example.com","id":1}
     *
     * In one phrase, here we are testing request auth state awareness.
     * This controller must ask the request:
     *      -do we have a user?
     *      -if no, return a 401
     *      -if yes, return user data
     */
    public function meTest()
    {
        $user = request()->user();

        // Not authenticated
        if ($user === null)
        {
            return response()
                ->setStatus(401)
                ->setData([
                    'message' => 'Unauthenticated',
                ])
                ->send();
        }

        // Authenticated
        return response()
            ->setData([
                'email' => $user->email,
            ])
            ->send();
    }
}
