<?php

namespace App\Helpers;

class ResponseApi
{
    /**
     * Returns a 100 response with a message indicating that the request was approved.
     *
     * @return array
     */
    public function continue()
    {
        return json_encode([
            'code' => 100,
            'data' => null,
            'message' => 'Request approved.'
        ]);
    }

    /**
     * Returns a 101 response with a message indicating that the request needs to switch protocols.
     *
     * @return array
     */
    public function switchProtocol()
    {
        return json_encode([
            'code' => 101,
            'data' => null,
            'message' => 'Request switch protocol.'
        ]);
    }

    /**
     * Returns a 200 response with a message indicating that the request was successful.
     *
     * @param mixed|null $data The data to be returned in the response.
     *
     * @return array
     */
    public function success($data = null)
    {
        return json_encode([
            'code' => 200,
            'data' => $data,
            'message' => 'Success.'
        ]);
    }

    /**
     * Returns a 201 response with a message indicating that the resource was created successfully.
     *
     * @param mixed|null $data The data to be returned in the response.
     *
     * @return array
     */
    public function created($data = null)
    {
        return json_encode([
            'code' => 201,
            'data' => $data,
            'message' => 'Resource created successfully.'
        ]);
    }

    /**
     * Returns a 202 response with a message indicating that the request was accepted.
     *
     * @param mixed|null $data The data to be returned in the response.
     *
     * @return array
     */
    public function accepted($data = null)
    {
        return json_encode([
            'code' => 202,
            'data' => $data,
            'message' => 'Request accepted.'
        ]);
    }

    /**
     * Returns a 204 response with a message indicating that there is no content.
     *
     * @return array
     */
    public function noContent()
    {
        return json_encode([
            'code' => 204,
            'data' => null,
            'message' => 'No content.'
        ]);
    }

    /**
     * Returns a 404 response with a message indicating that the requested data was not found.
     *
     * @return array
     */
    public function dataNotFound()
    {
        return json_encode([
            'code' => 404,
            'data' => null,
            'message' => 'Data not found.'
        ]);
    }

    /**
     * Returns a 301 response with a message indicating that the requested resource is bad.
     * 
     * @return array
     */
    public function badResource()
    {
        return json_encode([
            'code' => 301,
            'data' => null,
            'message' => 'Bad resource.'
        ]);
    }

    /**
     * Returns a 400 response with a message indicating that the request was bad.
     *
     * @param string $message The message to be returned in the response.
     *
     * @return array
     */
    public function badRequest($message = 'Bad request.')
    {
        return json_encode([
            'code' => 400,
            'data' => null,
            'message' => $message
        ]);
    }

    /**
     * Returns a 401 response with a message indicating that the request was unauthorized.
     *
     * @param string $message The message to be returned in the response.
     *
     * @return array
     */
    public function unauthorized($message = 'Unauthorized.')
    {
        return json_encode([
            'code' => 401,
            'data' => null,
            'message' => $message
        ]);
    }

    /**
     * Returns a 403 response with a message indicating that the request was forbidden.
     *
     * @param string $message The message to be returned in the response.
     *
     * @return array
     */
    public function forbidden($message = 'Forbidden.')
    {
        return json_encode([
            'code' => 403,
            'data' => null,
            'message' => $message
        ]);
    }

    /**
     * Returns a 409 response with a message indicating that there was a conflict.
     *
     * @param string $message The message to be returned in the response.
     *
     * @return array
     */
    public function conflict($message = 'Conflict.')
    {
        return json_encode([
            'code' => 409,
            'data' => null,
            'message' => $message
        ]);
    }

    /**
     * Returns a 422 response with a message indicating that the request entity was unprocessable.
     *
     * @param string $message The message to be returned in the response.
     *
     * @return array
     */
    public function unprocessableEntity($message = 'Unprocessable entity.')
    {
        return json_encode([
            'code' => 422,
            'data' => null,
            'message' => $message
        ]);
    }

    /**
     * Returns a 500 response with a message indicating that there was an internal server error.
     *
     * @param string $message The message to be returned in the response.
     *
     * @return array
     */
    public function internalServerError($message = 'Internal server error.')
    {
        return json_encode([
            'code' => 500,
            'data' => null,
            'message' => $message
        ]);
    }

    /**
     * Returns a 501 response with a message indicating that the request was not implemented.
     *
     * @param string $message The message to be returned in the response.
     *
     * @return array
     */
    public function notImplemented($message = 'Not implemented.')
    {
        return json_encode([
            'code' => 501,
            'data' => null,
            'message' => $message
        ]);
    }

    /**
     * Returns a 503 response with a message indicating that the service is unavailable.
     *
     * @param string $message The message to be returned in the response.
     *
     * @return array
     */
    public function serviceUnavailable($message = 'Service unavailable.')
    {
        return json_encode([
            'code' => 503,
            'data' => null,
            'message' => $message
        ]);
    }
}
