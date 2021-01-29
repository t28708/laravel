<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    	protected $fillable = [
    	'title',
    	'body',
    	'opisanie',
        'alt',
    	'tag_id',
    	'place_id',
    	'imgTitle',
    	'meta_title',
    	'meta_keyword',
    	'meta_description',
    ];

    public function user()
	{
		return $this->belongsTo('App\User');
	}

}
