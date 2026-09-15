<?php

declare(strict_types=1);

namespace Ugarit\Passkeys\Actions;

use Heritage\Contracts\Auth\Authenticatable;
use Ugarit\Passkeys\Events\PasskeyDeleted;
use Ugarit\Passkeys\Passkey;

class DeletePasskey
{
    /**
     * Delete the given passkey.
     */
    public function __invoke(Authenticatable $user, Passkey $passkey): void
    {
        $passkey->delete();

        PasskeyDeleted::dispatch($user, $passkey);
    }
}
