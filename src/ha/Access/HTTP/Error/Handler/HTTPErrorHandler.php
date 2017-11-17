<?php
declare(strict_types = 1);

namespace ha\Access\HTTP\Error\Handler;

use ha\Access\HTTP\IO\Request\HTTPInputRequest;
use ha\Access\HTTP\IO\Response\HTTPOutputResponse;

interface HTTPErrorHandler
{

    /**
     * Handle HTTP error or other \Throwable instance throwed under HTTP access.
     *
     * @param HTTPInputRequest $request
     * @param HTTPOutputResponse $response
     * @param \Throwable $error
     */
    public function handleError(HTTPInputRequest $request, HTTPOutputResponse $response, \Throwable $error) : void;

}