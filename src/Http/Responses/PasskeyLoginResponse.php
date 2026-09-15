<?php

declare(strict_types=1);

namespace Ugarit\Passkeys\Http\Responses;

use Heritage\Http\JsonResponse;
use Heritage\Http\Request;
use Ugarit\Passkeys\Contracts\PasskeyLoginResponse as PasskeyLoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

class PasskeyLoginResponse implements PasskeyLoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  Request  $request
     * @return Response
     */
    public function toResponse($request)
    {
        if ($request->wantsJson()) {
            return new JsonResponse([
                'redirect' => redirect()->intended(config('passkeys.redirect', '/'))->getTargetUrl(),
            ], 200);
        }

        return redirect()->intended(config('passkeys.redirect', '/'));
    }
}
