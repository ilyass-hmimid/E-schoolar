<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaiementProf;
use App\Models\Professeur;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
        $this->paymentService = $paymentService;
    }

    /**
     * Display a listing of professor payments.
     */
    public function index(Request $request)
    {
        $query = PaiementProf::with(['professeur', 'matrie'])
            ->orderBy('date_debut', 'desc')
            ->orderBy('professeur_id');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('statut', $request->status);
        }

        // Filter by professor
        if ($request->filled('professeur_id')) {
            $query->where('professeur_id', $request->professeur_id);
        }

        // Filter by date range
        if ($request->filled(['start_date', 'end_date'])) {
            $query->whereBetween('date_debut', [
                $request->start_date,
                $request->end_date
            ]);
        }

        $payments = $query->paginate(20);
        $professeurs = Professeur::orderBy('name')->get(['id', 'name']);

        return response()->json([
            'payments' => $payments,
            'professeurs' => $professeurs,
            'filters' => $request->only(['status', 'professeur_id', 'start_date', 'end_date'])
        ]);
    }

    /**
     * Calculate payments for a specific period.
     */
    public function calculate(Request $request)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);

        $startDate = Carbon::parse("first day of {$request->month}")->startOfDay();
        $endDate = Carbon::parse("last day of {$request->month}")->endOfDay();

        $results = $this->paymentService->calculateMonthlyPayments($startDate, $endDate);

        return response()->json([
            'success' => true,
            'message' => 'Payments calculated successfully',
            'data' => $results
        ]);
    }

    /**
     * Mark payments as paid.
     */
    public function markAsPaid(Request $request)
    {
        $request->validate([
            'payment_ids' => 'required|array',
            'payment_ids.*' => 'exists:paiements_prof,id',
            'date_paiement' => 'required|date',
            'notes' => 'nullable|string|max:1000'
        ]);

        $count = DB::transaction(function () use ($request) {
            return PaiementProf::whereIn('id', $request->payment_ids)
                ->update([
                    'statut' => 'paye',
                    'date_paiement' => $request->date_paiement,
                    'notes' => $request->notes,
                    'justified_by' => auth()->id()
                ]);
        });

        return response()->json([
            'success' => true,
            'message' => "$count paiements marqués comme payés avec succès.",
            'count' => $count
        ]);
    }

    /**
     * Get payment statistics.
     */
    public function stats()
    {
        $currentMonth = now()->format('Y-m');
        $startDate = Carbon::parse("first day of {$currentMonth}")->startOfDay();
        $endDate = Carbon::parse("last day of {$currentMonth}")->endOfDay();

        $stats = [
            'total_payments' => PaiementProf::count(),
            'pending_payments' => PaiementProf::where('statut', 'en_attente')->count(),
            'paid_this_month' => PaiementProf::where('statut', 'paye')
                ->whereBetween('date_paiement', [$startDate, $endDate])
                ->sum('montant_prof'),
            'total_paid' => PaiementProf::where('statut', 'paye')
                ->sum('montant_prof'),
            'pending_amount' => PaiementProf::where('statut', 'en_attente')
                ->sum('montant_prof')
        ];

        return response()->json($stats);
    }
}
