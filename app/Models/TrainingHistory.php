<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingHistory extends Model
{
    use HasFactory;
    protected $table = 'training_histories';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id',
        'nama_pelatihan',
        'durasi',
        'materi',
        'penyelenggara',
        'tanggal_mulai',
        'tanggal_selesai',
        'lokasi',
        'sertifikat',
        'tingkat',
        'jenis_pelatihan',
        'catatan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
