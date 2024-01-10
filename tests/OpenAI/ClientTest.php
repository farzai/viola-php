<?php

use Farzai\Viola\OpenAI\Client;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;

it('should send completion success', function () {
    $psrResponse = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
    $psrResponse->expects($this->once())
        ->method('getStatusCode')
        ->willReturn(200);

    $psrClient = $this->createMock(ClientInterface::class);
    $psrClient->expects($this->once())
        ->method('sendRequest')
        ->willReturn($psrResponse);

    $psrLogger = $this->createMock(LoggerInterface::class);
    $psrLogger->expects($this->once())
        ->method('info');

    $client = new Client('secret', $psrClient, $psrLogger);

    $response = $client->sendCompletion([]);

    expect($response)->toBeInstanceOf(\Farzai\Transport\Contracts\ResponseInterface::class);
    expect($response->statusCode())->toBe(200);
});
