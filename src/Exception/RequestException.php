<?php

namespace Effectra\Http\Client\Exception;

use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\RequestInterface;
use RuntimeException;

class RequestException extends RuntimeException implements RequestExceptionInterface
{
    /**
     * The request that caused the exception.
     *
     * @var RequestInterface
     */
    private $request;

    /**
     * Constructs a new RequestException with the specified message and request.
     *
     * @param string $message The exception message.
     * @param RequestInterface $request The request that caused the exception.
     * @param \Throwable|null $previous The previous exception if nested exception.
     */
    public function __construct($message = '', RequestInterface $request, \Throwable $previous = null)
    {
        $this->request = $request;
        parent::__construct($message, 0, $previous);
    }

    /**
     * Returns the request that caused the exception.
     *
     * @return RequestInterface The request object.
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
