<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $stats = [];

        if ($user->hasMaxPermission()) {
            // Admin vê estatísticas de usuários
            $stats['total_users'] = User::count();
            $stats['active_users'] = User::where('active', true)->count();
            $stats['total_orders'] = Order::count();
            $stats['recent_users'] = User::orderBy('created_at', 'desc')->take(5)->get();
        } else {
            // Usuários e gerentes veem estatísticas de pedidos
            $query = Order::query();
            
            if ($user->permission_level === 1) {
                // Usuário comum só vê seus pedidos
                $query->where('user_id', $user->_id);
            }
            
            $stats['total_orders'] = (clone $query)->count();
            $stats['pending_orders'] = (clone $query)->where('status', Order::STATUS_PENDING)->count();
            $stats['processing_orders'] = (clone $query)->where('status', Order::STATUS_PROCESSING)->count();
            $stats['completed_orders'] = (clone $query)->where('status', Order::STATUS_COMPLETED)->count();
            // Lista recente: só pedidos ativos (pendente/processando) para não poluir com mesas já encerradas
            $stats['recent_orders'] = (clone $query)->whereIn('status', [Order::STATUS_PENDING, Order::STATUS_PROCESSING])
                ->orderBy('created_at', 'desc')->take(5)->get();
        }

        return view('dashboard', compact('user', 'stats'));
    }
}
