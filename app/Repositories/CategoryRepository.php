<?php

namespace App\Repositories;

use App\Models\FacilityCategory;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Support\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
  public function all(): Collection
  {
    return FacilityCategory::orderBy('facility_name')->get();
  }

  public function find(int $id): ?FacilityCategory
  {
    return FacilityCategory::find($id);
  }

  public function create(array $data): FacilityCategory
  {
    return FacilityCategory::create($data);
  }

  public function update(FacilityCategory $category, array $data): FacilityCategory
  {
    $category->fill($data);
    $category->save();

    return $category;
  }

  public function delete(int $id): bool
  {
    return (bool) FacilityCategory::whereKey($id)->delete();
  }
}
