@extends('layout.master')

@push('plugin-styles')
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .header-bg {
            background-color: #2D335E;
        }
        .text-custom-blue {
            color: #2D335E;
        }
        .table-header {
            background-color: #2D335E;
            color: white;
        }
        .status-badge {
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-paid {
            background-color: #00BCD4;
            color: white;
        }
    </style>
@endpush

@section('content')
<div class="container mx-auto p-4 bg-white min-h-screen">
    <!-- Top Action Bar -->
    <div class="flex justify-between items-center mb-6 no-print">
        <div>
            <a href="{{ url('list_ya_wateja') }}" class="text-gray-600 hover:text-gray-900 flex items-center">
                <i class="icon-arrow-left8 mr-2"></i> Back to List
            </a>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('pacel_reg', $data2->id) }}" target="_blank" rel="opener" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow flex items-center transition">
                <i class="icon-printer mr-2"></i> Print Delivery Note
            </a>
        </div>
    </div>

    <!-- Header Section -->
    <div class="flex justify-between items-start mb-8">
        <div class="flex items-center">
            @if($company && $company->logo)
                <img src="{{ asset('storage/' . $company->logo) }}" alt="Logo" class="h-24">
            @else
                <div class="h-24 w-24 bg-gray-100 flex items-center justify-center text-gray-400 font-bold rounded">
                    LOGO
                </div>
            @endif
        </div>
        <div class="text-right">
            <p class="text-lg font-bold text-gray-700 uppercase">REF NO : {{ 'CSTM-' . str_pad($data2->id, 3, '0', STR_PAD_LEFT) }}</p>
            <div class="mt-4 text-sm text-gray-600 space-y-1">
                <p><span class="font-semibold">Booking Date :</span> {{ $data2->created_at->format('d/m/Y') }}</p>
                <p><span class="font-semibold">Booking Agent :</span> {{ $agent->name ?? 'N/A' }}</p>
                <p><span class="font-semibold">Payment Status :</span> 
                    @php
                        $totalAmount = $data->sum(function($p) { return (float) str_replace(',', '', $p->jumla); });
                        $totalPaid = $data->sum(function($p) { return (float) str_replace(',', '', $p->ela_iliyopokelewa); });
                        $status = 'Unpaid';
                        if($totalPaid >= $totalAmount && $totalAmount > 0) $status = 'Fully Paid';
                        elseif($totalPaid > 0) $status = 'Partially Paid';
                    @endphp
                    <span class="status-badge status-paid">{{ $status }}</span>
                </p>
                <p><span class="font-semibold">Currency :</span> TZS</p>
            </div>
        </div>
    </div>

    <!-- Info Boxes -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
        <!-- Mpokeaji (Ofisi) -->
        <div>
            <div class="table-header px-4 py-2 font-bold">
                Mpokeaji (Ofisi):
            </div>
            <div class="border border-t-0 p-4 space-y-1">
                <p class="text-xl font-bold text-gray-800">{{ $data2->mpokeaji }}</p>
                <p class="text-gray-600">Phone : {{ $data2->receiver_phone }}</p>
            </div>
        </div>

        <!-- Mteja -->
        <div>
            <div class="table-header px-4 py-2 font-bold">
                Mteja:
            </div>
            <div class="border border-t-0 p-4 space-y-1">
                <p class="text-xl font-bold text-gray-800">{{ $data2->mteja }}</p>
                <p class="text-gray-600">Phone : {{ $data2->customer_phone }}</p>
            </div>
        </div>
    </div>

    <!-- Mizigo Table -->
    <div class="mb-8">
        @php
            $totalStoo = 0;
            $totalOnCar = 0;
            $totalDropped = 0;
            foreach ($data as $p) {
                $q = \App\Support\ParcelQuantitySummary::breakdown($p);
                $totalStoo += $q['stoo'];
                $totalOnCar += $q['on_car'];
                $totalDropped += $q['dropped'];
            }
        @endphp

        @if($totalStoo > 0)
            <div class="mb-4 bg-orange-50 border border-orange-200 text-orange-900 px-4 py-3 rounded">
                <strong>Stoo iliyobaki:</strong> jumla <strong>{{ $totalStoo }}</strong> bado haijapakiwa.
                @if($totalOnCar > 0)
                    (Kwenye gari: {{ $totalOnCar }}@if($totalDropped > 0), imeshushwa: {{ $totalDropped }}@endif)
                @endif
            </div>
        @endif

        <table class="w-full border-collapse">
            <thead>
                <tr class="table-header">
                    <th class="border px-4 py-2 text-left w-12">#</th>
                    <th class="border px-4 py-2 text-left">Date</th>
                    <th class="border px-4 py-2 text-left">Mteja</th>
                    <th class="border px-4 py-2 text-left">Jina la Mzigo</th>
                    <th class="border px-4 py-2 text-left">Route</th>
                    <th class="border px-4 py-2 text-center">Idadi</th>
                    <th class="border px-4 py-2 text-center">Bado Stoo</th>
                    <th class="border px-4 py-2 text-center">Gari</th>
                    <th class="border px-4 py-2 text-center">Imeshushwa</th>
                    <th class="border px-4 py-2 text-left">Hali ya Mzigo</th>
                    <th class="border px-4 py-2 text-right">Gharama</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $parcel)
                @php $qty = \App\Support\ParcelQuantitySummary::breakdown($parcel); @endphp
                <tr class="{{ $qty['stoo'] > 0 ? 'bg-orange-50' : '' }}">
                    <td class="border px-4 py-3 text-center">{{ $index + 1 }}</td>
                    <td class="border px-4 py-3">{{ $parcel->created_at->format('d/m/Y') }}</td>
                    <td class="border px-4 py-3 font-semibold text-custom-blue">{{ $parcel->mteja ?: '-' }}</td>
                    <td class="border px-4 py-3 font-bold">{{ $parcel->name }}</td>
                    <td class="border px-4 py-3">
                        <div class="text-sm">
                            {{ $parcel->mzigo_unapotoka }} <i class="icon-arrow-right13 mx-1 text-gray-400"></i> {{ $parcel->mzigo_unapokwenda }}
                        </div>
                    </td>
                    <td class="border px-4 py-3 text-center">{{ $qty['total'] }}</td>
                    <td class="border px-4 py-3 text-center font-bold {{ $qty['stoo'] > 0 ? 'text-orange-600' : 'text-gray-400' }}">{{ $qty['stoo'] }}</td>
                    <td class="border px-4 py-3 text-center font-bold text-indigo-600">{{ $qty['on_car'] }}</td>
                    <td class="border px-4 py-3 text-center font-bold text-red-600">{{ $qty['dropped'] }}</td>
                    <td class="border px-4 py-3">
                        @php
                            $statusLabel = \App\Support\ParcelStatusLabel::label(
                                $parcel->activity,
                                optional($parcel->car)->status,
                                $parcel->idadi_shusha,
                                $parcel->idadi
                            );
                            $statusClass = \App\Support\ParcelStatusLabel::badgeClasses(
                                $parcel->activity,
                                optional($parcel->car)->status
                            );
                        @endphp
                        <span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                        @if($qty['is_partially_loaded'])
                            <div class="text-xs text-orange-700 mt-1 font-semibold">Sehemu bado stoo</div>
                        @endif
                    </td>
                    <td class="border px-4 py-3 text-right font-bold">{{ number_format((float) str_replace(',', '', $parcel->jumla), 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-gray-50">
                    <td colspan="5" class="border px-4 py-2 text-right font-bold">Jumla</td>
                    <td class="border px-4 py-2 text-center font-bold">{{ $data->sum('idadi') }}</td>
                    <td class="border px-4 py-2 text-center font-bold text-orange-600">{{ $totalStoo }}</td>
                    <td class="border px-4 py-2 text-center font-bold text-indigo-600">{{ $totalOnCar }}</td>
                    <td class="border px-4 py-2 text-center font-bold text-red-600">{{ $totalDropped }}</td>
                    <td class="border px-4 py-2"></td>
                    <td class="border px-4 py-2 text-right font-bold text-lg text-custom-blue">
                        {{ number_format($totalAmount, 2) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Delivery Progress / Unloading Details -->
    @php
        $hasDropAnalysis = false;
        foreach ($data as $parcel) {
            $qty = \App\Support\ParcelQuantitySummary::breakdown($parcel);
            $analysis = $dropAnalysisByParcel[$parcel->id] ?? null;
            if ($qty['on_car'] > 0 || $qty['dropped'] > 0 || $qty['stoo'] > 0
                || ($analysis && ($analysis['njiani']->isNotEmpty() || $analysis['mwisho']->isNotEmpty()))) {
                $hasDropAnalysis = true;
                break;
            }
        }
    @endphp

    @if($hasDropAnalysis)
    <div class="mt-12 border rounded-lg overflow-hidden no-print">
        <div class="table-header px-4 py-2 font-bold">
            Uchambuzi wa Mizigo (Stoo, Gari, Kushusha)
        </div>
        <div class="p-4 bg-gray-50 space-y-4">
            @foreach($data as $parcel)
                @php
                    $qty = \App\Support\ParcelQuantitySummary::breakdown($parcel);
                    $analysis = $dropAnalysisByParcel[$parcel->id] ?? null;
                    $hasParcelDrops = $analysis && ($analysis['njiani']->isNotEmpty() || $analysis['mwisho']->isNotEmpty());
                    $showParcel = $qty['on_car'] > 0 || $qty['dropped'] > 0 || $qty['stoo'] > 0 || $hasParcelDrops;
                @endphp
                @if($showParcel)
                    @php $summary = $analysis['summary'] ?? ['njiani_qty' => 0, 'mwisho_qty' => 0, 'njiani_amount' => 0, 'mwisho_amount' => 0]; @endphp
                    <div class="bg-white p-4 rounded border shadow-sm">
                        <div class="flex flex-wrap justify-between items-start gap-2 border-b pb-3 mb-3">
                            <p class="font-bold text-custom-blue text-lg">Mzigo: {{ $parcel->name }}</p>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
                            <div class="bg-gray-50 p-3 rounded border text-center">
                                <p class="text-xs text-gray-500 uppercase font-bold">Jumla</p>
                                <p class="text-xl font-bold">{{ $qty['total'] }}</p>
                            </div>
                            <div class="bg-orange-50 p-3 rounded border border-orange-100 text-center">
                                <p class="text-xs text-gray-500 uppercase font-bold">Bado Stoo</p>
                                <p class="text-xl font-bold text-orange-600">{{ $qty['stoo'] }}</p>
                            </div>
                            <div class="bg-indigo-50 p-3 rounded border border-indigo-100 text-center">
                                <p class="text-xs text-gray-500 uppercase font-bold">Kwenye Gari</p>
                                <p class="text-xl font-bold text-indigo-600">{{ $qty['on_car'] }}</p>
                            </div>
                            <div class="bg-red-50 p-3 rounded border border-red-100 text-center">
                                <p class="text-xs text-gray-500 uppercase font-bold">Imeshushwa</p>
                                <p class="text-xl font-bold text-red-600">{{ $qty['dropped'] }}</p>
                            </div>
                        </div>

                        @if($qty['is_partially_loaded'])
                            <div class="bg-orange-50 p-3 rounded border border-orange-100 text-sm text-orange-900 mb-4">
                                Sehemu bado iko stoo - hujapakia yote. Idadi <strong>{{ $qty['stoo'] }}</strong> inaweza kupakiwa baadae.
                            </div>
                        @endif

                        @if($hasParcelDrops)
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4 pt-2 border-t">
                            @if(config('app.allow_enroute_drops'))
                            <div class="bg-blue-50 p-3 rounded border border-blue-100 text-center">
                                <p class="text-xs text-gray-500 uppercase font-bold">Njiani</p>
                                <p class="text-xl font-bold text-blue-600">{{ $summary['njiani_qty'] }}</p>
                                @if($summary['njiani_amount'] > 0)
                                    <p class="text-xs text-gray-600 mt-1">Malipo: TSh {{ number_format($summary['njiani_amount']) }}</p>
                                @endif
                            </div>
                            @endif
                            <div class="bg-purple-50 p-3 rounded border border-purple-100 text-center">
                                <p class="text-xs text-gray-500 uppercase font-bold">Mwisho wa safari</p>
                                <p class="text-xl font-bold text-purple-700">{{ $summary['mwisho_qty'] }}</p>
                                @if($summary['mwisho_amount'] > 0)
                                    <p class="text-xs text-gray-600 mt-1">Malipo: TSh {{ number_format($summary['mwisho_amount']) }}</p>
                                @endif
                            </div>
                        </div>

                        @if(config('app.allow_enroute_drops') && $analysis['njiani']->isNotEmpty())
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Vituo vya njiani</p>
                            <div class="space-y-2 mb-4">
                                @foreach($analysis['njiani'] as $drop)
                                    <div class="flex flex-wrap justify-between gap-2 text-sm items-center bg-blue-50 p-3 rounded border border-blue-100">
                                        <div>
                                            <span class="font-semibold text-blue-700">{{ $drop->idadi_kushusha }} imeshushwa</span>
                                            at <span class="italic font-medium">{{ $drop->location }}</span>
                                            @if($drop->car_name)
                                                <span class="text-gray-400 ml-1">(Gari: {{ $drop->car_name }})</span>
                                            @endif
                                            @if($drop->amount_received > 0)
                                                <span class="text-green-700 ml-2">Malipo: TSh {{ number_format($drop->amount_received) }}</span>
                                            @endif
                                        </div>
                                        <div class="text-gray-500">
                                            {{ \Carbon\Carbon::parse($drop->created_at)->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if($analysis['mwisho']->isNotEmpty())
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Mwisho wa safari</p>
                            <div class="space-y-2">
                                @foreach($analysis['mwisho'] as $drop)
                                    <div class="flex flex-wrap justify-between gap-2 text-sm items-center bg-purple-50 p-3 rounded border border-purple-100">
                                        <div>
                                            <span class="font-semibold text-purple-800">{{ $drop->idadi_kushusha }} imeshushwa</span>
                                            mwisho - <span class="italic font-medium">{{ $drop->location }}</span>
                                            @if($drop->car_name)
                                                <span class="text-gray-400 ml-1">(Gari: {{ $drop->car_name }})</span>
                                            @endif
                                            @if($drop->amount_received > 0)
                                                <span class="text-green-700 ml-2">Malipo: TSh {{ number_format($drop->amount_received) }}</span>
                                            @endif
                                        </div>
                                        <div class="text-gray-500">
                                            {{ \Carbon\Carbon::parse($drop->created_at)->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
