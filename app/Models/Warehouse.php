<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Warehouse extends Model
{
    protected $fillable = [
        'country_id',
        'state_id',
        'type',
        'parent_id',
        'district_id',
        'taluka_id',
        'name',
        'code',
        'address',
        'contact_person',
        'email',
        'contact_number',
        'status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function parent()
    {
        return $this->belongsTo(Warehouse::class, 'parent_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'warehouse_id');
    }

    public function suppliers()
    {
        return $this->hasMany(Supplier::class, 'warehouse_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function taluka()
    {
        return $this->belongsTo(Talukas::class, 'taluka_id');
    }

    public function productBatches()
    {
        return $this->hasMany(ProductBatch::class);
    }
    
    public function categories()
    {
        return $this->hasMany(Category::class, 'warehouse_id');
    }
}
