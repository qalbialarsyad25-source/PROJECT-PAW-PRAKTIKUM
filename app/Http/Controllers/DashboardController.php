<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display user's command center dashboard.
     */
    public function index(): View
    {
        $user = Auth::user();

        $characters = $user->characters()
            ->latest()
            ->take(6)
            ->get();

        $activeCampaigns = $user->campaigns()
            ->with('character')
            ->where('status', 'Active')
            ->latest()
            ->take(5)
            ->get();

        $completedCampaigns = $user->campaigns()
            ->with('character')
            ->where('status', 'Completed')
            ->latest()
            ->take(5)
            ->get();

        $totalCharacters = $user->characters()->count();
        $totalCampaigns = $user->campaigns()->count();
        $activeCount = $user->campaigns()->where('status', 'Active')->count();
        $completedCount = $user->campaigns()->where('status', 'Completed')->count();
        $highestLevel = $user->characters()->max('level') ?? 1;

        return view('dashboard.index', compact(
            'user',
            'characters',
            'activeCampaigns',
            'completedCampaigns',
            'totalCharacters',
            'totalCampaigns',
            'activeCount',
            'completedCount',
            'highestLevel'
        ));
    }
}
