<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Auth;
use Firebase\JWT\JWT;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'role_id', 'first_name', 'last_name', 'phone_number', 'email', 'position', 'photo', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function fullname() {
        return $this->first_name . " " . $this->last_name;
    }

    public function setPasswordAttribute($password) {
        $this->attributes['password'] = bcrypt($password);
    }


    /**
     * Roles and permissions
     */
    public function role() {
        return $this->belongsTo(Role::class);
    }

    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->role->name == $role; 
        }
        
        // else it's a collection
        return $role->contains('id', $this->role_id);
    }


    /**
     * Generate a new token
     */
    public function generateToken()
    {
        $payload = [
            'iss' => "napa-automative",  
            'sub' => $this->id,
            'iat' => time(),  
            'exp' => time() + 3600*24, // 1 day
            'data' => [
                'fullname' => $this->fullname(),
            ]
        ];
        
        return JWT::encode($payload, config('jwt.secret_key'));
    }


    /**
     * Update verified_at after first time successful login
     */
    public function verifyAccount()
    {
        if (!$this->verified_at) {
            $this->verified_at = Carbon::now();
            $this->save();
        }
    }


    /**
     * Generates unique username from first and last names
     * @return username
     */
    public function generateUsername()
    {
        $fullName = cryllicToLatin("{$this->first_name}_{$this->last_name}");
        $username = $fullName;
        $counter = 2;

        while (self::where('username', $username)->exists()) {
            $username = $fullName . $counter;
            $counter++;
        }

        return $username;
    }
}
