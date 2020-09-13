<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected   $table  =   'person';
    protected   $primaryKey =   'id';

    public function list(array $params)
    {
        $builder = $this->select('*')->get();
        return $builder;
    }
}
