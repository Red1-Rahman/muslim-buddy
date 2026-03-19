<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ZakatController extends Controller
{
    /**
     * Show Zakat calculator page.
     */
    public function calculator()
    {
        $user = Auth::user();
        $nisabData = null;

        try {
            $response = Http::timeout(8)
                ->retry(2, 500)
                ->get('https://nisab.tahababa.com/nisab.json');

            if ($response->successful()) {
                $nisabData = $response->json();
            }
        } catch (\Throwable $e) {
            $nisabData = null;
        }

        $rawMadhab = strtolower((string) ($user->madhab ?? $user->calculation_method ?? 'hanafi'));

        $map = [
            'hanafi' => 'hanafi',
            'maliki' => 'maliki',
            'shafi' => 'shafii',
            'shafii' => 'shafii',
            'hanbali' => 'hanbali',
        ];

        $userMadhab = $map[$rawMadhab] ?? 'hanafi';

        return view('zakat.calculator', compact('nisabData', 'userMadhab', 'user'));
    }

    /**
     * Record (or reset) yearly Zakat payment reminder state.
     */
    public function markPaid(Request $request)
    {
        $user = Auth::user();
        $currentYear = (int) date('Y');

        if ($request->boolean('reset')) {
            $user->update([
                'zakat_paid_this_year' => false,
                'zakat_paid_year' => null,
            ]);

            return back()->with('success', 'Zakat payment reminder has been reset for this year.');
        }

        $user->update([
            'zakat_paid_this_year' => true,
            'zakat_paid_year' => $currentYear,
        ]);

        return back()->with('success', 'Zakat payment recorded for this year.');
    }
}
