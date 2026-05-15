<?php

namespace App\Services;

use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Support\Collection;
use Throwable;

class CategoryService
{
    public function __construct(private readonly CategoryRepositoryInterface $categories) {}

    public function getAll(): Collection
    {
        try {
            return $this->categories->all();
        } catch (Throwable $e) {
            if ($e instanceof \RuntimeException) {
                throw $e;
            }

            throw new \RuntimeException('Failed to fetch categories.', 0, $e);
        }
    }

    public function create(array $data)
    {
        try {
            return $this->categories->create($data);
        } catch (Throwable $e) {
            if ($e instanceof \RuntimeException) {
                throw $e;
            }

            throw new \RuntimeException('Failed to create category.', 0, $e);
        }
    }

    public function update(int $id, array $data)
    {
        try {
            $category = $this->categories->find($id);

            if (!$category) {
                throw new \RuntimeException('Category not found.');
            }

            return $this->categories->update($category, $data);
        } catch (Throwable $e) {
            if ($e instanceof \RuntimeException) {
                throw $e;
            }

            throw new \RuntimeException('Failed to update category.', 0, $e);
        }
    }

    public function delete(int $id): void
    {
        try {
            $deleted = $this->categories->delete($id);

            if (!$deleted) {
                throw new \RuntimeException('Category not found.');
            }
        } catch (Throwable $e) {
            if ($e instanceof \RuntimeException) {
                throw $e;
            }

            throw new \RuntimeException('Failed to delete category.', 0, $e);
        }
    }
}
