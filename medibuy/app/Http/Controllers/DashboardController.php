<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Equipment;
use App\Models\Invoice;
use App\Models\Rental;

class DashboardController extends Controller
{
    public function index()
    {
        $totalEquipments = Equipment::count();
        $availableEquipments = Equipment::where('status', 'Disponible')->count();

        $activeRentals = Rental::whereIn('status', ['Programada', 'En curso'])->count();

        $totalClients = Cliente::count();

        $pendingStatuses = ['Pendiente', 'Parcial', 'Vencido'];

        $pendingAmount = Invoice::whereIn('payment_status', $pendingStatuses)->sum('total');
        $pendingInvoices = Invoice::whereIn('payment_status', $pendingStatuses)->count();

        $equipmentStatus = [
            [
                'label' => 'Disponible',
                'value' => Equipment::where('status', 'Disponible')->count(),
            ],
            [
                'label' => 'Rentado',
                'value' => Equipment::where('status', 'Rentado')->count(),
            ],
            [
                'label' => 'Mantenimiento',
                'value' => Equipment::where('status', 'Mantenimiento')->count(),
            ],
            [
                'label' => 'Fuera de servicio',
                'value' => Equipment::where('status', 'Fuera de servicio')->count(),
            ],
        ];

        $recentRentals = Rental::latest()->take(6)->get();

        return view('dashboard.index', compact(
            'totalEquipments',
            'availableEquipments',
            'activeRentals',
            'totalClients',
            'pendingAmount',
            'pendingInvoices',
            'equipmentStatus',
            'recentRentals'
        ));
    }
}