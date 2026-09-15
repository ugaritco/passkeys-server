<?php

declare(strict_types=1);

namespace Ugarit\Passkeys\Events;

use Heritage\Contracts\Auth\Authenticatable;
use Heritage\Foundation\Events\Dispatchable;
use Heritage\Queue\SerializesModels;
use Ugarit\Passkeys\Passkey;

class PasskeyDeleted
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Authenticatable $user,
        public Passkey $passkey
    ) {
        //
    }
}
