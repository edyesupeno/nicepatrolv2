<?php

namespace App\Http\Controllers\Api\Schemas;

/**
 * @OA\Schema(
 *     schema="PenerimaanBarang",
 *     type="object",
 *     title="Penerimaan Barang",
 *     description="Model penerimaan barang",
 *     @OA\Property(property="id", type="integer", example=1, description="ID penerimaan barang"),
 *     @OA\Property(property="hash_id", type="string", example="abc123def456", description="Hash ID untuk URL"),
 *     @OA\Property(property="perusahaan_id", type="integer", example=1, description="ID perusahaan"),
 *     @OA\Property(property="project_id", type="integer", nullable=true, example=1, description="ID project"),
 *     @OA\Property(property="area_id", type="integer", nullable=true, example=1, description="ID area penyimpanan"),
 *     @OA\Property(property="pos", type="string", nullable=true, example="A1-B2-C3", description="Point of Storage"),
 *     @OA\Property(property="nomor_penerimaan", type="string", example="PB202601200001", description="Nomor penerimaan otomatis"),
 *     @OA\Property(property="nama_barang", type="string", example="Laptop Dell Inspiron", description="Nama barang"),
 *     @OA\Property(property="kategori_barang", type="string", enum={"Dokumen", "Material", "Elektronik", "Logistik"}, example="Elektronik", description="Kategori barang"),
 *     @OA\Property(property="jumlah_barang", type="integer", example=2, description="Jumlah barang"),
 *     @OA\Property(property="satuan", type="string", example="unit", description="Satuan barang"),
 *     @OA\Property(property="kondisi_barang", type="string", enum={"Baik", "Rusak", "Segel Terbuka"}, example="Baik", description="Kondisi barang"),
 *     @OA\Property(property="pengirim", type="string", example="PT. Supplier ABC", description="Nama pengirim"),
 *     @OA\Property(property="tujuan_departemen", type="string", example="IT Department", description="Departemen tujuan"),
 *     @OA\Property(property="foto_barang", type="string", nullable=true, example="penerimaan-barang/foto/1768619162_696afc9a0f8d6.jpg", description="Path foto barang"),
 *     @OA\Property(property="foto_url", type="string", nullable=true, example="https://devapi.nicepatrol.id/storage/penerimaan-barang/foto/1768619162_696afc9a0f8d6.jpg", description="URL foto barang"),
 *     @OA\Property(property="tanggal_terima", type="string", format="datetime", example="2026-01-20T10:30:00.000000Z", description="Tanggal penerimaan"),
 *     @OA\Property(property="formatted_tanggal_terima", type="string", example="20/01/2026 10:30", description="Tanggal terima terformat"),
 *     @OA\Property(property="status", type="string", example="Diterima", description="Status penerimaan"),
 *     @OA\Property(property="petugas_penerima", type="string", example="John Doe", description="Nama petugas penerima"),
 *     @OA\Property(property="keterangan", type="string", nullable=true, example="Barang dalam kondisi baik", description="Keterangan tambahan"),
 *     @OA\Property(property="created_at", type="string", format="datetime", example="2026-01-20T10:30:00.000000Z", description="Tanggal dibuat"),
 *     @OA\Property(property="updated_at", type="string", format="datetime", example="2026-01-20T10:30:00.000000Z", description="Tanggal diupdate"),
 *     @OA\Property(
 *         property="project",
 *         type="object",
 *         nullable=true,
 *         description="Data project",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="nama", type="string", example="Kantor Jakarta")
 *     ),
 *     @OA\Property(
 *         property="area",
 *         type="object",
 *         nullable=true,
 *         description="Data area penyimpanan",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="nama", type="string", example="Gudang A"),
 *         @OA\Property(property="alamat", type="string", nullable=true, example="Lantai 1")
 *     )
 * )
 */
class PenerimaanBarangSchema
{
    // This class is only for OpenAPI documentation
}