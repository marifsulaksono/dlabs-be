<?php

namespace App\Http\Controllers\Api;

use App\Helpers\User\UserHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class UserController extends Controller
{
    private $userHelper;

    public function __construct()
    {
        $this->userHelper = new UserHelper;
    }

    /**
     * Delete data user
     *
     * @param  mixed  $id
     */
    public function destroy($id)
    {
        $user = $this->userHelper->delete($id);

        if (! $user) {
            return response()->failed(['Mohon maaf data pengguna tidak ditemukan']);
        }

        Redis::del('user:'.$id); // Hapus data pengguna dari Redis

        return response()->success($user, 'User berhasil dihapus');
    }

    /**
     * Mengambil data user dilengkapi dengan pagination
     */
    public function index(Request $request)
    {
        $filter = [
            'name' => $request->name ?? '',
            'email' => $request->email ?? '',
        ];
        $users = $this->userHelper->getAll($filter, $request->page ?? 1, $request->limit ?? 25, $request->sort ?? '');

        return response()->success(new UserCollection($users['data']));
    }

    /**
     * Menampilkan user secara spesifik dari tabel user
     *
     * @param  mixed  $id
     */
    public function show($id)
    {
        $userData = Redis::get('user:'.$id);

        if ($userData) {
            $user = new UserResource((object) json_decode($userData, true));

            return response()->success($user);
        }

        $user = $this->userHelper->getById($id);

        if (! ($user['status'])) {
            return response()->failed(['Data user tidak ditemukan'], 404);
        }

        $return = json_decode(json_encode(new UserResource($user['data'])), true);

        Redis::set('user:'.$id, json_encode($return));

        return response()->success(new UserResource($user['data']));
    }

    /**
     * Membuat data user baru & disimpan ke tabel user
     */
    public function store(UserRequest $request)
    {
        /**
         * Menampilkan pesan error ketika validasi gagal
         * pengaturan validasi bisa dilihat pada class app/Http/request/User/CreateRequest
         */
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['email', 'name', 'password', 'photo', 'age', 'status', 'user_roles_id']);
        $user = $this->userHelper->create($payload);

        if (! $user['status']) {
            return response()->failed($user['error']);
        }

        $return = json_decode(json_encode(new UserResource((object) $user['data'])), 1);
        Redis::set('user:'.$user['data']['id'], json_encode($return));

        return response()->success(new UserResource($user['data']), 'User berhasil ditambahkan');
    }

    /**
     * Mengubah data user di tabel user
     */
    public function update(UserRequest $request, $id)
    {
        /**
         * Menampilkan pesan error ketika validasi gagal
         * pengaturan validasi bisa dilihat pada class app/Http/request/User/UpdateRequest
         */
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['email', 'name', 'password', 'photo', 'age', 'status', 'user_roles_id']);
        $user = $this->userHelper->update($payload, $id ?? 0);

        if (! $user['status']) {
            return response()->failed($user['error']);
        }

        $return = json_decode(json_encode(new UserResource((object) $user['data'])), 1);
        Redis::set('user:'.$user['data']['id'], json_encode($return));

        return response()->success(new UserResource($user['data']), 'User berhasil diubah');
    }
}
