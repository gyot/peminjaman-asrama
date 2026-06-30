<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Host extends Model
{
    protected $fillable = ['hosts'];
    public function up(){
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('host');
            $table->string('phone')->nullable();
            $table->timestamps();
        });
    }  
}
