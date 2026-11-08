<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Rol;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The model's default values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'role' => 'cliente',
    ];

    /**
     * Determine whether the user has the administrator role.
     */
    public function isAdmin(): bool
    {
        return $this->role === Rol::Admin;
    }

    /**
     * Determine whether the user has the customer role.
     */
    public function isCliente(): bool
    {
        return $this->role === Rol::Cliente;
    }

    /**
     * Get the orders placed by the user.
     *
     * @return HasMany<Pedido, $this>
     */
    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class);
    }

    /**
     * Get the inventory movements registered by the user.
     *
     * @return HasMany<MovimientoInventario, $this>
     */
    public function movimientosInventario(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class);
    }

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
            'role' => Rol::class,
        ];
    }
}
