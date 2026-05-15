<?php

namespace App\Repositories\Contracts;

use App\Models\FacilityCategory;
use Illuminate\Support\Collection;

interface CategoryRepositoryInterface
{
  public function all(): Collection;

  public function find(int $id): ?FacilityCategory;

  public function create(array $data): FacilityCategory;

  public function update(FacilityCategory $category, array $data): FacilityCategory;

  public function delete(int $id): bool;
}
