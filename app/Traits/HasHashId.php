<?php

namespace App\Traits;

use Vinkla\Hashids\Facades\Hashids;

trait HasHashId
{
    protected static function bootHasHashId()
    {
        // Auto append hash_id to array
    }

    public function getHashIdAttribute(): string
    {
        return Hashids::encode($this->id);
    }

    public function getRouteKeyName(): string
    {
        return 'hash_id';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $decoded = Hashids::decode($value);
        
        if (empty($decoded)) {
            abort(404);
        }

        $id = $decoded[0];
        
        return $this->where('id', $id)->firstOrFail();
    }

    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
