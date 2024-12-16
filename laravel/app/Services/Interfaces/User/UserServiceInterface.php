<?php

namespace App\Services\Interfaces\User;

interface UserServiceInterface
{
    public function paginate($userCatalogueId = null);

    public function create();

    public function update($id);

    public function destroy($id);

    public function updateProfile();
}
