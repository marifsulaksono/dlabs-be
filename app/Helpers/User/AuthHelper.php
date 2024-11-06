<?php

namespace App\Helpers\User;

use App\Helpers\Common;
use App\Http\Resources\UserResource;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

/**
 * Helper khusus untuk authentifikasi pengguna
 *
 * @author Wahyu Agung <wahyuagung26@gmail.com>
 */
class AuthHelper extends Common
{
    /**
     * Proses validasi email dan password
     * jika terdaftar pada database dilanjutkan generate token JWT
     *
     * @param  string  $email
     * @param  string  $password
     * @return void
     */
    public static function login($email, $password)
    {
        try {
            $credentials = ['email' => $email, 'password' => $password];
            if (! $token = JWTAuth::attempt($credentials)) {
                return [
                    'status' => false,
                    'error' => ['Kombinasi email dan password yang kamu masukkan salah'],
                ];
            }
        } catch (JWTException $e) {
            return [
                'status' => false,
                'error' => ['Could not create token.'],
            ];
        }

        return [
            'status' => true,
            'data' => self::createNewToken($token),
        ];
    }

    /**
     * Get the token array structure.
     *
     * @param  string  $token
     * @return \Illuminate\Http\JsonResponse
     */
    protected static function createNewToken($token)
    {
        $user = new UserResource(auth()->user());

        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ];
    }

    public static function logout()
    {
        try {
            $removeToken = JWTAuth::invalidate(JWTAuth::getToken());

            if ($removeToken) {
                //return response JSON
                return [
                    'status' => true,
                    'message' => 'Logout Success!',
                ];
            }
        } catch (JWTException $e) {
            dd($e, JWTAuth::getToken());

            return [
                'status' => false,
                'error' => ['Could not logout token.'],
            ];
        }
    }
}
