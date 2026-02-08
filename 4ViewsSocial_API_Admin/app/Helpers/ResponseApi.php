<?php

namespace App\Helpers;

class ResponseApi
{
    public function continue()
    {
        return json_encode([
            'code' => 100,
            'data' => null,
            'message' => 'Request approved.'
        ]);
    }

    public function switchProtocol()
    {
        return json_encode([
            'code' => 101,
            'data' => null,
            'message' => 'Request switch protocol.'
        ]);
    }

    public function success($data = null)
    {
        return json_encode([
            'code' => 200,
            'data' => $data,
            'message' => 'Success.'
        ]);
    }

    public function dataNotfound()
    {
        return json_encode([
            'code' => 204,
            'data' => null,
            'message' => 'Data not found.'
        ]);
    }

    public function BadResource()
    {
        return json_encode([
            'code' => 301,
            'data' => null,
            'message' => 'Bad resource.'
        ]);
    }

    public function BadRequest($message = null)
    {
        return json_encode([
            'code' => 400,
            'data' => null,
            'message' => $message
        ]);
    }

    public function UnAuthorization($message = null)
    {
        return json_encode([
            'code' => 401,
            'data' => null,
            'message' => $message
        ]);
    }

    public function forbidden()
    {
        return json_encode([
            'code' => 403,
            'data' => null,
            'message' => 'Forbidden.'
        ]);
    }

    public function InternalServerError()
    {
        return json_encode([
            'code' => 500,
            'data' => null,
            'message' => 'Internal server error.'
        ]);
    }
}