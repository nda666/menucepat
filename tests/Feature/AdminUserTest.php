<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function user_table_test()
    {
        $user = Admin::factory(1)->create()->first();
        $response = $this->actingAs($user, 'admin')
            ->get('/user/table');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ],
            ],
            'recordsTotal',
            'recordsFiltered',
        ]);
    }

    /** @test */
    function user_create_test()
    {
        $user = Admin::factory(1)->create()->first();

        $response = $this->actingAs($user, 'admin')
            ->post('/user', [
                'nama' => 'Adha Bakhtiar',
                'email' => 'adhabakhtiar@gmail.com',
                'tgl_lahir' => '1992-11-06',
                'kota_lahir' => 'Jember',
                'divisi' => 'Test Divisi',
                'subdivisi' => 'Test SubDivisi',
                'company' => 'Test Company',
                'department' => 'Test Department',
                'jabatan' => 'Test Jabatan',
                'lokasi' => 'Test Lokasi',
                'bagian' => 'Test Bagian',
                'sex' => 0,
                'blood' => 1,
                'alamat' => 'Some Addreess',
                'nik' => 'Random Nik',
                'password' => 'password',
                'whatsapp' => 'Test Whatsapp',
            ]);

        // should return 201
        $response->assertStatus(201);
        // Should return response like this
        $response->assertJson([
            'success' => true,
            'data' => [
                'nama' => 'Adha Bakhtiar',
                'email' => 'adhabakhtiar@gmail.com',
                'tgl_lahir' => '1992-11-06',
                'kota_lahir' => 'Jember',
                'divisi' => 'Test Divisi',
                'subdivisi' => 'Test SubDivisi',
                'company' => 'Test Company',
                'department' => 'Test Department',
                'jabatan' => 'Test Jabatan',
                'lokasi' => 'Test Lokasi',
                'bagian' => 'Test Bagian',
                'sex' => 0,
                'blood' => 1,
                'alamat' => 'Some Addreess',
                'nik' => 'Random Nik',
                'whatsapp' => 'Test Whatsapp',
            ],
        ]);
    }

    /** @test */
    function show_user_test()
    {
        $admin = Admin::factory(1)->create()->first();
        $user = User::factory(2)->create()->first();

        $response = $this->actingAs($admin, 'admin')
            ->get("user/notfoundtest");
        $response->assertNotFound();

        $response = $this->actingAs($admin, 'admin')
            ->get("user/{$user->id}");

        $response->assertOk();
        $response->assertJsonStructure([

            'email',
            'tgl_lahir',
            'kota_lahir',
            'divisi',
            'subdivisi',
            'company',
            'department',
            'jabatan',
            'lokasi',
            'bagian',
            'sex',
            'blood',
            'alamat',
            'nik',
            'whatsapp',
        ]);
    }
    /** @test */
    function user_update_test()
    {
        $admin = Admin::factory(1)->create()->first();
        $user = User::factory(2)->create()->first();
        $response = $this->actingAs($admin, 'admin')
            ->post("/user/{$user->id}", [
                '_method' => 'PUT',
                'nama' => 'Adha Bakhtiar Edit',
                'email' => 'adhabakhtiar@gmail.com',
                'tgl_lahir' => '1992-11-06',
                'kota_lahir' => 'Jember',
                'divisi' => 'Test Divisi',
                'subdivisi' => 'Test SubDivisi',
                'company' => 'Test Company',
                'department' => 'Test Department',
                'jabatan' => 'Test Jabatan',
                'lokasi' => 'Test Lokasi',
                'bagian' => 'Test Bagian',
                'sex' => 0,
                'blood' => 1,
                'alamat' => 'Some Addreess',
                'nik' => 'Random Nik',
                'password' => 'password',
                'whatsapp' => 'Test Whatsapp',
            ]);

        // should return 200
        $response->assertStatus(200);
        // Should return response like this
        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'nama' => 'Adha Bakhtiar Edit',
                'email' => 'adhabakhtiar@gmail.com',
                'tgl_lahir' => '1992-11-06',
                'kota_lahir' => 'Jember',
                'divisi' => 'Test Divisi',
                'subdivisi' => 'Test SubDivisi',
                'company' => 'Test Company',
                'department' => 'Test Department',
                'jabatan' => 'Test Jabatan',
                'lokasi' => 'Test Lokasi',
                'bagian' => 'Test Bagian',
                'sex' => 0,
                'blood' => 1,
                'alamat' => 'Some Addreess',
                'nik' => 'Random Nik',
                'whatsapp' => 'Test Whatsapp',
            ],
        ]);
    }

    /** @test */
    public function user_delete_test()
    {
        $admin = Admin::factory(1)->create()->first();
        $user = User::factory(2)->create()->first();

        // Start not found test
        $response = $this->actingAs($admin, 'admin')
            ->post("/user/notfoundtest", [
                '_method' => 'DELETE',
            ]);

        // should return 404
        $response->assertNotFound();

        // Start delete test
        $response = $this->actingAs($admin, 'admin')
            ->post("/user/{$user->id}", [
                '_method' => 'DELETE',
            ]);

        // should return 200
        $response->assertOk();
        // Should return response like this
        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'nama' => $user->nama,
                'email' => $user->email,
                'tgl_lahir' => $user->tgl_lahir,
                'kota_lahir' => $user->kota_lahir,
                'divisi' => $user->divisi,
                'subdivisi' => $user->subdivisi,
                'company' => $user->company,
                'department' => $user->department,
                'jabatan' => $user->jabatan,
                'lokasi' => $user->lokasi,
                'bagian' => $user->bagian,
            ]
        ]);
    }

    /** @test */
    function user_unlock_test()
    {
        $admin = Admin::factory(1)->create()->first();
        $user = User::factory(2)->create()->first();

        // Start not found test
        $response = $this->actingAs($admin, 'admin')
            ->post("/user/unlock/notfoundtest");

        // should return 404
        $response->assertNotFound();

        // Start unlock test
        $response = $this->actingAs($admin, 'admin')
            ->post("/user/unlock/{$user->id}");

        // should return 200
        $response->assertOk();
        // Should return response like this
        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'nama' => $user->nama,
                'email' => $user->email,
                'tgl_lahir' => $user->tgl_lahir,
                'kota_lahir' => $user->kota_lahir,
                'divisi' => $user->divisi,
                'subdivisi' => $user->subdivisi,
                'company' => $user->company,
                'department' => $user->department,
                'jabatan' => $user->jabatan,
                'lokasi' => $user->lokasi,
                'bagian' => $user->bagian,
                'lock' => 0,
            ]
        ]);
    }
}
