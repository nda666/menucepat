<?php

namespace Tests\Feature;

use App\Models\Admin;
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

        // should return 200 OK
        $response->assertOk();
        // Should return response like this
        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => 1,
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
}
