<?php

namespace Farzai\Viola\Storage;

use Farzai\Viola\Contracts\PromptRepositoryInterface;

class PromptRepository implements PromptRepositoryInterface
{
    /**
     * Compile the prompt.
     */
    public function compile(string $name, array $bindings = []): string
    {
        $content = file_get_contents(__DIR__."/../../stubs/prompts/{$name}.txt");
        
        return str_replace(
            array_map(fn ($key) => ":{$key}:", array_keys($bindings)),
            array_values($bindings),
            $content,
        );
    }
}