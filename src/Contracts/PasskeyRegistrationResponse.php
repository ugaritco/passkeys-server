<?php

declare(strict_types=1);

namespace Ugarit\Passkeys\Contracts;

use Heritage\Contracts\Support\Responsable;
use Ugarit\Passkeys\Passkey;

interface PasskeyRegistrationResponse extends Responsable
{
    /**
     * Set the passkey that was registered.
     */
    public function withPasskey(Passkey $passkey): static;
}
