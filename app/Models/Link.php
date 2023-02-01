<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class Link extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'long_link',
        'short_link',
        'is_active'
    ];

    public function userLinks()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function userLink()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
