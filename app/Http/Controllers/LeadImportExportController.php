<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Lead;
use App\Models\Source;
use Carbon\Carbon;
use League\Csv\Reader;
use League\Csv\Writer;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LeadImportExportController extends Controller
{
    public function index()
    {
        $importHistory = $this->getImportHistory();
        $exportHistory = $this->getExportHistory();
        $sources = Source::where('status', 'active')->get();
        
        return view('lead-import-export.index', compact('importHistory', 'exportHistory', 'sources'));
    }
    
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240', // 10MB max
            'source_id' => 'nullable|exists:sources,id',
            'skip_duplicates' => 'boolean',
            'update_existing' => 'boolean'
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        try {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            
            // Store the uploaded file temporarily
            $filePath = $file->store('temp-imports');
            
            $result = $this->processImportFile(
                $filePath,
                $extension,
                $request->source_id,
                $request->boolean('skip_duplicates'),
                $request->boolean('update_existing')
            );
            
            // Clean up temporary file
            Storage::delete($filePath);
            
            // Log import activity
            $this->logImportActivity([
                'filename' => $file->getClientOriginalName(),
                'total_rows' => $result['total_rows'],
                'imported' => $result['imported'],
                'updated' => $result['updated'],
                'skipped' => $result['skipped'],
                'errors' => $result['errors'],
                'source_id' => $request->source_id
            ]);
            
            if ($result['errors'] > 0) {
                return back()->with('warning', 
                    "Import completed with {$result['imported']} leads imported, {$result['updated']} updated, {$result['skipped']} skipped, and {$result['errors']} errors."
                );
            }
            
            return back()->with('success', 
                "Successfully imported {$result['imported']} leads and updated {$result['updated']} existing leads."
            );
            
        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }
    
    public function export(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'format' => 'required|in:csv,xlsx',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'source_id' => 'nullable|exists:sources,id',
            'status' => 'nullable|in:new,contacted,qualified,converted,lost',
            'include_custom_fields' => 'boolean'
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        try {
            $query = Lead::with('source');
            
            // Apply filters
            if ($request->date_from) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            
            if ($request->date_to) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
            
            if ($request->source_id) {
                $query->where('source_id', $request->source_id);
            }
            
            if ($request->status) {
                $query->where('status', $request->status);
            }
            
            $leads = $query->get();
            
            if ($leads->isEmpty()) {
                return back()->with('warning', 'No leads found matching the specified criteria.');
            }
            
            $filename = 'leads_export_' . now()->format('Y-m-d_H-i-s');
            
            if ($request->format === 'csv') {
                return $this->exportToCsv($leads, $filename, $request->boolean('include_custom_fields'));
            } else {
                return $this->exportToExcel($leads, $filename, $request->boolean('include_custom_fields'));
            }
            
        } catch (\Exception $e) {
            return back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }
    
    public function downloadTemplate(Request $request)
    {
        $format = $request->get('format', 'csv');
        
        $headers = [
            'name',
            'email',
            'phone',
            'company',
            'position',
            'source_name',
            'status',
            'value',
            'notes',
            'created_at'
        ];
        
        $sampleData = [
            [
                'John Doe',
                'john@example.com',
                '+1234567890',
                'Example Corp',
                'Manager',
                'Website',
                'new',
                '1000.00',
                'Sample lead from website contact form',
                '2024-01-15 10:30:00'
            ],
            [
                'Jane Smith',
                'jane@company.com',
                '+0987654321',
                'Tech Solutions',
                'Director',
                'Google Ads',
                'contacted',
                '2500.00',
                'Interested in premium package',
                '2024-01-16 14:20:00'
            ]
        ];
        
        if ($format === 'csv') {
            return $this->generateCsvTemplate($headers, $sampleData);
        } else {
            return $this->generateExcelTemplate($headers, $sampleData);
        }
    }
    
    public function getImportHistory()
    {
        // This would typically come from a database table
        // For now, we'll return sample data
        return collect([
            [
                'id' => 1,
                'filename' => 'leads_batch_1.csv',
                'total_rows' => 150,
                'imported' => 145,
                'updated' => 3,
                'skipped' => 2,
                'errors' => 0,
                'created_at' => now()->subDays(2),
                'status' => 'completed'
            ],
            [
                'id' => 2,
                'filename' => 'marketing_leads.xlsx',
                'total_rows' => 89,
                'imported' => 85,
                'updated' => 0,
                'skipped' => 1,
                'errors' => 3,
                'created_at' => now()->subDays(5),
                'status' => 'completed'
            ]
        ]);
    }
    
    public function getExportHistory()
    {
        // This would typically come from a database table
        // For now, we'll return sample data
        return collect([
            [
                'id' => 1,
                'filename' => 'leads_export_2024-01-15.xlsx',
                'format' => 'xlsx',
                'records' => 234,
                'filters' => 'All leads, Last 30 days',
                'created_at' => now()->subDays(1),
                'file_size' => '45.2 KB'
            ],
            [
                'id' => 2,
                'filename' => 'converted_leads.csv',
                'format' => 'csv',
                'records' => 67,
                'filters' => 'Status: Converted, Source: Google Ads',
                'created_at' => now()->subDays(3),
                'file_size' => '12.8 KB'
            ]
        ]);
    }
    
    private function processImportFile($filePath, $extension, $sourceId, $skipDuplicates, $updateExisting)
    {
        $result = [
            'total_rows' => 0,
            'imported' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => 0
        ];
        
        if ($extension === 'csv') {
            $data = $this->readCsvFile($filePath);
        } else {
            $data = $this->readExcelFile($filePath);
        }
        
        $result['total_rows'] = count($data);
        
        foreach ($data as $row) {
            try {
                $processResult = $this->processLeadRow($row, $sourceId, $skipDuplicates, $updateExisting);
                $result[$processResult]++;
            } catch (\Exception $e) {
                $result['errors']++;
            }
        }
        
        return $result;
    }
    
    private function readCsvFile($filePath)
    {
        $csv = Reader::createFromPath(Storage::path($filePath), 'r');
        $csv->setHeaderOffset(0);
        
        return iterator_to_array($csv->getRecords());
    }
    
    private function readExcelFile($filePath)
    {
        $spreadsheet = IOFactory::load(Storage::path($filePath));
        $worksheet = $spreadsheet->getActiveSheet();
        $data = $worksheet->toArray();
        
        // Convert to associative array using first row as headers
        $headers = array_shift($data);
        $result = [];
        
        foreach ($data as $row) {
            $result[] = array_combine($headers, $row);
        }
        
        return $result;
    }
    
    private function processLeadRow($row, $sourceId, $skipDuplicates, $updateExisting)
    {
        // Validate required fields
        if (empty($row['email']) || empty($row['name'])) {
            throw new \Exception('Missing required fields: name and email');
        }
        
        // Check for existing lead
        $existingLead = Lead::where('email', $row['email'])->first();
        
        if ($existingLead) {
            if ($skipDuplicates) {
                return 'skipped';
            } elseif ($updateExisting) {
                $this->updateLead($existingLead, $row, $sourceId);
                return 'updated';
            } else {
                return 'skipped';
            }
        }
        
        // Create new lead
        $this->createLead($row, $sourceId);
        return 'imported';
    }
    
    private function createLead($row, $sourceId)
    {
        // Determine source
        $source = null;
        if ($sourceId) {
            $source = Source::find($sourceId);
        } elseif (!empty($row['source_name'])) {
            $source = Source::where('name', $row['source_name'])->first();
        }
        
        Lead::create([
            'name' => $row['name'],
            'email' => $row['email'],
            'phone' => $row['phone'] ?? null,
            'company' => $row['company'] ?? null,
            'position' => $row['position'] ?? null,
            'source_id' => $source ? $source->id : null,
            'status' => $row['status'] ?? 'new',
            'value' => $row['value'] ?? 0,
            'notes' => $row['notes'] ?? null,
            'created_at' => !empty($row['created_at']) ? Carbon::parse($row['created_at']) : now()
        ]);
    }
    
    private function updateLead($lead, $row, $sourceId)
    {
        $updateData = [];
        
        if (!empty($row['name'])) $updateData['name'] = $row['name'];
        if (!empty($row['phone'])) $updateData['phone'] = $row['phone'];
        if (!empty($row['company'])) $updateData['company'] = $row['company'];
        if (!empty($row['position'])) $updateData['position'] = $row['position'];
        if (!empty($row['status'])) $updateData['status'] = $row['status'];
        if (!empty($row['value'])) $updateData['value'] = $row['value'];
        if (!empty($row['notes'])) $updateData['notes'] = $row['notes'];
        
        // Update source if provided
        if ($sourceId) {
            $updateData['source_id'] = $sourceId;
        } elseif (!empty($row['source_name'])) {
            $source = Source::where('name', $row['source_name'])->first();
            if ($source) {
                $updateData['source_id'] = $source->id;
            }
        }
        
        $lead->update($updateData);
    }
    
    private function exportToCsv($leads, $filename, $includeCustomFields)
    {
        $csv = Writer::createFromString('');
        
        // Headers
        $headers = [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Company',
            'Position',
            'Source',
            'Status',
            'Value',
            'Notes',
            'Created At',
            'Updated At'
        ];
        
        $csv->insertOne($headers);
        
        // Data rows
        foreach ($leads as $lead) {
            $row = [
                $lead->id,
                $lead->name,
                $lead->email,
                $lead->phone,
                $lead->company,
                $lead->position,
                $lead->source ? $lead->source->name : '',
                $lead->status,
                $lead->value,
                $lead->notes,
                $lead->created_at->format('Y-m-d H:i:s'),
                $lead->updated_at->format('Y-m-d H:i:s')
            ];
            
            $csv->insertOne($row);
        }
        
        return response($csv->toString(), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"'
        ]);
    }
    
    private function exportToExcel($leads, $filename, $includeCustomFields)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Headers
        $headers = [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Company',
            'Position',
            'Source',
            'Status',
            'Value',
            'Notes',
            'Created At',
            'Updated At'
        ];
        
        $sheet->fromArray($headers, null, 'A1');
        
        // Style headers
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E5E7EB']
            ]
        ];
        $sheet->getStyle('A1:L1')->applyFromArray($headerStyle);
        
        // Data rows
        $row = 2;
        foreach ($leads as $lead) {
            $data = [
                $lead->id,
                $lead->name,
                $lead->email,
                $lead->phone,
                $lead->company,
                $lead->position,
                $lead->source ? $lead->source->name : '',
                $lead->status,
                $lead->value,
                $lead->notes,
                $lead->created_at->format('Y-m-d H:i:s'),
                $lead->updated_at->format('Y-m-d H:i:s')
            ];
            
            $sheet->fromArray($data, null, 'A' . $row);
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'L') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        $writer = new Xlsx($spreadsheet);
        
        $tempFile = tempnam(sys_get_temp_dir(), 'export');
        $writer->save($tempFile);
        
        return response()->download($tempFile, $filename . '.xlsx')->deleteFileAfterSend();
    }
    
    private function generateCsvTemplate($headers, $sampleData)
    {
        $csv = Writer::createFromString('');
        $csv->insertOne($headers);
        
        foreach ($sampleData as $row) {
            $csv->insertOne($row);
        }
        
        return response($csv->toString(), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="lead_import_template.csv"'
        ]);
    }
    
    private function generateExcelTemplate($headers, $sampleData)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Headers
        $sheet->fromArray($headers, null, 'A1');
        
        // Style headers
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E5E7EB']
            ]
        ];
        $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);
        
        // Sample data
        $row = 2;
        foreach ($sampleData as $data) {
            $sheet->fromArray($data, null, 'A' . $row);
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'J') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        $writer = new Xlsx($spreadsheet);
        
        $tempFile = tempnam(sys_get_temp_dir(), 'template');
        $writer->save($tempFile);
        
        return response()->download($tempFile, 'lead_import_template.xlsx')->deleteFileAfterSend();
    }
    
    private function logImportActivity($data)
    {
        // This would typically save to a database table
        // For now, we'll just log it
        Log::info('Lead import completed', $data);
    }
}