<?php namespace Jenssegers\AB\Models;

use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Experiment extends Eloquent {

    protected $primaryKey = 'name';

    public $timestamps = false;

    protected $fillable = ['name', 'visitors', 'engagement'];

    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        // Set the connection based on the config.
        $this->connection = Config::get('ab::connection');
    }

    public function goals()
    {
        return $this->hasMany('Jenssegers\AB\Models\Goal', 'experiment');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('name', Config::get('ab::experiments'));
    }

}
