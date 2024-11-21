<?php

namespace App\Observers;

use App\Models\Report;
use App\Models\ReportLog;
use App\Models\RetailerAddress; // Add this line
use Illuminate\Support\Facades\Auth;

class ReportObserver
{
    public function created(Report $report)
    {
        $this->logReportChange($report, 'created');
    }

    public function updated(Report $report)
    {
        // Skip logging if it was just created
        if ($report->wasRecentlyCreated) {
            return;
        }

        $this->logReportChange($report, 'updated');
    }

    public function deleted(Report $report)
{
    if (!$report->exists) {
        // The report has already been deleted; avoid creating log
        return;
    }
    
    $this->logReportChange($report, 'deleted');
}

    protected function logReportChange(Report $report, string $action)
    {
        // Create an array to hold both old and new values
        $description = [];

        // Map database field names to readable labels
        $fieldsMap = [
            'retailer_id' => 'Retailer',
            'location' => 'Location',
            'pos' => 'POS',
            'file_1' => 'File 1',
            'file_2' => 'File 2',
            'status' => 'Status',
            'date' => 'Date',
        ];

        if ($action === 'updated') {
            // Get the old values before the update
            $oldValues = $report->getOriginal();
            $newValues = $report->getChanges();

            // Prepare the description for the updated report
            $description = [
                'old' => [],
                'new' => [],
            ];

            foreach ($fieldsMap as $key => $label) {
                if (isset($newValues[$key]) && $oldValues[$key] !== $newValues[$key]) {
                    $description['old'][$label] = $oldValues[$key] ?? 'N/A';
                    $description['new'][$label] = $newValues[$key] ?? 'N/A';
                }
            }
        } elseif ($action === 'created') {
            // For created reports, just log the new values
            $description['new'] = $this->mapFields($report, $fieldsMap);
        } elseif ($action === 'deleted') {
            // Log the old values when a report is deleted
            $description['old'] = $this->mapFields($report, $fieldsMap);
        }

        // Create the log entry
        ReportLog::create([
            'report_id' => $report->id,
            'user_id' => Auth::id(), // Log the user who made the change
            'action' => $action,
            'description' => json_encode($description), // Store details as JSON
        ]);
        // dd($report);
    }

    protected function mapFields(Report $report, array $fieldsMap)
    {
        // Fetch retailer address
        $address = RetailerAddress::where('retailer_id', $report->retailer_id)->first();

        // Construct the location string
        $location = $address 
            ? trim("{$address->street_no} {$address->street_name}, {$address->province}, {$address->city}")
            : '-';

        // Map the report fields to human-readable labels
        return [
            'Retailer' => optional($report->retailer)->dba ?? 'N/A', // Show DBA instead of ID
            'Location' => $location,
            'POS' => $report->pos,
            'File 1' => $report->file_1 ?? '-', // Display N/A if file is not present
            'File 2' => $report->file_2 ?? '-', // Display N/A if file is not present
            'Status' => $report->status,
            'Created At' => $report->created_at->format('Y-m-d H:i:s'), 
        ];
    }
}
