<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MySqlEntry extends Model
{
    protected $connection = 'mysql';

    protected $table = 'entry';

    protected $primaryKey = 'entry_id';

    public $timestamps = false;

    protected $fillable = [
        'entry_id',
        'entry_created',
        'entry_modified',
    ];
}
