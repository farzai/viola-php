<?php

namespace Farzai\Viola\Contracts;

interface AnswerResolverInterface
{
    /**
     * Resolve the answer.
     *
     * @return mixed
     */
    public function resolveQueryCommand(string $content): string;
}
