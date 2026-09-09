<?php

namespace App\Services;

use App\Models\Vehicle;

class VehicleHealthService
{
    /**
     * @return array{score: int, status: string, label: string, reasons: array<int, array{severity: string, title: string, detail: string}>}
     */
    public function assess(Vehicle $vehicle): array
    {
        $vehicle->loadMissing(['maintenanceRecords', 'motTests.defects']);

        $score = 100;
        $reasons = [];

        if ($vehicle->mot_due_at?->isPast() || $vehicle->mot_status === 'expired') {
            $score -= 40;
            $reasons[] = $this->reason('critical', 'MOT has expired', 'The vehicle should not be driven until it has a valid MOT.');
        } elseif ($vehicle->mot_due_at?->isBefore(today()->addDays(30))) {
            $score -= 12;
            $reasons[] = $this->reason('soon', 'MOT due soon', 'Book the MOT before '.$vehicle->mot_due_at->format('j M Y').'.');
        }

        if ($vehicle->tax_due_at?->isPast() || $vehicle->tax_status === 'untaxed') {
            $score -= 35;
            $reasons[] = $this->reason('critical', 'Vehicle tax needs attention', 'Check the vehicle tax status before driving.');
        } elseif ($vehicle->tax_due_at?->isBefore(today()->addDays(30))) {
            $score -= 8;
            $reasons[] = $this->reason('soon', 'Tax renewal approaching', 'Renew or confirm a direct debit before the due date.');
        }

        if ($vehicle->insurance_due_at?->isPast()) {
            $score -= 30;
            $reasons[] = $this->reason('critical', 'Insurance date has passed', 'Confirm continuous insurance cover before driving.');
        }

        $overdueRecords = $vehicle->maintenanceRecords
            ->where('status', 'planned')
            ->filter(fn ($record): bool => $record->due_at?->isPast() ?? false);

        if ($overdueRecords->isNotEmpty()) {
            $score -= min(30, $overdueRecords->count() * 10);
            $reasons[] = $this->reason('attention', $overdueRecords->count().' overdue maintenance '.str('item')->plural($overdueRecords->count()), 'Review overdue work and record anything already completed.');
        }

        $unresolvedDefects = $vehicle->motTests
            ->flatMap->defects
            ->whereNull('resolved_at');

        $dangerousDefects = $unresolvedDefects->where('dangerous', true)->count();
        $majorDefects = $unresolvedDefects->where('type', 'major')->count();
        $advisories = $unresolvedDefects->where('type', 'advisory')->count();

        if ($dangerousDefects > 0) {
            $score -= 35;
            $reasons[] = $this->reason('critical', 'Dangerous MOT defect recorded', 'Have the defect inspected and resolved before driving.');
        } elseif ($majorDefects > 0) {
            $score -= min(30, $majorDefects * 15);
            $reasons[] = $this->reason('critical', 'Unresolved major MOT defect', 'A qualified garage should confirm the repair.');
        } elseif ($advisories > 0) {
            $score -= min(15, $advisories * 5);
            $reasons[] = $this->reason('attention', $advisories.' MOT '.str('advisory')->plural($advisories).' to monitor', 'Plan the work before the items become failures.');
        }

        $score = max(0, min(100, $score));
        [$status, $label] = match (true) {
            $score < 50 => ['action', 'Action required'],
            $score < 80 => ['attention', 'Needs attention'],
            default => ['healthy', 'In good shape'],
        };

        if ($reasons === []) {
            $reasons[] = $this->reason('good', 'Everything is on track', 'No overdue legal or maintenance items were found.');
        }

        return compact('score', 'status', 'label', 'reasons');
    }

    /** @return array{severity: string, title: string, detail: string} */
    private function reason(string $severity, string $title, string $detail): array
    {
        return compact('severity', 'title', 'detail');
    }
}
