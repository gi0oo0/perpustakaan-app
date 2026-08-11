<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Loan extends Model
{
    use HasFactory;

    const TARIF_DENDA_PER_HARI = 500;
    const MAX_DENDA = 5000;
    const MAX_ACTIVE_LOANS = 3;

    protected $fillable = [
        'user_id',
        'book_id',
        'loan_date',
        'due_date',
        'duration_days',
        'denda_per_day',
        'returned_at',
        'denda',
        'status_denda',
        'processed_by',
    ];

    protected $casts = [
        'loan_date' => 'date',
        'due_date' => 'date',
        'returned_at' => 'date',
        'duration_days' => 'integer',
        'denda_per_day' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function isReturned(): bool
    {
        return $this->returned_at !== null;
    }

    public function isOverdue(): bool
    {
        return !$this->isReturned() && $this->due_date->isPast();
    }

    public function getDaysLate(): int
    {
        if ($this->isReturned()) {
            return max(0, $this->returned_at->diffInDays($this->due_date, false) * -1);
        }
        return max(0, Carbon::today()->diffInDays($this->due_date, false) * -1);
    }

    public function getDendaPerDay(): int
    {
        return $this->denda_per_day ?: self::TARIF_DENDA_PER_HARI;
    }

    public static function calculateDenda(int $daysLate, ?int $rate = null): int
    {
        $rate = $rate ?: self::TARIF_DENDA_PER_HARI;
        return min($daysLate * $rate, self::MAX_DENDA);
    }

    public function getPotentialDenda(): int
    {
        return self::calculateDenda($this->getDaysLate(), $this->getDendaPerDay());
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->isReturned()) {
            if ($this->denda > 0) {
                return 'Dikembalikan (Telat)';
            }
            return 'Dikembalikan';
        }
        if ($this->isOverdue()) {
            return 'Terlambat';
        }
        return 'Dipinjam';
    }

    public function getStatusColorAttribute(): string
    {
        if ($this->isReturned()) {
            if ($this->denda > 0) return 'coral';
            return 'primary';
        }
        if ($this->isOverdue()) return 'coral';
        return 'lemon';
    }
}
