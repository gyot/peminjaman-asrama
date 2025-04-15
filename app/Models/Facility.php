<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $fillable = ['user_id','name', 'capacity', 'unit', 'price', 'image', 'description'];
    public function up(){
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('name');
            $table->integer('capacity');
            $table->string('unit');
            $table->decimal('price', 10, 2);
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }  
    
    public function applications()
    {
        return $this->belongsToMany(Application::class, 'application_facility');
    }

}
