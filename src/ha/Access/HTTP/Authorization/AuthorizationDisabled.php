<?php
declare(strict_types = 1);

namespace ha\Access\HTTP\Authorization;

use ha\Access\HTTP\IO\Request\HTTPInputRequest;
use ha\Access\HTTP\IO\Response\HTTPOutputResponse;

/**
 * Class AuthorizationDisabled.
 *
 * This class is used for cases when we do not need authorization.
 */
class AuthorizationDisabled implements Authorization
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
    public function authorize(HTTPInputRequest $request, HTTPOutputResponse $response) : bool
    {
        // redirection example: $request->redirectToURL('/unauthorized');
        return true;
    }

}