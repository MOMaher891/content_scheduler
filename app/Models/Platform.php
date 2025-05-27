<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    use HasFactory;
    protected $table = "platforms";
    protected $fillable = [
        "name",
        "type_id",
    ];
    public function platformType()
    {
        return $this->belongsTo(PlatformType::class, 'type_id', 'id');
    }
    public function userPlatforms()
    {
        return $this->hasMany(UserPlatform::class, 'platform_id', 'id');
    }
}
