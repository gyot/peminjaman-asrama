<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id', 'name', 'address', 'event_name', 'event_start_date', 'event_end_date', 'phone_number', 'status', 'notes'
    ];

    // Relasi dengan User
    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

    // Relasi dengan Facility (jika diperlukan)
    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('user_id')->constrained();
            $table->string('name');
            $table->text('address');
            $table->foreignId('facility_id')->constrained('facilities');
            $table->string('event_name');
            $table->date('event_start_date');
            $table->date('event_end_date');
            $table->string('phone_number');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
        
    }
}
