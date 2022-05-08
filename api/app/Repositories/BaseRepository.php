<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class BaseRepository
{
    public function beginTransaction()
    {
        DB::beginTransaction();
    }

    public function commit()
    {
        DB::commit();
    }

    public function rollBack()
    {
        DB::rollBack();
    }
}
