<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Application extends Model
{
    use HasFactory;
    protected $table = 'applications';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name', 'address', 'event_name', 'event_start_date', 'event_end_date', 'phone_number', 'notes'
    ];

    // Relasi dengan User
    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

    

    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('user_id')->constrained();
            $table->string('name');
            $table->text('address');
            // $table->foreignId('facility_id')->constrained('facilities');
            $table->string('event_name');
            $table->date('event_start_date');
            $table->date('event_end_date');
            $table->string('phone_number');
            // $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
        
    }

    // Application.php
    public function approval()
    {
        return $this->hasOne(Approvals::class, 'id_applications');
    }    

    public function facilities()
    {
        return $this->belongsToMany(Facility::class, 'application_facility', 'application_id', 'facility_id');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('event_name', 'like', '%' . $search . '%');
        });
    }
    public function scopeFilterByDate($query, $date)
    {
        if ($date) {
            $query->whereDate('event_start_date', $date);
        }
    }
    public function scopeFilterByStatus($query, $status)
    {
        if ($status) {
            $query->where('status', $status);
        }
    }
    public function scopeFilterByFacility($query, $facilityId)
    {
        if ($facilityId) {
            $query->whereHas('facilities', function ($query) use ($facilityId) {
                $query->where('id', $facilityId);
            });
        }
    }
    public function scopeFilterByUser($query, $userId)
    {
        if ($userId) {
            $query->where('user_id', $userId);
        }
    }
    public function scopeFilterByDateRange($query, $startDate, $endDate)
    {
        if ($startDate && $endDate) {
            $query->whereBetween('event_start_date', [$startDate, $endDate]);
        }
    }
    public function scopeFilterByEventName($query, $eventName)
    {
        if ($eventName) {
            $query->where('event_name', 'like', '%' . $eventName . '%');
        }
    }
    public function scopeFilterByPhoneNumber($query, $phoneNumber)
    {
        if ($phoneNumber) {
            $query->where('phone_number', 'like', '%' . $phoneNumber . '%');
        }
    }
    public function scopeFilterByAddress($query, $address)
    {
        if ($address) {
            $query->where('address', 'like', '%' . $address . '%');
        }
    }
    public function scopeFilterByNotes($query, $notes)
    {
        if ($notes) {
            $query->where('notes', 'like', '%' . $notes . '%');
        }
    }
    public function scopeFilterByCreatedAt($query, $createdAt)
    {
        if ($createdAt) {
            $query->whereDate('created_at', $createdAt);
        }
    }
    public function scopeFilterByUpdatedAt($query, $updatedAt)
    {
        if ($updatedAt) {
            $query->whereDate('updated_at', $updatedAt);
        }
    }
    public function scopeFilterByStatusAndDate($query, $status, $date)
    {
        if ($status && $date) {
            $query->where('status', $status)
                ->whereDate('event_start_date', $date);
        }
    }
    public function scopeFilterByStatusAndFacility($query, $status, $facilityId)
    {
        if ($status && $facilityId) {
            $query->where('status', $status)
                ->whereHas('facilities', function ($query) use ($facilityId) {
                    $query->where('id', $facilityId);
                });
        }
    }
    public function scopeFilterByStatusAndUser($query, $status, $userId)
    {
        if ($status && $userId) {
            $query->where('status', $status)
                ->where('user_id', $userId);
        }
    }
    public function scopeFilterByStatusAndDateRange($query, $status, $startDate, $endDate)
    {
        if ($status && $startDate && $endDate) {
            $query->where('status', $status)
                ->whereBetween('event_start_date', [$startDate, $endDate]);
        }
    }                   

}
