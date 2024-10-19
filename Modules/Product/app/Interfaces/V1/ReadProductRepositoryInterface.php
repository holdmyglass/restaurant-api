<?php

namespace Modules\Product\Interfaces\V1;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Models\Product;

interface ReadProductRepositoryInterface
{
    public function getAllProducts(): Collection;

    public function getProductById(string $id): Product;

    public function getProductsByVersionIdentifier(string $versionIdentifier): Collection;
}
