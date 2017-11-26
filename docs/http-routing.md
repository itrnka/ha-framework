![ha framework](img/ha-logo.png "ha framework")

# HTTP application and routing in *ha* framework


When our application runs an HTTP server, such as an Apache HTTP server, nginx, etc., our job is to process the HTTP request, convert it to an HTTP response, and send this response to the client.  In order to process the request, we need to go through the list of URLs supported in our application. We also need to verify whether client requirements match our capabilities. If something is wrong, we need to transform error(s) to adequate HTTP response. If the URL and everything else is correct, we run a process that produces or processes the data (by current URL) and transforms the result of the process into an HTTP response. 

This functionality can be achieved in several ways, but *ha* framework uses the following objects:

- **Request** - is the object that represents the client request and is injected into each route.
- **Response** -  is the object that represents the response of the server to the client request and is injected into each route.
- **Router** - contains routes and, after running the application, takes care to find and validate the route for that request. If an error occurs, it calls the error handler.
- **Route** - has a mechanism for checking whether the URL and headers are correct, and if so, it creates the appropriate controller and runs controller method to handle request. Route is injected into each controller as dependencies holder (request + response).
- **Controller** - contains methods to handle the current request, transforms request data for some application service, calls this service with prepared data, and transforms the result into response.
- **Error handler** - is an instance that generates a client response in the event of an error and is injected into the router.
- **Authorization** - is an instance that is injected into the route, its job is to verify whether the authorization requirements are met at that URL.
- **Router builder** - creates instances of the above mentioned objects for the current environment and returns the finished router to application bootstrap.

(TODO)