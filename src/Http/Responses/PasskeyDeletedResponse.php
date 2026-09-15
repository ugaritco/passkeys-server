<?php

declare(strict_types=1);

namespace Ugarit\Passkeys\Http\Responses;

use Heritage\Http\JsonResponse;
use Heritage\Http\Request;
use Ugarit\Passkeys\Contracts\PasskeyDeletedResponse as PasskeyDeletedResponseContract;
use Symfony\Component\HttpFoundation\Response;

class PasskeyDeletedResponse implements PasskeyDeletedResponseContract
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
            return new JsonResponse(['status' => 'passkey-deleted'], 200);
        }

        return back()->with('status', 'passkey-deleted');
    }
}
