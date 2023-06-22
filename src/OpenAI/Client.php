<?php

namespace Farzai\Viola\OpenAI;

use Farzai\Transport\Response;
use Farzai\Transport\Transport;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

class Client
{
    /**
     * @var Transport
     */
    private $transport;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * Client constructor.
     */
    public function __construct(Transport $transport, string $apiKey)
    {
        $this->transport = $transport;
        $this->apiKey = $apiKey;
    }

    public function sendCompletion(array $body): ResponseInterface
    {
        $headers = $this->getHeaders();

        $request = new Request('POST', 'https://api.openai.com/v1/chat/completions', $headers, json_encode($body));

        $response = $this->transport->sendRequest($request);

        return new Response($request, $response);
    }

    public function getHeaders(): array
    {
        return [
            'Authorization' => 'Bearer '.$this->apiKey,
            'Content-Type' => 'application/json',
        ];
    }
}
