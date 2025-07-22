<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class LogApproval extends Model
{
    use HasFactory;

    protected $table = 'log_approval';
    protected $primaryKey = 'id_log_approval';

    protected $fillable = ['id_pengajuan', 'id_verifikator', 'status', 'catatan'];

    public $timestamps = true;

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class, 'id_pengajuan', 'id_pengajuan');
    }

    // public function verifikator()
    // {
    //     return $this->belongsTo(Verifikator::class, 'id_verifikator', 'id_verifikator');
    // }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'id_verifikator', 'id'); // asumsinya ke table users
    }
}
