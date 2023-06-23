<?php

use Farzai\Viola\OpenAI\Client;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;

it('should send completion success', function () {
    $psrClient = \Mockery::mock(ClientInterface::class)
        ->shouldReceive('sendRequest')->once()
        ->andReturn(\Mockery::mock(\Psr\Http\Message\ResponseInterface::class)
            ->shouldReceive('getStatusCode')
            ->andReturn(200)
            ->getMock()
        )
        ->getMock();

    $psrLogger = \Mockery::mock(LoggerInterface::class)
        ->shouldReceive('debug')
        ->getMock();

    $client = new Client('secret', $psrClient, $psrLogger);

    expect($client->sendCompletion([]))
        ->toBeInstanceOf(\Farzai\Transport\Contracts\ResponseInterface::class);
});
