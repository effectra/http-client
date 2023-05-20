<?php

namespace Effectra\Http\Client;

use CurlHandle;

use Effectra\Http\Client\Exception\ClientException;

use Effectra\Http\Message\Request;
use Effectra\Http\Message\Response;
use Effectra\Http\Message\Stream;
use Effectra\Http\Message\Uri;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class Client implements ClientInterface
{
    /**
     * @var CurlHandle cURL handle
     */
    private $curl;

    /**
     * CurlClient constructor.
     */
    public function __construct()
    {
        $this->curl = curl_init();
        curl_setopt_array($this->curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $method = $request->getMethod();
        $uri = $request->getUri();
        $headers = $request->getHeaders();
        $body = $request->getBody();

        if (!$body instanceof StreamInterface) {
            $body = $this->createStream((string) $body);
        }

        $stream = $body->isSeekable() ? $body : $this->createStream($body);

        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($this->curl, CURLOPT_URL, (string) $uri);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $stream->getContents());
        curl_setopt($this->curl, CURLOPT_INFILE, $stream->detach());
        curl_setopt($this->curl, CURLOPT_INFILESIZE, $stream->getSize());

        $responseBody = curl_exec($this->curl);
        $responseCode = curl_getinfo($this->curl, CURLINFO_RESPONSE_CODE);
        $responseHeadersSize = curl_getinfo($this->curl, CURLINFO_HEADER_SIZE);

        $responseHeaders = $this->parseHeaders(substr($responseBody, 0, $responseHeadersSize));
        $responseBody = substr($responseBody, $responseHeadersSize);

        if ($responseBody === false) {
            throw new ClientException("An error occurred while processing the request.");
        }

        return new Response(
           statusCode: $responseCode, 
           headers: $responseHeaders, 
           body: $responseBody,
           reasonPhrase: Response::$statusTexts[$responseCode]
        );
    }

    public function send(RequestInterface $request, array $options = [])
    {
        $this->setRequestWithOptions($request, $options);
    }

    /**
     * Create a stream from a string or a stream object.
     *
     * @param string|StreamInterface $body
     * @return StreamInterface
     */
    private function createStream($body): StreamInterface
    {
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, (string) $body);
        rewind($stream);

        return new Stream($stream);
    }

    /**
     * Parse headers from a string.
     *
     * @param string $headers
     * @return array
     */
    private function parseHeaders(string $headers): array
    {
        $headers =$this->removeHttpStatus($headers);
        $headers = explode("\r\n", $headers);
        $result = [];

        foreach ($headers as $header) {
            if ($header !== '') {
                if (strpos($header, ':') !== false) {
                    [$key, $value] = explode(':', $header, 2);
                    $key = trim($key);
                    $value = trim($value);
                    $result[$key][] = $value;
                } else {
                    $result[] = $header;
                }
            }
        }

        return $result;
    }


    private function removeHttpStatus(string $text) :string
    {
        $pattern = '/HTTP\/\d\.\d \d{3} [A-Za-z ]+/';
        $replacement = '';

        $result = preg_replace($pattern, $replacement, $text);

        return $result;
    }



    public function get(string $uri, ?array $options = []): ResponseInterface
    {
        /**
         * @var \Effectra\Http\Message\Request $request
         */
        $request = $this->setRequestWithOptions(new Request('GET', new Uri($uri)), $options);
        $response = $this->sendRequest($request);

        return $response;
    }
    public function post(string $uri, ?array $options = []): ResponseInterface
    {
        /**
         * @var \Effectra\Http\Message\Request $request
         */
        $request = $this->setRequestWithOptions(new Request('POST', new Uri($uri)), $options);
        $response = $this->sendRequest($request);

        return $response;
    }
    public function put(string $uri, ?array $options = []): ResponseInterface
    {
        /**
         * @var \Effectra\Http\Message\Request $request
         */
        $request = $this->setRequestWithOptions(new Request('PUT', new Uri($uri)), $options);
        $response = $this->sendRequest($request);

        return $response;
    }
    public function patch(string $uri, ?array $options = []): ResponseInterface
    {
        /**
         * @var \Effectra\Http\Message\Request $request
         */
        $request = $this->setRequestWithOptions(new Request('PATCH', new Uri($uri)), $options);
        $response = $this->sendRequest($request);

        return $response;
    }
    public function delete(string $uri, ?array $options = []): ResponseInterface
    {
        /**
         * @var \Effectra\Http\Message\Request $request
         */
        $request = $this->setRequestWithOptions(new Request('DELETE', new Uri($uri)), $options);
        $response = $this->sendRequest($request);

        return $response;
    }
    public function head(string $uri, ?array $options = []): ResponseInterface
    {
        /**
         * @var \Effectra\Http\Message\Request $request
         */
        $request = $this->setRequestWithOptions(new Request('HEAD', new Uri($uri)), $options);
        $response = $this->sendRequest($request);

        return $response;
    }

    public function setRequestWithOptions(Request $request, array $options = [])
    {
        $headers = $options['headers'] ?? [];
        $body =  $options['body'] ?? new Stream('');

        return $request
            ->withHeaders($headers)
            ->withBody($body);
    }
}
