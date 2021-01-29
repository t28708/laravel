<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    	protected $fillable = [
    	'title',
    	'body',
    	'article_id',
    	'user_id',
    	'comment_parent'
    ];

    public function user()
	{
		return $this->belongsTo('App\User');
	}

	    public function article()
	{
		return $this->belongsTo('App\Article');
	}

	public function region()
	{
		return $this->belongsTo('App\Region');
	}

		public function place()
	{
		return $this->belongsTo('App\Place');
	}

}
