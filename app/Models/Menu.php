<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menus';

    protected $fillable = [
        'title',
        'icon',
        'route',
        'type',      // single | dropdown
        'key',       // unique key for dropdown toggle
        'parent_id',
        'order',
    ];

    /* ==========================
     |  Relationships
     |==========================*/

    // Parent menu
    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    // Child menus
    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')
                    ->orderBy('order');
    }

    /* ==========================
     |  Scopes
     |==========================*/

    // Only top-level menus
    public function scopeParents($query)
    {
        return $query->whereNull('parent_id')
                     ->orderBy('order');
    }

    // Only dropdown menus
    public function scopeDropdowns($query)
    {
        return $query->where('type', 'dropdown');
    }

    // Only single menus
    public function scopeSingles($query)
    {
        return $query->where('type', 'single');
    }
}
