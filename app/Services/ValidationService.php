<?php

namespace App\Services;

use App\Models\User;
use App\Models\Validation;
use App\Repositories\Contracts\MapLocationRepositoryInterface;
use App\Repositories\Contracts\ValidationRepositoryInterface;
use Throwable;

class ValidationService
{
    public function __construct(
        private readonly ValidationRepositoryInterface $validations,
        private readonly MapLocationRepositoryInterface $mapLocations
    ) {}

    public function validate(Validation $validation, array $data, User $admin): Validation
    {
        try {
            $status = $data['approval_status'] ?? $data['status'] ?? null;
            $note = $data['evaluation_note'] ?? $data['note'] ?? null;

            if (empty($status)) {
                throw new \RuntimeException('Approval status is required.');
            }

            $payload = [
                'user_id' => $admin->id,
                'approval_status' => $status,
                'evaluation_note' => $note,
                'validation_date' => now(),
            ];

            $validation = $this->validations->update($validation, $payload);

            if ($validation->approval_status === 'approved') {
                $validation->loadMissing('report');

                if (!$validation->report) {
                    throw new \RuntimeException('Validation report not found.');
                }

                $mapPayload = [
                    'place_name' => $data['place_name'] ?? null,
                    'address' => $data['address'] ?? null,
                    'status' => $data['status'] ?? null,
                    'facility_type' => $data['facility_type'] ?? null,
                ];

                $this->mapLocations->upsertFromReport($validation, $validation->report, $mapPayload);
            } else {
                $this->mapLocations->deleteByValidationId($validation->id);
            }

            return $validation;
        } catch (Throwable $e) {
            if ($e instanceof \RuntimeException) {
                throw $e;
            }

            throw new \RuntimeException('Failed to validate report.', 0, $e);
        }
    }
}
