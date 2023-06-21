<?php

namespace Farzai\Viola\Contracts;

interface PromptRepositoryInterface
{
    /**
     * Compile the prompt.
     */
    public function compile(string $name, array $bindings = []): string;
}