<?php

declare(strict_types=1);

namespace Ugarit\Passkeys\Http\Controllers;

use Heritage\Contracts\Auth\StatefulGuard;
use Heritage\Http\JsonResponse;
use Heritage\Http\Request;
use Heritage\Routing\Controller;
use Heritage\Support\Facades\Auth;
use Heritage\Support\Facades\Config;
use Ugarit\Passkeys\Actions\GenerateVerificationOptions;
use Ugarit\Passkeys\Actions\VerifyPasskey;
use Ugarit\Passkeys\Contracts\PasskeyLoginResponse;
use Ugarit\Passkeys\Exceptions\InvalidPasskeyException;
use Ugarit\Passkeys\Http\Requests\PasskeyVerificationRequest;
use Ugarit\Passkeys\Passkeys;
use Ugarit\Passkeys\Support\WebAuthn;
use RuntimeException;

class PasskeyLoginController extends Controller
{
    /**
     * Get passkey login options.
     */
    public function index(Request $request, GenerateVerificationOptions $generate): JsonResponse
    {
        $options = $generate();

        $serialized = WebAuthn::toJson($options);

        $request->session()->put('passkey.verification_options', $serialized);

        return response()->json([
            'options' => WebAuthn::toBrowserArray($options),
        ]);
    }

    /**
     * Verify the passkey and log the user in.
     */
    public function store(
        PasskeyVerificationRequest $request,
        VerifyPasskey $verify,
    ): PasskeyLoginResponse {
        $passkey = $verify(
            $request->credential(),
            $request->verificationOptions()
        );

        $guard = Auth::guard(Config::string('passkeys.guard'));

        if (! $guard instanceof StatefulGuard) {
            throw new RuntimeException('Passkeys requires a stateful authentication guard.');
        }

        if (! Passkeys::allowsLogin($request, $passkey)) {
            throw InvalidPasskeyException::make('Unable to sign in with this account.');
        }

        $guard->login($passkey->user, $request->remember());

        $request->session()->regenerate();

        return app(PasskeyLoginResponse::class);
    }
}
