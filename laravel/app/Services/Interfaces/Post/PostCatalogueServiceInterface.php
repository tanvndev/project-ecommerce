<?php

namespace App\Services\Interfaces\Post;

/**
 * Interface PostCatalogueServiceInterface
 */
interface PostCatalogueServiceInterface
{
    public function paginate();

    public function create();

    public function update($id);

    public function destroy($id);

    public function list();
}
