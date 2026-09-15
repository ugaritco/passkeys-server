<?php

declare(strict_types=1);

namespace Ugarit\Passkeys;

use Heritage\Database\Eloquent\Builder;
use Heritage\Database\Eloquent\Casts\Attribute;
use Heritage\Database\Eloquent\Model;
use Heritage\Database\Eloquent\Relations\BelongsTo;
use Heritage\Support\Carbon;
use Ugarit\Passkeys\Contracts\PasskeyUser;
use Ugarit\Passkeys\Support\Aaguids;

/**
 * @mixin Builder<Passkey>
 *
 * @property int $id
 * @property int|string $user_id
 * @property string $name
 * @property string $credential_id
 * @property array<string, mixed> $credential
 * @property Carbon|null $last_used_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read PasskeyUser $user
 * @property-read string|null $authenticator
 */
class Passkey extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'credential_id',
        'credential',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        'authenticator',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'credential' => 'json',
            'last_used_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the passkey.
     *
     * @return BelongsTo<Model, $this>
     */
    public function user(): BelongsTo
    {
        /** @var class-string<Model> $model */
        $model = Passkeys::userModel();

        return $this->belongsTo($model, 'user_id');
    }

    /**
     * Get the authenticator name based on the AAGUID.
     */
    protected function authenticator(): Attribute
    {
        return Attribute::get(function (): ?string {
            $aaguid = $this->credential['aaguid'] ?? null;

            if (! is_string($aaguid) || $aaguid === Aaguids::unknown()) {
                return null;
            }

            return Aaguids::labelFor($aaguid);
        });
    }
}
