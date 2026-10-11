<?php

namespace App\Services;

use App\Models\Report;
use App\Repositories\Contracts\ReportRepositoryInterface;
use App\Repositories\Contracts\ValidationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Throwable;

class ReportService
{
    public function __construct(
        private readonly ReportRepositoryInterface $reports,
        private readonly ValidationRepositoryInterface $validations,
        private readonly PhotoUploadService $photoUploadService
    ) {}

    public function createReport(array $data, int $userId, string $photoUrl): Report
    {
        try {
            if (!empty($data['uuid'])) {
                $existing = $this->reports->findByUuid($data['uuid']);
                if ($existing) {
                    return $existing;
                }
            }

            $report = $this->reports->create([
                'uuid' => $data['uuid'] ?? null,
                'user_id' => $userId,
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'photo_url' => $photoUrl,
            ]);

            $categories = $data['categories'] ?? [];

            foreach ($categories as $item) {
                if (!is_array($item) || !array_key_exists('id', $item) || !array_key_exists('available', $item)) {
                    throw new \RuntimeException('Invalid category payload.');
                }

                $this->reports->addDetail($report, (int) $item['id'], (bool) $item['available']);
            }

            $this->validations->initPending($report);

            return $report;
        } catch (Throwable $e) {
            if ($e instanceof \RuntimeException) {
                throw $e;
            }

            throw new \RuntimeException('Failed to create report.', 0, $e);
        }
    }

    public function getUserReports(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        try {
            return $this->reports->getByUserPaginated($userId, $perPage);
        } catch (Throwable $e) {
            if ($e instanceof \RuntimeException) {
                throw $e;
            }

            throw new \RuntimeException('Failed to fetch reports.', 0, $e);
        }
    }

    public function getUserReportStats(int $userId): array
    {
        try {
            return $this->reports->getStatsByUser($userId);
        } catch (Throwable $e) {
            throw new \RuntimeException('Failed to fetch report stats.', 0, $e);
        }
    }

    public function getReportDetail(int $reportId, int $userId): Report
    {
        try {
            $report = $this->reports->findWithRelations($reportId);

            if (!$report) {
                throw new \RuntimeException('Report not found.');
            }

            if ((int) $report->user_id !== $userId) {
                throw new \RuntimeException('Unauthorized access to report.');
            }

            return $report;
        } catch (Throwable $e) {
            if ($e instanceof \RuntimeException) {
                throw $e;
            }

            throw new \RuntimeException('Failed to fetch report detail.', 0, $e);
        }
    }
}
