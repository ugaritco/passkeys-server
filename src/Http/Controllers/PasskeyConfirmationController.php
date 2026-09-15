<?php

declare(strict_types=1);

namespace Ugarit\Passkeys\Http\Controllers;

use Heritage\Auth\AuthenticationException;
use Heritage\Http\JsonResponse;
use Heritage\Http\Request;
use Heritage\Routing\Controller;
use Heritage\Session\Store as SessionStore;
use Heritage\Support\Facades\Auth;
use Heritage\Support\Facades\Config;
use Ugarit\Passkeys\Actions\GenerateVerificationOptions;
use Ugarit\Passkeys\Actions\VerifyPasskey;
use Ugarit\Passkeys\Contracts\PasskeyConfirmationResponse;
use Ugarit\Passkeys\Contracts\PasskeyUser;
use Ugarit\Passkeys\Http\Requests\PasskeyVerificationRequest;
use Ugarit\Passkeys\Support\WebAuthn;
use RuntimeException;

class PasskeyConfirmationController extends Controller
{
    /**
     * Get passkey confirmation options for the authenticated user.
     */
    public function index(Request $request, GenerateVerificationOptions $generate): JsonResponse
    {
        $user = Auth::guard(Config::string('passkeys.guard'))->user()
            ?? throw new AuthenticationException;

        if (! $user instanceof PasskeyUser) {
            throw new RuntimeException('User model must implement the PasskeyUser contract.');
        }

        $options = $generate($user);

        $serialized = WebAuthn::toJson($options);

        $request->session()->put('passkey.verification_options', $serialized);

        return response()->json([
            'options' => WebAuthn::toBrowserArray($options),
        ]);
    }

    /**
     * Confirm the user's password via passkey verification.
     */
    public function store(
        PasskeyVerificationRequest $request,
        VerifyPasskey $verify,
    ): PasskeyConfirmationResponse {
        $user = Auth::guard(Config::string('passkeys.guard'))->user()
            ?? throw new AuthenticationException;

        if (! $user instanceof PasskeyUser) {
            throw new RuntimeException('User model must implement the PasskeyUser contract.');
        }

        $verify(
            $request->credential(),
            $request->verificationOptions(),
            $user
        );

        /** @var SessionStore $session */
        $session = $request->session();

        $session->passwordConfirmed();

        return app(PasskeyConfirmationResponse::class);
    }
}
