<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class FeedbackRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'message',
        'manager_notes',
        'source_page',
        'status',
        'manager_id',
        'in_progress_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'in_progress_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (FeedbackRequest $model): void {
            if (! $model->isDirty('status')) {
                return;
            }

            $user = auth()->user();
            $old = (string) $model->getOriginal('status');
            $new = (string) $model->status;

            if ($old === 'done' && $new !== 'done' && ! $user?->hasRole('admin')) {
                throw ValidationException::withMessages([
                    'status' => 'Только администратор может открыть закрытую заявку.',
                ]);
            }

            if ($new === 'new') {
                $model->in_progress_at = null;
                $model->completed_at = null;
            } elseif ($new === 'in_progress') {
                $model->completed_at = null;
                if ($old !== 'in_progress') {
                    $model->in_progress_at = now();
                }
            } elseif ($new === 'done') {
                $model->completed_at = now();
            }
        });
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
}
