<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approvals extends Model
{
    //
    use HasFactory;

    protected $fillable = [
         'id_applications', 'id_user', 'status', 'notes'
    ];

    // Relasi dengan User
    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

    // Relasi dengan Facility (jika diperlukan)
    public function applications()
    {
        return $this->belongsTo(Application::class);
    }

    public function up()
    {
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();           
            $table->foreignId('id_applications')->constrained('applications');
            $table->foreignId('id_user')->constrained('users');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }    
}
