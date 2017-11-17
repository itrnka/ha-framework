<?php
declare(strict_types = 1);

namespace ha\Access\HTTP\Authorization;


use ha\Access\HTTP\IO\Request\HTTPInputRequest;
use ha\Access\HTTP\IO\Response\HTTPOutputResponse;

/**
 * Interface Authorization.
 *
 */
interface Authorization
{

    /**
     * Determine whether user access is authorized or not and return this bool state.
     * If state is false, you can use redirect to login page or some other functionality.
     *
     * @param HTTPInputRequest $request
     * @param HTTPOutputResponse $response
     *
     * @return bool
     */
    public function authorize(HTTPInputRequest $request, HTTPOutputResponse $response) : bool;

}