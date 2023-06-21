<?php

namespace Farzai\Viola;

use Farzai\Viola\Contracts\ViolaResponseInterface;

final class ViolaResponse implements ViolaResponseInterface
{
    /**
     * Create a new Viola response instance.
     */
    public function __construct(
        private array $body,
        private ?string $query,
        private ?array $results)
    {
    }

    /**
     * Return the answer.
     */
    public function getAnswer(): string
    {
        foreach ($this->body['choices'] as $choice) {
            if ($choice['message']['role'] === 'assistant') {
                return $choice['message']['content'];
            }
        }

        throw new \Exception('No answer found.');
    }

    /**
     * Return the query command.
     */
    public function getQueryCommand(): string
    {
        if (! $this->query) {
            throw new \Exception('No query command found.');
        }

        return $this->query;
    }

    /**
     * Return the query results.
     *
     * @return array<array<string, mixed>>
     */
    public function getResults(): array
    {
        if (! $this->results) {
            throw new \Exception('No query results found.');
        }

        return $this->results;
    }
}
