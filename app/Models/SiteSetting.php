<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = ['group', 'key', 'value', 'type', 'label', 'hint', 'sort_order'];

    protected static function booted(): void
    {
        $flush = fn () => cache()->forget('site.settings');
        static::saved($flush);
        static::deleted($flush);
    }

    public function castValue(): mixed
    {
        return match ($this->type) {
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOL),
            'number' => is_numeric($this->value) ? $this->value + 0 : null,
            'json' => json_decode((string) $this->value, true),
            default => $this->value,
        };
    }
}
