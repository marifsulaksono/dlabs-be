<?php

namespace App\Http\Middleware;

use Closure;
use DateTime;
use Exception;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
{
    /**
     * Middleware untuk validasi token yang dikirim.
     * Jika user melakukan perubahan email / password yang berhubungan dengan keamanan
     * maka user tersebut harus login ulang.
     *
     * Pengecekannya menggunakan tgl data keamana diubah > tgl keamanan pada token yg disimpan di frontend
     */
    public function handle($request, Closure $next, $roles = '')
    {
        try {
            $userModel = JWTAuth::parseToken()->authenticate();
            $userToken = JWTAuth::parseToken()->getPayload()->get('user');

            $updatedDb = new DateTime($userModel['updated_security']);
            $updatedToken = new DateTime($userToken['updated_security']);

            // Cek jika ada perubahan pengaturan keamanan, user harus login ulang
            if ($updatedDb > $updatedToken) {
                return response()->failed(['Terdapat perubahan pengaturan keamanan, silahkan login ulang'], 403);
            }

            /**
             * Middleware untuk mengecek permission user ketika melakukan request ke salah satu routes
             * Pada saat membuat sebuah routes, tambahkan hak akses apa yang boleh mengakses endpoint ini
             *
             * Contohnya : Route::get('/users', [UserController::class, 'index'])->middleware(['auth.api','role:user_view']);
             *
             * Routes di atas hanya dapat diakses jika request dilengkapi dengan token JWT dan user memiliki akses "user_view"
             */
            if (! empty($roles) && ! $userModel->isHasRole($roles)) {
                return response()->failed(['Anda tidak memiliki credential untuk mengakses data ini'], 403);
            }
        } catch (Exception $e) {
            if ($e instanceof TokenInvalidException) {
                return response()->failed(['Token yang anda gunakan tidak valid'], 401);
            } elseif ($e instanceof TokenExpiredException) {
                return response()->failed(['Token anda telah kadaluarsa, silahkan login ulang'], 401);
            } else {
                return response()->failed(['Silahkan login terlebih dahulu. '.$e->getMessage()], 401);
            }
        }

        return $next($request);
    }
}
