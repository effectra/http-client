<?php

namespace Effectra\Http\Client\Exception;

use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;


class NetworkException extends RuntimeException implements NetworkExceptionInterface
{
    /**
     * The request that triggered the network exception.
     *
     * @var RequestInterface
     */
    private $request;

    /**
     * The response from the server, if available.
     *
     * @var ResponseInterface|null
     */
    private $response;

    /**
     * Constructs a new NetworkException with the specified message, request, response, and previous exception.
     *
     * @param string $message The exception message.
     * @param RequestInterface $request The request that triggered the network exception.
     * @param ResponseInterface|null $response The response from the server, if available.
     * @param \Throwable|null $previous The previous exception if nested exception.
     */
    public function __construct($message = "", RequestInterface $request, ResponseInterface $response = null, \Throwable $previous = null)
    {
        $this->request = $request;
        $this->response = $response;
        parent::__construct($message, 0, $previous);
    }

    /**
     * Returns the request that triggered the network exception.
     *
     * @return RequestInterface The request object.
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * Returns the response from the server, if available.
     *
     * @return ResponseInterface|null The response object or null if not available.
     */
    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }
}
