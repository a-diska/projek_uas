<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class Pengajuan extends Model
{
    use HasFactory;

    protected $table = 'pengajuan';
    protected $primaryKey = 'id_pengajuan';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = ['id_user', 'id_pelayanan', 'id_workshop', 'status', 'catatan'];

    public $timestamps = true;

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function pelayanan()
    {
        return $this->belongsTo(Pelayanan::class, 'id_pelayanan');
    }

    public function workshop()
    {
        return $this->belongsTo(Workshop::class, 'id_workshop');
    }

    public function dokumen()
    {
        return $this->hasMany(Dokumen::class, 'id_pengajuan');
    }

    public function logApproval()
    {
        return $this->hasMany(LogApproval::class, 'id_pengajuan', 'id_pengajuan');
    }
}
