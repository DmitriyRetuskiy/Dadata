<?php

namespace App\Models;
use Illuminate\Support\Facades\DB;

class PersonDB
{
    public static function getPerson()
    {
        try {
            $res =  db::table('')->select('*');
        } catch (\Exception $e){
            var_dump($e);
        }
       $res =  db::table('')->select('*');

       return $res;
    }
}
