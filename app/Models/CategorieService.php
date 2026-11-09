<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategorieService extends Model
{
    protected $table = 'categories_services';

    protected $fillable = [
        'nom',
        'ordre',
    ];

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}