<?php

namespace Farzai\Viola\Contracts;

interface TokenRepository
{
    /**
     * Get the ChatGPT API token.
     */
    public function getToken(): ?string;

    /**
     * Set the ChatGPT API token.
     */
    public function setToken(string $token): void;

    /**
     * Forget API token.
     */
    public function forget(): void;
}
