@extends('layouts.app')

@section('title', 'Zakat Calculator')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-blue-50 py-6">
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8 bg-white rounded-2xl shadow-lg border border-emerald-100 p-6 md:p-8">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Zakat Calculator</h1>
                <p class="text-gray-600 mt-2 max-w-3xl">
                    Zakat is one of the five pillars of Islam — an obligation on wealth that meets or exceeds the Nisab threshold, held for one lunar year.
                </p>
            </div>
            <div class="inline-flex items-center text-sm bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full">
                <i class="fas fa-scale-balanced mr-2"></i> Zakat Reminder & Estimate
            </div>
        </div>

        <div class="mt-4 text-sm text-gray-600">
            Live Nisab data source:
            <a href="https://nisab.tahababa.com" target="_blank" rel="noopener noreferrer" class="text-emerald-700 font-medium hover:text-emerald-900 underline">
                nisab.tahababa.com
            </a>
        </div>
    </div>

    @if(!$nisabData)
        <div class="mb-6 rounded-lg border border-amber-300 bg-amber-50 p-4 text-amber-900">
            <p class="font-medium">Live Nisab data is currently unavailable. Please try again shortly or consult a scholar for current thresholds.</p>
        </div>
    @endif

    @if($nisabData)
        @php
            $madhabData = data_get($nisabData, "nisab.{$userMadhab}", []);
            $goldGrams = data_get($madhabData, 'gold.grams');
            $silverGrams = data_get($madhabData, 'silver.grams');
            $goldUsd = data_get($madhabData, 'gold.values.USD');
            $silverUsd = data_get($madhabData, 'silver.values.USD');
            $timestamp = data_get($nisabData, 'meta.timestamp');
        @endphp

        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-emerald-900 mb-3">Live Nisab Thresholds ({{ strtoupper($userMadhab) }})</h2>
            <div class="grid md:grid-cols-2 gap-4 text-sm text-emerald-900">
                <div class="bg-white/70 rounded-lg p-3 border border-emerald-100">
                    <p class="font-medium">Gold nisab:</p>
                    <p>{{ $goldGrams }} grams = ${{ number_format((float) $goldUsd, 2) }} (USD)</p>
                </div>
                <div class="bg-white/70 rounded-lg p-3 border border-emerald-100">
                    <p class="font-medium">Silver nisab:</p>
                    <p>{{ $silverGrams }} grams = ${{ number_format((float) $silverUsd, 2) }} (USD)</p>
                </div>
            </div>
            <p class="text-xs mt-3 text-emerald-800">Timestamp: {{ $timestamp }}</p>
            <p class="text-xs mt-1 text-emerald-800">Values updated 6× daily. Source: <a href="https://nisab.tahababa.com" target="_blank" rel="noopener noreferrer" class="underline font-medium">nisab.tahababa.com</a></p>
            <p class="text-xs mt-1 text-emerald-800">Islam permits using either gold or silver nisab. Many scholars recommend silver as it is lower and more inclusive.</p>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Wealth Inputs</h2>

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label for="currency" class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                <select id="currency" class="w-full rounded-lg border-gray-300 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="USD">USD</option>
                    <option value="GBP">GBP</option>
                    <option value="EUR">EUR</option>
                    <option value="BDT">BDT</option>
                    <option value="MYR">MYR</option>
                    <option value="SAR">SAR</option>
                </select>
            </div>

            <div class="md:col-span-2 grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cash and bank savings</label>
                    <input id="cash" type="number" min="0" step="0.01" class="w-full rounded-lg border-gray-300" value="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gold owned (grams)</label>
                    <input id="gold_grams" type="number" min="0" step="0.01" class="w-full rounded-lg border-gray-300" value="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Silver owned (grams)</label>
                    <input id="silver_grams" type="number" min="0" step="0.01" class="w-full rounded-lg border-gray-300" value="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Business inventory value</label>
                    <input id="inventory" type="number" min="0" step="0.01" class="w-full rounded-lg border-gray-300" value="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Money owed to you / receivables</label>
                    <input id="receivables" type="number" min="0" step="0.01" class="w-full rounded-lg border-gray-300" value="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Debts you owe</label>
                    <input id="debts" type="number" min="0" step="0.01" class="w-full rounded-lg border-gray-300" value="0">
                </div>
            </div>
        </div>

        <div id="currency-note" class="mt-3 text-xs text-gray-500"></div>

        <div class="mt-5 rounded-xl bg-gray-50 p-4 border border-gray-200">
            <p id="nisab-display" class="text-sm text-gray-700 mb-2"></p>
            <p id="wealth-display" class="text-sm text-gray-700 mb-2"></p>
            <div id="result-box" class="rounded-md p-3 text-sm"></div>
        </div>
    </div>

    <div class="mb-6 rounded-xl border border-blue-200 bg-blue-50 p-4 text-blue-900 shadow-sm">
        <p class="font-medium mb-1"><i class="fas fa-circle-info mr-1"></i> Important: Zakat is only obligatory if your wealth has been at or above the Nisab threshold for a complete lunar year (Hawl).</p>
        <p>If your wealth dropped below Nisab at any point during the year, Zakat may not be obligatory. Consult a qualified scholar for your specific situation.</p>
    </div>

    @php
        $currentYear = (int) date('Y');
        $isPaidThisYear = (bool) $user->zakat_paid_this_year && (int) $user->zakat_paid_year === $currentYear;
    @endphp
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-3">Mark as Paid</h3>

        @if($isPaidThisYear)
            <p class="text-sm text-gray-700 mb-3">You have recorded that you paid Zakat in {{ $currentYear }}.</p>
            <form action="{{ route('zakat.markPaid') }}" method="POST" class="inline-block">
                @csrf
                <input type="hidden" name="reset" value="1">
                <button type="submit" class="text-sm text-gray-600 underline hover:text-gray-800">Reset</button>
            </form>
        @else
            <form action="{{ route('zakat.markPaid') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-700 text-white hover:bg-emerald-800 shadow-sm">Record that I have paid Zakat this year</button>
            </form>
            <p class="text-xs text-gray-500 mt-2">This is a personal reminder only. It does not verify payment or notify anyone.</p>
        @endif
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-4 text-xs text-gray-600 mb-10">
        <p>Zakat calculations shown are estimates for guidance only. Scholars may differ on certain asset types and thresholds. Always consult a qualified Islamic scholar for your personal obligation.</p>
        <p class="mt-2">Nisab data acknowledgment: <a href="https://nisab.tahababa.com" target="_blank" rel="noopener noreferrer" class="text-emerald-700 underline font-medium">nisab.tahababa.com</a></p>
    </div>
