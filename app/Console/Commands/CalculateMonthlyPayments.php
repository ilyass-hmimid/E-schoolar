<?php

namespace App\Console\Commands;

use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CalculateMonthlyPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:calculate 
                            {--month= : The month to calculate payments for (format: YYYY-MM)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate monthly payments for professors';

    /**
     * The payment service instance.
     *
     * @var \App\Services\PaymentService
     */
    protected $paymentService;

    /**
     * Create a new command instance.
     *
     * @param  \App\Services\PaymentService  $paymentService
     * @return void
     */
    public function __construct(PaymentService $paymentService)
    {
        parent::__construct();
        $this->paymentService = $paymentService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $month = $this->option('month') ?: now()->format('Y-m');
            
            // Calculate date range for the month
            $startDate = Carbon::parse("first day of $month")->startOfDay();
            $endDate = Carbon::parse("last day of $month")->endOfDay();
            
            $this->info("Calculating payments for period: {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}");
            
            // Calculate payments
            $results = $this->paymentService->calculateMonthlyPayments($startDate, $endDate);
            
            if (empty($results)) {
                $this->warn('No payments were calculated.');
                return 0;
            }
            
            // Display results
            $this->table(
                ['Professor', 'Subject', 'Status', 'Amount'],
                array_map(function($item) {
                    return [
                        $item['professeur'],
                        $item['matiere'],
                        $item['status'],
                        number_format($item['montant'], 2) . ' MAD'
                    ];
                }, $results)
            );
            
            $totalAmount = array_sum(array_column($results, 'montant'));
            $this->info("\nTotal amount to be paid: " . number_format($totalAmount, 2) . ' MAD');
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Error calculating payments: ' . $e->getMessage());
            Log::error('Error calculating payments: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}
