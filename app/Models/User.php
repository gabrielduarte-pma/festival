<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'password', 'role', 'ativo', 'telefone', 'email_verified_at'])]
#[Hidden(['password', 'invite_token', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail, PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'ativo' => 'boolean',
            'invite_expires_at' => 'datetime',
            'convite_enviado_em' => 'datetime',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function stands(): BelongsToMany
    {
        return $this->belongsToMany(Stand::class, 'stand_user')->orderBy('nome');
    }

    public function podeEditar(Stand $stand): bool
    {
        return $this->isAdmin() || $this->stands()->whereKey($stand->id)->exists();
    }

    /** Gera novo token de convite; guarda só o hash e devolve o token em texto puro. */
    public function gerarConvite(): string
    {
        $token = Str::random(48);
        $this->forceFill([
            'invite_token' => hash('sha256', $token),
            'invite_expires_at' => now()->addDays((int) config('festival.convite_dias', 14)),
        ])->save();

        return $token;
    }

    public static function porConvite(string $token): ?self
    {
        return static::where('invite_token', hash('sha256', $token))
            ->where('invite_expires_at', '>', now())
            ->where('ativo', true)
            ->first();
    }
}
