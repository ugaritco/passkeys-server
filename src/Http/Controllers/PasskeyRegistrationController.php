<?php

declare(strict_types=1);

namespace Ugarit\Passkeys\Http\Controllers;

use Heritage\Auth\AuthenticationException;
use Heritage\Http\JsonResponse;
use Heritage\Http\Request;
use Heritage\Routing\Controller;
use Heritage\Support\Facades\Auth;
use Heritage\Support\Facades\Config;
use Ugarit\Passkeys\Actions\DeletePasskey;
use Ugarit\Passkeys\Actions\GenerateRegistrationOptions;
use Ugarit\Passkeys\Actions\StorePasskey;
use Ugarit\Passkeys\Contracts\PasskeyDeletedResponse;
use Ugarit\Passkeys\Contracts\PasskeyRegistrationResponse;
use Ugarit\Passkeys\Contracts\PasskeyUser;
use Ugarit\Passkeys\Http\Requests\PasskeyRegistrationRequest;
use Ugarit\Passkeys\Passkey;
use Ugarit\Passkeys\Support\WebAuthn;
use RuntimeException;

class PasskeyRegistrationController extends Controller
{
    /**
     * Get passkey registration options for the authenticated user.
     */
    public function index(Request $request, GenerateRegistrationOptions $generate): JsonResponse
    {
        $user = Auth::guard(Config::string('passkeys.guard'))->user()
            ?? throw new AuthenticationException;

        $options = $generate($user);

        $serialized = WebAuthn::toJson($options);

        $request->session()->put('passkey.registration_options', $serialized);

        return response()->json([
            'options' => WebAuthn::toBrowserArray($options),
        ]);
    }

    /**
     * Store a new passkey for the authenticated user.
     */
    public function store(
        PasskeyRegistrationRequest $request,
        StorePasskey $storePasskey,
    ): PasskeyRegistrationResponse {
        $user = Auth::guard(Config::string('passkeys.guard'))->user()
            ?? throw new AuthenticationException;

        $passkey = $storePasskey(
            $user,
            $request->string('name')->toString(),
            $request->credential(),
            $request->registrationOptions()
        );

        return app(PasskeyRegistrationResponse::class)->withPasskey($passkey);
    }

    /**
     * Delete a passkey for the authenticated user.
     */
    public function destroy(
        Passkey $passkey,
        DeletePasskey $deletePasskey
    ): PasskeyDeletedResponse {
        $user = Auth::guard(Config::string('passkeys.guard'))->user()
            ?? throw new AuthenticationException;

        if (! $user instanceof PasskeyUser) {
            throw new RuntimeException('User model must implement the PasskeyUser contract.');
        }

        abort_unless($passkey->user_id === $user->getKey(), 403);

        $deletePasskey($user, $passkey);

        return app(PasskeyDeletedResponse::class);
    }
}
