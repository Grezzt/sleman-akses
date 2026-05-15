<?php

namespace App\Services;

use App\Repositories\Contracts\MapLocationRepositoryInterface;
use Illuminate\Support\Collection;
use Throwable;
use App\Models\MapLocation;

class MapLocationService
{
    public function __construct(private readonly MapLocationRepositoryInterface $mapLocations) {}

    public function getPublished(): Collection
    {
        try {
            return $this->mapLocations->getAllPublished();
        } catch (Throwable $e) {
            if ($e instanceof \RuntimeException) {
                throw $e;
            }

            throw new \RuntimeException('Failed to fetch map locations.', 0, $e);
        }
    }

    public function getPublishedWithDetails(): Collection
    {
        try {
            return $this->mapLocations->getAllPublished();
        } catch (Throwable $e) {
            if ($e instanceof \RuntimeException) {
                throw $e;
            }

            throw new \RuntimeException('Failed to fetch map location details.', 0, $e);
        }
    }

    public function delete(int $locationId): void
    {
        try {
            $deleted = $this->mapLocations->delete($locationId);

            if (!$deleted) {
                throw new \RuntimeException('Map location not found.');
            }
        } catch (Throwable $e) {
            if ($e instanceof \RuntimeException) {
                throw $e;
            }

            throw new \RuntimeException('Failed to delete map location.', 0, $e);
        }
    }

    public function getDetail(int $id): MapLocation
    {
        try {
            $location = $this->mapLocations->findWithDetails($id);

            if (!$location) {
                throw new \RuntimeException('Map location not found.');
            }

            return $location;
        } catch (Throwable $e) {
            if ($e instanceof \RuntimeException) {
                throw $e;
            }

            throw new \RuntimeException('Failed to fetch map location detail.', 0, $e);
        }
    }
}
