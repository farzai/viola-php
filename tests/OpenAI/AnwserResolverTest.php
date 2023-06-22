<?php

use Farzai\Viola\OpenAI\AnswerResolver;

it('can resolve the answer', function () {
    $resolver = new AnswerResolver();

    $answer = <<<'EOT'
SQLQuery: "SELECT * FROM users;"
EOT;

    $this->assertEquals('SELECT * FROM users', $resolver->resolveQueryCommand($answer));
});

it('can resolve the answer with new line', function () {
    $resolver = new AnswerResolver();

    $answer = <<<'EOT'
SQLQuery: "SELECT * FROM users
WHERE id = 1;"
EOT;

    $this->assertEquals('SELECT * FROM users WHERE id = 1', $resolver->resolveQueryCommand($answer));
});

it('cannot resolve the answer', function () {
    $resolver = new AnswerResolver();

    $answer = <<<'EOT'
"SELECT * FROM users"
EOT;

    $resolver->resolveQueryCommand($answer);
})->throws(\Farzai\Viola\Exceptions\UnexpectedAnswer::class);