</div>
</div>

@php
    $madhabData = data_get($nisabData, "nisab.{$userMadhab}", []);
    $goldValues = data_get($madhabData, 'gold.values', []);
    $silverValues = data_get($madhabData, 'silver.values', []);
@endphp

<script>
(function () {
    const nisabData = @json($nisabData);
    const goldValues = @json($goldValues);
    const silverValues = @json($silverValues);

    const currencyEl = document.getElementById('currency');
    const fields = ['cash', 'gold_grams', 'silver_grams', 'inventory', 'receivables', 'debts']
        .map(id => document.getElementById(id));

    const currencyNote = document.getElementById('currency-note');
    const nisabDisplay = document.getElementById('nisab-display');
    const wealthDisplay = document.getElementById('wealth-display');
    const resultBox = document.getElementById('result-box');

    function num(el) {
        const value = parseFloat(el.value);
        return Number.isFinite(value) ? value : 0;
    }

    function formatAmount(value, currency) {
        return `${currency} ${Number(value).toLocaleString(undefined, { maximumFractionDigits: 2 })}`;
    }

    function calculate() {
        const currency = currencyEl.value;

        const cash = num(document.getElementById('cash'));
        const goldGrams = num(document.getElementById('gold_grams'));
        const silverGrams = num(document.getElementById('silver_grams'));
        const inventory = num(document.getElementById('inventory'));
        const receivables = num(document.getElementById('receivables'));
        const debts = num(document.getElementById('debts'));

        const goldPerGramUsd = Number(nisabData?.prices?.gold?.per_gram || 0);
        const silverPerGramUsd = Number(nisabData?.prices?.silver?.per_gram || 0);

        const goldValueUsd = goldGrams * goldPerGramUsd;
        const silverValueUsd = silverGrams * silverPerGramUsd;

        const totalUsd = cash + goldValueUsd + silverValueUsd + inventory + receivables - debts;

        const goldNisabCurrency = Number(goldValues?.[currency] ?? 0);
        const silverNisabCurrency = Number(silverValues?.[currency] ?? 0);
        const goldNisabUsd = Number(goldValues?.USD ?? 0);
        const silverNisabUsd = Number(silverValues?.USD ?? 0);

        let exchangeRate = 1;
        let hasCurrencyNisab = goldNisabCurrency > 0 && silverNisabCurrency > 0;

        if (currency !== 'USD' && hasCurrencyNisab && goldNisabUsd > 0) {
            exchangeRate = goldNisabCurrency / goldNisabUsd;
        }

        let displayTotal = totalUsd;
        let displayGoldNisab = goldNisabUsd;
        let displaySilverNisab = silverNisabUsd;

        if (currency !== 'USD' && hasCurrencyNisab) {
            displayTotal = totalUsd * exchangeRate;
            displayGoldNisab = goldNisabCurrency;
            displaySilverNisab = silverNisabCurrency;
            currencyNote.textContent = '';
        } else if (currency === 'BDT' || currency === 'SAR') {
            currencyNote.textContent = 'BDT and SAR thresholds are shown in USD only. Convert using today\'s exchange rate.';
        } else {
            currencyNote.textContent = '';
        }

        const nisabThreshold = Math.min(displayGoldNisab || Number.MAX_VALUE, displaySilverNisab || Number.MAX_VALUE);

        nisabDisplay.textContent = `Gold Nisab: ${formatAmount(displayGoldNisab, currency === 'USD' || hasCurrencyNisab ? currency : 'USD')} | Silver Nisab: ${formatAmount(displaySilverNisab, currency === 'USD' || hasCurrencyNisab ? currency : 'USD')} | Applied (lower): ${formatAmount(nisabThreshold, currency === 'USD' || hasCurrencyNisab ? currency : 'USD')}`;
        wealthDisplay.textContent = `Total zakatable wealth: ${formatAmount(displayTotal, currency === 'USD' || hasCurrencyNisab ? currency : 'USD')}`;

        if (!Number.isFinite(nisabThreshold) || nisabThreshold <= 0) {
            resultBox.className = 'rounded-md p-3 text-sm bg-amber-50 text-amber-900 border border-amber-200';
            resultBox.textContent = 'Unable to determine Nisab threshold for the selected currency right now. Please use USD or consult a scholar.';
            return;
        }

        if (displayTotal >= nisabThreshold) {
            const zakatDue = displayTotal * 0.025;
            resultBox.className = 'rounded-md p-3 text-sm bg-emerald-50 text-emerald-900 border border-emerald-200';
            resultBox.innerHTML = `Your Zakat due is approximately <strong>${formatAmount(zakatDue, currency === 'USD' || hasCurrencyNisab ? currency : 'USD')}</strong>.<br>This is 2.5% of your total zakatable wealth.`;
        } else {
            resultBox.className = 'rounded-md p-3 text-sm bg-gray-100 text-gray-800 border border-gray-200';
            resultBox.textContent = 'Your wealth is below the Nisab threshold. Zakat is not obligatory this year.';
        }
    }

    currencyEl.addEventListener('change', calculate);
    fields.forEach(el => el.addEventListener('input', calculate));
    calculate();
})();
</script>
@endsection
