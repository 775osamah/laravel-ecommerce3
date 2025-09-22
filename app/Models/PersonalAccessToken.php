<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;
use Illuminate\Database\Eloquent\Model;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    /**
     * Create a new personal access token for the user.
     */
    public static function createToken(Model $tokenable, string $name, array $abilities = ['*'])
    {
        // Generate the plain text token (same as parent)
        $plainTextToken = sprintf(
            '%s%s%s',
            $tokenable->getKey(), 
            '|',
            \Illuminate\Support\Str::random(40)
        );
        
        // Hash the token for storage
        $hashedToken = hash('sha256', $plainTextToken);
        
        // Create the token record
        $token = static::forceCreate([
            'tokenable_type' => $tokenable->getMorphClass(),
            'tokenable_id' => $tokenable->getKey(),
            'name' => $name,
            'token' => $hashedToken, // Use the hashed token
            'abilities' => $abilities,
        ]);
        
        return new \Laravel\Sanctum\NewAccessToken($token, $plainTextToken);
    }
    
    /**
     * Find the token instance matching the given token.
     */
    public static function findToken($token)
    {
        if (strpos($token, '|') === false) {
            return static::where('token', $token)->first();
        }
        
        // Hash the token before searching
        $hashedToken = hash('sha256', $token);
        
        return static::where('token', $hashedToken)->first();
    }
}