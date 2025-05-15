<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Education extends Model
{
    //
    use HasFactory;
    protected $table = 'educations';
    protected $primaryKey = 'id';
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $fillable = ['user_id','tingkat_pendidikan', 'institusi', 'jurusan', 'tahun_masuk', 'tahun_lulus', 'ijazah'];
    // public function up(){
    //     Schema::create('educations', function (Blueprint $table) {
    //         $table->id();
    //         $table->foreignId('user_id')->constrained();
    //         $table->string('tingkat_pendidikan');
    //         $table->string('institusi');
    //         $table->string('jurusan');
    //         $table->date('tahun_masuk');
    //         $table->date('tahun_lulus');
    //         $table->timestamps();
    //     });
    // }  
}
