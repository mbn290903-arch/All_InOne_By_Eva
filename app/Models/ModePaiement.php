<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
    
class ModePaiement extends Model
{
    protected $table = 'modes_paiement';

    protected $fillable = ['libelle', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}