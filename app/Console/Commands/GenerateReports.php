<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use App\Models\Visit;
use App\Models\Employee;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\PDF;

class GenerateReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:generate {--client= : Client ID to generate reports for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate weekly reports for clients';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Starting automated reports generation...");

        // Get client ID if specified, otherwise run for all clients
        $clientId = $this->option('client');

        if ($clientId) {
            $clients = Client::where('id', $clientId)->get();
            if ($clients->isEmpty()) {
                $this->error("Client with ID {$clientId} not found.");
                return Command::FAILURE;
            }
        } else {
            $clients = Client::where('is_active', true)->get();
        }

        $this->info("Generating reports for " . $clients->count() . " clients...");

        $startDate = Carbon::now()->startOfWeek()->subWeek();
        $endDate = Carbon::now()->endOfWeek()->subWeek();

        foreach ($clients as $client) {
            $this->line("Generating report for client: {$client->name}");

            // Get client statistics
            $employeeCount = Employee::where('client_id', $client->id)->count();
            $totalVisits = Visit::whereHas('employee', function($query) use ($client) {
                $query->where('client_id', $client->id);
            })
            ->whereBetween('envisagee', [$startDate, $endDate])
            ->count();

            $completedVisits = Visit::whereHas('employee', function($query) use ($client) {
                $query->where('client_id', $client->id);
            })
            ->where('etat', 'Effectuée')
            ->whereBetween('envisagee', [$startDate, $endDate])
            ->count();

            $completionRate = $totalVisits > 0 ? round(($completedVisits / $totalVisits) * 100) : 0;

            // Generate PDF report
            $data = [
                'client' => $client,
                'period' => [
                    'start' => $startDate->format('Y-m-d'),
                    'end' => $endDate->format('Y-m-d'),
                ],
                'stats' => [
                    'employeeCount' => $employeeCount,
                    'totalVisits' => $totalVisits,
                    'completedVisits' => $completedVisits,
                    'completionRate' => $completionRate
                ]
            ];

            try {
                $pdf = PDF::loadView('exports.weekly_report', $data)->output();
                $filename = "client_{$client->id}_report_{$startDate->format('Ymd')}.pdf";
                Storage::disk('exports')->put($filename, $pdf);
                $this->info("Report saved: {$filename}");
            } catch (\Exception $e) {
                $this->error("Error generating report for client {$client->name}: " . $e->getMessage());
            }
        }

        $this->info("Report generation completed.");

        return Command::SUCCESS;
    }
}
