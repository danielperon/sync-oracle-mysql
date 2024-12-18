<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OracleEntry extends Model
{

    protected $connection = 'oracle';

    protected $table = 'PORTAL.ENTRY';

    protected $primaryKey = 'entry_id';


    public $timestamps = false;

    protected $fillable = [
        'entry_id',
        'entry_created',
        'entry_modified',
    ];
}
