# Effectra HTTP Client

Effectra HTTP Client is a lightweight PHP library that simplifies sending HTTP requests and handling responses. It provides an intuitive interface and utilizes cURL for efficient communication with HTTP-based APIs and services.

## Installation

You can install the library using Composer. Run the following command in your project directory:

```shell
composer require effectra/http-client
```

## Usage

To send an HTTP request, create an instance of the `Client` class and call the `sendRequest` method:

```php
use Effectra\Http\Client\Client;
use Effectra\Http\Message\Request;
use Effectra\Http\Message\Uri;

$client = new Client();

// Create a request object
$request = new Request('GET', new Uri('https://api.example.com/users'));

// Send the request and get the response
$response = $client->sendRequest($request);

// Access the response status code
$status = $response->getStatusCode();

// Access the response headers
$headers = $response->getHeaders();

// Access the response body as a string
$body = $response->getBody()->getContents();

// Process the response data as needed
// ...
```

You can also use convenience methods like `get`, `post`, `put`, `patch`, `delete`, and `head` to send requests with specific methods.

```php
use Effectra\Http\Client\Client;
use Effectra\Http\Message\Request;
use Effectra\Http\Message\Uri;

$client = new Client();

$client->get('https://api.example.com/users');

// Send the request and get the response
$response = $client->sendRequest($request);

// Access the response status code
$status = $response->getStatusCode();

// Access the response headers
$headers = $response->getHeaders();

// Access the response body as a string
$body = $response->getBody()->getContents();

// Process the response data as needed
// ...
```

## Contributing

Contributions are welcome! Feel free to open issues or submit pull requests for any improvements or bug fixes you'd like to contribute.

## License

This library is licensed under the [MIT License](LICENSE).
```

Feel free to customize the README file as per your specific library features and requirements.