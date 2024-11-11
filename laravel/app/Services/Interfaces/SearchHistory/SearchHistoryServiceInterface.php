<?php

namespace App\Services\Interfaces\SearchHistory;

interface SearchHistoryServiceInterface
{
    public function paginate();

    public function create();

    public function update($id);

    public function destroy($id);
}
