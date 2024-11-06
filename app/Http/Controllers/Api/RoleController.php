<?php

namespace App\Http\Controllers\Api;

use App\Helpers\User\RoleHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Http\Resources\RoleCollection;
use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    private $roleHelper;

    public function __construct()
    {
        $this->roleHelper = new RoleHelper;
    }

    /**
     * Delete data role
     *
     * @param  mixed  $id
     */
    public function destroy($id)
    {
        $role = $this->roleHelper->delete($id);

        if (! $role) {
            return response()->failed(['Mohon maaf role tidak ditemukan']);
        }

        return response()->success($role, 'Role berhasil dihapus');
    }

    /**
     * Mengambil data role dilengkapi dengan pagination
     */
    public function index(Request $request)
    {
        $filter = [
            'name' => $request->name ?? '',
        ];
        $roles = $this->roleHelper->getAll($filter, $request->page ?? 1, $request->limit ?? 25, $request->sort ?? '');

        // dd($roles);
        return response()->success(new RoleCollection($roles['data']));

    }

    /**
     * Menampilkan role secara spesifik dari tabel user_role
     *
     * @param  mixed  $id
     */
    public function show($id)
    {
        $role = $this->roleHelper->getById($id);

        if (! ($role['status'])) {
            return response()->failed(['Data role tidak ditemukan'], 404);
        }

        return response()->success(new RoleResource($role['data']));
    }

    /**
     * Membuat data role baru & disimpan ke tabel user_role
     */
    public function store(RoleRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['name', 'access']);
        $role = $this->roleHelper->create($payload);

        if (! $role['status']) {
            return response()->failed($role['error']);
        }

        return response()->success(new RoleResource($role['data']), 'Role berhasil ditambahkan');
    }

    /**
     * Mengubah data role di tabel user_role
     */
    public function update(RoleRequest $request, string $id)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['name', 'access']);
        $role = $this->roleHelper->update($payload, $id);

        if (! $role['status']) {
            return response()->failed($role['error']);
        }

        return response()->success(new RoleResource($role['data']), 'Role berhasil diubah');
    }
}
