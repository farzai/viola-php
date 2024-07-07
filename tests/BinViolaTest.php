<?php

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

beforeEach(function () {
    $this->storage = new Farzai\Viola\Storage\TemporaryFilesystemStorage();

    $this->storage->remove('api_key');
});

it('should use TemporaryFilesystemStorage by default', function () {
    $this->storage->set('api_key', 'test');

    $process = new Process(['php', 'bin/viola', 'config:show']);
    $process->run();

    if (! $process->isSuccessful()) {
        throw new ProcessFailedException($process);
    }

    $output = $process->getOutput();
    expect($output)->toContain('Database Connection');
});
