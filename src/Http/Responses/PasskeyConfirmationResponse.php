<?php

declare(strict_types=1);

namespace Ugarit\Passkeys\Http\Responses;

use Heritage\Http\JsonResponse;
use Heritage\Http\Request;
use Ugarit\Passkeys\Contracts\PasskeyConfirmationResponse as PasskeyConfirmationResponseContract;
use Symfony\Component\HttpFoundation\Response;

class PasskeyConfirmationResponse implements PasskeyConfirmationResponseContract
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
                'redirect' => redirect()->intended()->getTargetUrl(),
            ], 200);
        }

        return redirect()->intended();
    }
}
