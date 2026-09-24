<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ServiceRequestPhotograph extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'service_request_photographs';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'service_request_id',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        'file_url',
    ];

    /**
     * Service request this photograph belongs to.
     */
    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class, 'service_request_id');
    }

    /**
     * Get the full public URL of the photograph.
     */
    protected function fileUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->file_path ? Storage::disk('public')->url($this->file_path) : null,
        );
    }
}
