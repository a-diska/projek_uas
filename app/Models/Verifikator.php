<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verifikator extends Model
{
    use HasFactory;

    protected $table = 'verifikator';
    protected $primaryKey = 'id_verifikator';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = ['id_user', 'tahapan', 'jabatan', 'status'];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function logApproval()
    {
        return $this->hasMany(LogApproval::class, 'id_verifikator', 'id_verifikator');
    }
}
