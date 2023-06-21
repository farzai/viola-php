<?php

namespace Farzai\Viola\Contracts;

interface ViolaResponseInterface
{
    /**
     * Return the answer.
     */
    public function getAnswer(): string;

    /**
     * Return the query command.
     */
    public function getQueryCommand(): string;

    /**
     * Return the query results.
     *
     * @return array<array<string, mixed>>
     */
    public function getResults(): array;
}
