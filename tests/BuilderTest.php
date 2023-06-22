<?php

use Farzai\Viola\Builder;
use Farzai\Viola\Viola;

it('can create a new Viola builder instance', function () {
    $builder = Viola::builder();

    $this->assertInstanceOf(Builder::class, $builder);
});

it('can create a new Viola instance', function () {
    $viola = Viola::builder()
        ->setApiKey('secret')
        ->build();

    $this->assertInstanceOf(Viola::class, $viola);
});
