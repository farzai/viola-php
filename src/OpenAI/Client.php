<?php

namespace Farzai\Viola\OpenAI;

use Farzai\Transport\Contracts\ResponseInterface;
use Farzai\Transport\Response;
use Farzai\Transport\Transport;
use Farzai\Transport\TransportBuilder;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;

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
    public function __construct(?ClientInterface $client, ?LoggerInterface $logger, string $apiKey)
    {
        $this->apiKey = $apiKey;

        $builder = TransportBuilder::make();
        if ($client) {
            $builder->setClient($client);
        }

        if ($logger) {
            $builder->setLogger($logger);
        }

        $this->transport = $builder->build();
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
