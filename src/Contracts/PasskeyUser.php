<?php

declare(strict_types=1);

namespace Ugarit\Passkeys\Contracts;

use Heritage\Contracts\Auth\Authenticatable;
use Heritage\Database\Eloquent\Model;
use Heritage\Database\Eloquent\Relations\HasMany;

interface PasskeyUser extends Authenticatable
{
    /**
     * Get the passkeys associated with the user.
     *
     * @return HasMany<Passkey, Model>
     *
     * @phpstan-return HasMany<\Ugarit\Passkeys\Passkey, Model>
     */
    public function passkeys(): HasMany;

    /**
     * Determine if the user has any passkeys enabled.
     */
    public function hasPasskeysEnabled(): bool;

    /**
     * Get the value of the model's primary key.
     *
     * @return mixed
     */
    public function getKey();

    /**
     * Get the unique user handle for WebAuthn.
     */
    public function getPasskeyUserHandle(): string;

    /**
     * Get the display name for WebAuthn registration.
     */
    public function getPasskeyDisplayName(): string;

    /**
     * Get the username for WebAuthn registration.
     */
    public function getPasskeyUsername(): string;
}
