<?php

namespace App\Services\Interfaces\ProhibitedWord;

interface ProhibitedWordServiceInterface
{
    public function paginate();

    public function create();

    public function update($id);

}
