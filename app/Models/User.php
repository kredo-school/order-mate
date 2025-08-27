<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

   /**
 * @property int $role
 * @method bool isAdmin()
 * @method bool isManager()
 */


class User extends Authenticatable
{

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
        ];
    }

<<<<<<< HEAD
    public function store()
    {
        return $this->hasOne(Store::class, 'user_id');
    }
=======
    public function store(){
        return $this->hasOne(Store::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 2;
        // もし role カラムを使っていないなら、is_admin というフラグを参照してもOK
        // return (bool) $this->is_admin;
    }

    /**
     * マネージャーかどうか判定
     */
    public function isManager(): bool
    {
        return $this->role === 1;
    }

>>>>>>> 9b0db3f2b9b7a7f7c6f514460ba7e44a5234c217
}
