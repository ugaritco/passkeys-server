<?php

declare(strict_types=1);
use Heritage\Foundation\Http\FormRequest;
use Heritage\Routing\Controller;
use Ugarit\Passkeys\PasskeysServiceProvider;

arch('strict types')
    ->expect('Ugarit\Passkeys')
    ->toUseStrictTypes();

arch('no debugging')
    ->expect(['dd', 'dump', 'var_dump', 'die', 'ray'])
    ->not->toBeUsed();

arch('controllers')
    ->expect('Ugarit\Passkeys\Http\Controllers')
    ->toExtend(Controller::class)
    ->toHaveSuffix('Controller');

arch('actions are invokable')
    ->expect('Ugarit\Passkeys\Actions')
    ->toBeInvokable();

arch('exceptions')
    ->expect('Ugarit\Passkeys\Exceptions')
    ->toExtend(Exception::class)
    ->toHaveSuffix('Exception');

arch('requests')
    ->expect('Ugarit\Passkeys\Http\Requests')
    ->toExtend(FormRequest::class)
    ->toHaveSuffix('Request');

arch('no direct env calls')
    ->expect('env')
    ->not->toBeUsedIn('Ugarit\Passkeys')
    ->ignoring([
        PasskeysServiceProvider::class,
    ]);

arch('tests')
    ->expect('Tests')
    ->not->toBeUsedIn('Ugarit\Passkeys');
