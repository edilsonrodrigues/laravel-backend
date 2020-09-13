<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected   $table  =   'user';
    protected   $primaryKey =   'id';

    public function list(array $params)
    {
        $builder = $this->select('*')->get();
        return $builder;
    }

    public function login(array $params)
    {
        $builder = $this->select('*')->get();
        return $builder;
    }
}
