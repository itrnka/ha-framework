<?php
declare(strict_types=1);

namespace ha\Access\HTTP\Router\Route;

/**
 * Class HTTPRouteExample.
 * Example implementation.
 */
class HTTPRouteExample extends HTTPRouteDefaultAbstract implements HTTPRoute
{

    /**
     * Determine whether this route is compatible with URL. If false, router skips this route.
     * @return bool
     */
    public function URLIsCompatible(): bool
    {
        if ($this->request->getUrl()->getPath() === '/') {
            return true;
        }
        return false;
    }

    /**
     * Setup response headers and body by your controller or other logic.
     */
    public function prepareResponse(): void
    {
        // change default HTTP status code
        $this->response->setStatusCode(200);

        // clear default response headers
        $this->response->resetHeaders();

        // add your response headers
        $this->response->setCharset('UTF-8');
        $this->response->setContentType('text/html');
        $this->response->addHeader(
            'X-Some-Header-Example-Name: headerValueExample'
        ); // add some other headers by this example

        // call here your controller to get real response body
        $body = 'This is an example generated in Route <i>' . get_class($this) . '</i> with a fictive Controller.';

        // set body to response as response body
        $this->response->setBody($body);
    }
}