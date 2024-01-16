<?php

namespace Farzai\Viola\Resources;

use Farzai\Viola\Contracts\PromptRepositoryInterface;

class PromptRepository implements PromptRepositoryInterface
{
    /**
     * Compile the prompt.
     */
    public function compile(string $name, array $bindings = []): string
    {
        $content = file_exists(__DIR__."/../../stubs/prompts/{$name}.txt")
            ? file_get_contents(__DIR__."/../../stubs/prompts/{$name}.txt")
            : $name;

        return str_replace(
            array_map(fn ($key) => ":{$key}:", array_keys($bindings)),
            array_values($bindings),
            $content,
        );
    }
}
