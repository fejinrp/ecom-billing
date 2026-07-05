<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomepageBanner extends Model
{
    use HasFactory;

    protected $table = 'homepage_banners';

    protected $fillable = [
        'image_path',
        'title',
        'subtitle',
        'badge_text',
        'link_url',
        'sort_order',
        'is_active',
    ];
}
