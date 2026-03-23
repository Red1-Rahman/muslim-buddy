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
        $fxRates = null;

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

        // Fallback when external nisab API is unavailable (Render network failures)
        if ($nisabData === null) {
            $nisabData = [
                'meta' => ['timestamp' => 'Fallback data - gold/silver prices approximate'],
                'nisab' => [
                    'hanafi'  => ['gold' => ['grams' => 87.48,  'description' => '87.48g gold'], 'silver' => ['grams' => 612.36, 'description' => '612.36g silver']],
                    'maliki'  => ['gold' => ['grams' => 85.0,   'description' => '85g gold'],   'silver' => ['grams' => 595.0,  'description' => '595g silver']],
                    'shafii'  => ['gold' => ['grams' => 85.0,   'description' => '85g gold'],   'silver' => ['grams' => 595.0,  'description' => '595g silver']],
                    'hanbali' => ['gold' => ['grams' => 85.0,   'description' => '85g gold'],   'silver' => ['grams' => 595.0,  'description' => '595g silver']],
                ],
                'prices' => ['gold' => ['per_gram' => 92.0], 'silver' => ['per_gram' => 0.92]],
            ];
        }

        try {
            $fxResponse = Http::timeout(8)
                ->retry(2, 500)
                ->get('https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/usd.json');

            if ($fxResponse->successful()) {
                $fxRates = data_get($fxResponse->json(), 'usd');
            }
        } catch (\Throwable $e) {
            $fxRates = null;
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

        return view('zakat.calculator', compact('nisabData', 'userMadhab', 'user', 'fxRates'));
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
