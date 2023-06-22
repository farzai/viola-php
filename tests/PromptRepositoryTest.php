<?php

use Farzai\Viola\Resources\PromptRepository;

it('should compile the prompt', function () {
    $prompt = new PromptRepository();

    $compiled = $prompt->compile('This is a :foo: test', [
        'foo' => 'bar',
    ]);

    expect($compiled)->toBe('This is a bar test');
});

it('should compile the prompt from the stubs', function () {
    $prompt = new PromptRepository();

    $compiled = $prompt->compile('tables', [
        'question' => 'What is total number of users?',
        'tables' => 'users, posts, comments',
    ]);

    expect($compiled)->toBe(<<<'EOF'
Given the below input question and list of potential tables, output a comma separated list of the table names that may be necessary to answer this question.

Question: What is total number of users?

Tables:
users, posts, comments

Relevant Table Names:

EOF);
});
