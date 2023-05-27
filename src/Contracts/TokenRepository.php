<?php

namespace Farzai\Viola\Contracts;

interface TokenRepository
{
    /**
     * Get the ChatGPT API token.
     * 
     * @return string|null
     */
    public function getToken(): ?string;

    /**
     * Set the ChatGPT API token.
     * 
     * @param string $token
     * @return void
     */
    public function setToken(string $token): void;

    /**
     * Forget API token.
     * 
     * @return void
     */
    public function forget(): void;
}