<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Perusahaan;
use App\Models\Project;
use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\PenerimaanBarang;
use Laravel\Sanctum\Sanctum;

class MultiTenancyTest extends TestCase
{
    use RefreshDatabase;

    protected $perusahaanA;
    protected $perusahaanB;
    protected $projectA;
    protected $projectB;
    protected $userA;
    protected $userB;
    protected $karyawanA;
    protected $karyawanB;

    protected function setUp(): void
    {
        parent::setUp();

        // Create perusahaan
        $this->perusahaanA = Perusahaan::factory()->create(['nama_perusahaan' => 'Perusahaan A']);
        $this->perusahaanB = Perusahaan::factory()->create(['nama_perusahaan' => 'Perusahaan B']);

        // Create projects
        $this->projectA = Project::factory()->create([
            'perusahaan_id' => $this->perusahaanA->id,
            'nama' => 'Project A'
        ]);
        $this->projectB = Project::factory()->create([
            'perusahaan_id' => $this->perusahaanB->id,
            'nama' => 'Project B'
        ]);

        // Create jabatan
        $jabatanA = Jabatan::factory()->create(['perusahaan_id' => $this->perusahaanA->id]);
        $jabatanB = Jabatan::factory()->create(['perusahaan_id' => $this->perusahaanB->id]);

        // Create users
        $this->userA = User::factory()->create([
            'perusahaan_id' => $this->perusahaanA->id,
            'role' => 'security_officer'
        ]);
        $this->userB = User::factory()->create([
            'perusahaan_id' => $this->perusahaanB->id,
            'role' => 'security_officer'
        ]);

        // Create karyawan
        $this->karyawanA = Karyawan::factory()->create([
            'perusahaan_id' => $this->perusahaanA->id,
            'project_id' => $this->projectA->id,
            'user_id' => $this->userA->id,
            'jabatan_id' => $jabatanA->id
        ]);
        $this->karyawanB = Karyawan::factory()->create([
            'perusahaan_id' => $this->perusahaanB->id,
            'project_id' => $this->projectB->id,
            'user_id' => $this->userB->id,
            'jabatan_id' => $jabatanB->id
        ]);
    }

    /** @test */
    public function user_can_only_see_their_own_projects()
    {
        Sanctum::actingAs($this->userA);

        $response = $this->getJson('/api/v1/penerimaan-barang-projects');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['nama' => 'Project A']);
        $response->assertJsonMissing(['nama' => 'Project B']);
    }

    /** @test */
    public function user_cannot_access_other_perusahaan_projects()
    {
        Sanctum::actingAs($this->userA);

        // Try to get areas from Project B (different perusahaan)
        $response = $this->getJson("/api/v1/penerimaan-barang-areas/{$this->projectB->id}");

        $response->assertStatus(403);
        $response->assertJsonFragment(['message' => 'Anda tidak memiliki akses ke project tersebut']);
    }

    /** @test */
    public function user_can_only_see_their_own_penerimaan_barang()
    {
        // Create penerimaan barang for both perusahaan
        $penerimaanA = PenerimaanBarang::factory()->create([
            'perusahaan_id' => $this->perusahaanA->id,
            'project_id' => $this->projectA->id,
            'nama_barang' => 'Barang A'
        ]);
        $penerimaanB = PenerimaanBarang::factory()->create([
            'perusahaan_id' => $this->perusahaanB->id,
            'project_id' => $this->projectB->id,
            'nama_barang' => 'Barang B'
        ]);

        Sanctum::actingAs($this->userA);

        $response = $this->getJson('/api/v1/penerimaan-barang');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data.data');
        $response->assertJsonFragment(['nama_barang' => 'Barang A']);
        $response->assertJsonMissing(['nama_barang' => 'Barang B']);
    }

    /** @test */
    public function user_cannot_create_penerimaan_barang_for_other_project()
    {
        Sanctum::actingAs($this->userA);

        $data = [
            'project_id' => $this->projectB->id, // Try to use Project B
            'nama_barang' => 'Test Barang',
            'kategori_barang' => 'Elektronik',
            'jumlah_barang' => 1,
            'satuan' => 'unit',
            'kondisi_barang' => 'Baik',
            'pengirim' => 'Test Pengirim',
            'tujuan_departemen' => 'IT',
            'tanggal_terima' => '2026-01-20'
        ];

        $response = $this->postJson('/api/v1/penerimaan-barang', $data);

        // Should fail validation because project doesn't exist in user's scope
        $response->assertStatus(422);
    }

    /** @test */
    public function user_auto_assigned_to_their_project()
    {
        Sanctum::actingAs($this->userA);

        $data = [
            // Don't specify project_id - should auto-assign
            'nama_barang' => 'Test Barang',
            'kategori_barang' => 'Elektronik',
            'jumlah_barang' => 1,
            'satuan' => 'unit',
            'kondisi_barang' => 'Baik',
            'pengirim' => 'Test Pengirim',
            'tujuan_departemen' => 'IT',
            'tanggal_terima' => '2026-01-20'
        ];

        $response = $this->postJson('/api/v1/penerimaan-barang', $data);

        $response->assertStatus(201);
        $response->assertJsonFragment(['project_id' => $this->projectA->id]);
    }

    /** @test */
    public function superadmin_can_see_all_projects()
    {
        $superadmin = User::factory()->create([
            'perusahaan_id' => $this->perusahaanA->id,
            'role' => 'superadmin'
        ]);

        Sanctum::actingAs($superadmin);

        $response = $this->getJson('/api/v1/penerimaan-barang-projects');

        $response->assertStatus(200);
        // Superadmin should see all projects from their perusahaan
        $response->assertJsonCount(1, 'data'); // Only Project A (same perusahaan)
        $response->assertJsonFragment(['nama' => 'Project A']);
    }

    /** @test */
    public function login_returns_project_info()
    {
        $response = $this->postJson('/api/v1/login', [
            'email' => $this->userA->email,
            'password' => 'password'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'user' => [
                    'project_id',
                    'project' => ['id', 'nama']
                ]
            ]
        ]);
        $response->assertJsonFragment(['project_id' => $this->projectA->id]);
    }
}