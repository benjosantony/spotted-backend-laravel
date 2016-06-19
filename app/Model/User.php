<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use DB;
class User extends Model
{
    protected $table = 'sp_user';
    public $timestamps = false;
}
