<?php

namespace Farzai\Viola\Contracts;

interface Actor
{
    public function handle(string $command);

    public function getPrompt(): string;

    public function getIdentifier(): string;
}
