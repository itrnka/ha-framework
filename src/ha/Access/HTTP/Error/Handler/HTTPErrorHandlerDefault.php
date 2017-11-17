<?php
declare(strict_types = 1);

namespace ha\Access\HTTP\Error\Handler;

use ha\Access\HTTP\Error\HTTPError;
use ha\Access\HTTP\IO\Request\HTTPInputRequest;
use ha\Access\HTTP\IO\Response\HTTPOutputResponse;

class HTTPErrorHandlerDefault implements HTTPErrorHandler
{

    /**
     * Handle HTTP error or other \Throwable instance throwed under HTTP access.
     *
     * @param HTTPInputRequest $request
     * @param HTTPOutputResponse $response
     * @param \Throwable $e
     *
     * @throws \Throwable
     * @internal param \Throwable $error
     */
    public function handleError(HTTPInputRequest $request, HTTPOutputResponse $response, \Throwable $e) : void
    {
        if ($e instanceof HTTPError) {
            $e->generateErrorResponse();
        }
        #$error = (new HTTPServerError([], $e->getMessage()))->generateErrorResponse();
        throw $e;
    }

}