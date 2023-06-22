<?php

namespace Farzai\Viola\OpenAI;

use Farzai\Viola\Contracts\AnswerResolverInterface;
use Farzai\Viola\Exceptions\UnexpectedAnswer;

class AnswerResolver implements AnswerResolverInterface
{
    /**
     * Resolve the answer.
     *
     * @return mixed
     */
    public function resolveQueryCommand(string $content): string
    {
        // Trim the content.
        $content = trim($content);

        // Remove the new line.
        $content = implode(' ', array_map(fn ($line) => trim($line), explode("\n", $content)));

        if (preg_match('/SQLQuery: "(.*?)"/im', $content, $matches)) {
            return rtrim($matches[1], ';');
        }

        throw new UnexpectedAnswer();
    }
}
