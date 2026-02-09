<?php

declare(strict_types=1);

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

arch('all source classes use strict types')
    ->expect('Dev2Geek\LaravelInit')
    ->toUseStrictTypes();

arch('commands extend Illuminate Command')
    ->expect('Dev2Geek\LaravelInit\Commands')
    ->toExtend(Illuminate\Console\Command::class);

arch('no env() calls outside config')
    ->expect(['env'])
    ->not->toBeUsed();
