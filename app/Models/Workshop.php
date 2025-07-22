<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class Workshop extends Model
{
    use HasFactory;

    protected $table = 'workshop';
    protected $primaryKey = 'id_workshop';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = ['nama_workshop', 'tanggal_mulai', 'tanggal_selesai', 'lokasi'];

    public $timestamps = true;


    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function pengajuan()
    {
        return $this->hasMany(Pengajuan::class, 'id_workshop', 'id_workshop');
    }
}
