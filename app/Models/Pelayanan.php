<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class Pelayanan extends Model
{
    use HasFactory;

    protected $table = 'pelayanan';
    protected $primaryKey = 'id_pelayanan';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = ['nama_pelayanan', 'deskripsi', 'status'];

    public $timestamps = true;

    /**
     * Format tanggal saat serialisasi.
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function pengajuan()
    {
        return $this->hasMany(Pengajuan::class, 'id_pelayanan', 'id_pelayanan');
    }
}
