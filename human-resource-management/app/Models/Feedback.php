<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Feedback extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sender_id',
        'employee_id', // Make sure this is included
        'message',
        'type',
        'is_anonymous',
        'status',
        'sent_at'
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'is_anonymous' => 'boolean',
        ];
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Add the employee relationship
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(FeedbackResponse::class);
    }

    public function scopeSent(Builder $query): Builder
    {
        return $query->where('status', 'sent');
    }

    public function scopeForEmployee(Builder $query, int $employeeId): Builder
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeFromSender(Builder $query, int $userId): Builder
    {
        return $query->where('sender_id', $userId);
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now()
        ]);
    }

    public function canView(User $user): bool
    {
        return $user->id === $this->sender_id;
    }

    public function canDelete(User $user): bool
    {
        return $user->id === $this->sender_id && $this->status === 'draft';
    }
}