@extends('layout.master')

@push('plugin-styles')
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        .detail-card {
            transition: all 0.3s ease;
        }
        .detail-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .info-label {
            font-weight: 600;
            color: #4b5563;
        }
        .info-value {
            color: #1f2937;
        }
    </style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8 animate__animated animate__fadeIn">
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Maelezo ya Mzigo: <span class="text-blue-600">RG-{{ $parcel->hashtag ?: $parcel->id }}</span></h2>
        <a href="{{ route('list_ya_mizigo.index') }}" class="btn btn-secondary flex items-center gap-2">
            <i class="icon-arrow-left"></i> Rudi kwenye List
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Basic Info Card -->
        <div class="bg-white rounded-xl shadow-sm p-6 detail-card border border-gray-100">
            <div class="flex items-center gap-3 mb-4 border-b pb-2">
                <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                    <i class="icon-package text-xl"></i>
                </div>
                <h3 class="text-lg font-bold">Taarifa za Msingi</h3>
            </div>
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="info-label">Jina la Mzigo:</span>
                    <span class="info-value">{{ $parcel->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="info-label">Mteja:</span>
                    <span class="info-value">{{ $parcel->mteja }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="info-label">Mpokeaji (Ofisi):</span>
                    <span class="info-value">{{ $parcel->mpokeaji }}</span>
                </div>
            </div>
        </div>

        <!-- Quantity & Pricing Card -->
        <div class="bg-white rounded-xl shadow-sm p-6 detail-card border border-gray-100">
            <div class="flex items-center gap-3 mb-4 border-b pb-2">
                <div class="p-2 bg-green-50 rounded-lg text-green-600">
                    <i class="icon-dollar-sign text-xl"></i>
                </div>
                <h3 class="text-lg font-bold">Idadi na Gharama</h3>
            </div>
            <div class="space-y-4">
                @php $qty = \App\Support\ParcelQuantitySummary::breakdown($parcel); @endphp
                <div class="flex justify-between">
                    <span class="info-label">Idadi Jumla:</span>
                    <span class="info-value font-bold text-blue-600">{{ $qty['total'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="info-label">Bado Stoo (haijapakiwa):</span>
                    <span class="info-value font-bold {{ $qty['stoo'] > 0 ? 'text-orange-600' : 'text-gray-500' }}">{{ $qty['stoo'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="info-label">Kwenye Gari:</span>
                    <span class="info-value font-bold text-indigo-600">{{ $qty['on_car'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="info-label">Imeshushwa:</span>
                    <span class="info-value font-bold text-red-600">{{ $qty['dropped'] }}</span>
                </div>
                @if($qty['is_partially_loaded'])
                    <div class="bg-orange-50 p-3 rounded-lg border border-orange-100 text-sm text-orange-800">
                        Sehemu ya mzigo bado iko stoo - hujapakia yote. Unaweza kupakia zaidi baadae.
                    </div>
                @endif
                @php
                    $jumlaVal    = (float) str_replace(',', '', $parcel->jumla ?? 0);
                    $receivedVal = (float) str_replace(',', '', $parcel->ela_iliyopokelewa ?? 0);
                    $deniVal     = max(0, $jumlaVal - $receivedVal);
                    $fullyPaid   = $deniVal < 0.01;
                @endphp
                <div class="flex justify-between pt-2 border-t">
                    <span class="info-label">Jumla Kuu:</span>
                    <span class="info-value font-bold text-lg">TSh {{ number_format($jumlaVal) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="info-label">Hela Iliyopokelewa:</span>
                    <span class="info-value text-green-600 font-bold">TSh {{ number_format($receivedVal) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="info-label">Baki ya Deni:</span>
                    @if($fullyPaid)
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">✓ Malipo Kamili</span>
                    @else
                        <span class="info-value text-red-600 font-bold">TSh {{ number_format($deniVal) }}</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Route Info Card -->
        <div class="bg-white rounded-xl shadow-sm p-6 detail-card border border-gray-100">
            <div class="flex items-center gap-3 mb-4 border-b pb-2">
                <div class="p-2 bg-purple-50 rounded-lg text-purple-600">
                    <i class="icon-map-pin text-xl"></i>
                </div>
                <h3 class="text-lg font-bold">Route ya Mzigo</h3>
            </div>
            <div class="space-y-4">
                <div class="flex items-center gap-4">
                    <div class="flex flex-col items-center">
                        <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                        <div class="w-0.5 h-12 bg-gray-200"></div>
                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                    </div>
                    <div class="flex flex-col justify-between h-20">
                        <div>
                            <p class="text-xs text-gray-500 uppercase">Kutoka</p>
                            <p class="font-bold">{{ $parcel->mzigo_unapotoka }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase">Kwenda</p>
                            <p class="font-bold">{{ $parcel->mzigo_unapokwenda }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status & Loading Info Card -->
        <div class="bg-white rounded-xl shadow-sm p-6 detail-card border border-gray-100">
            <div class="flex items-center gap-3 mb-4 border-b pb-2">
                <div class="p-2 bg-yellow-50 rounded-lg text-yellow-600">
                    <i class="icon-truck text-xl"></i>
                </div>
                <h3 class="text-lg font-bold">Hali ya Usafirishaji</h3>
            </div>
            <div class="space-y-4">
                @if(!$hasBeenPackedOrShipped)
                    <div class="bg-orange-50 p-4 rounded-lg border border-orange-100 text-center">
                        <p class="text-orange-700 font-bold animate__animated animate__pulse animate__infinite">
                            Bado Haijapakiwa
                        </p>
                    </div>
                @else
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
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="info-label">Hali:</span>
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </div>
                        @if((int) $parcel->idadi_kupakia > 0)
                            <div class="flex justify-between">
                                <span class="info-label">Idadi kwenye gari:</span>
                                <span class="info-value font-bold text-blue-600">{{ $parcel->idadi_kupakia }}</span>
                            </div>
                        @endif
                        @if((int) $parcel->idadi_shusha > 0)
                            <div class="flex justify-between">
                                <span class="info-label">Idadi iliyoshushwa:</span>
                                <span class="info-value font-bold text-red-600">{{ $parcel->idadi_shusha }}</span>
                            </div>
                        @endif
                        @foreach($packingAssignments as $assignment)
                            <div class="bg-blue-50 p-3 rounded-lg border border-blue-100">
                                <div class="flex justify-between">
                                    <span class="info-label">Gari:</span>
                                    <span class="info-value font-bold text-blue-600">{{ $assignment->car ? $assignment->car->carNumber : 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between mt-2">
                                    <span class="info-label">Idadi Iliyowekwa:</span>
                                    <span class="info-value font-bold">{{ $assignment->idadi }}</span>
                                </div>
                            </div>
                        @endforeach
                        @if($packingAssignments->isEmpty() && $parcel->car_id && $parcel->car)
                            <div class="bg-blue-50 p-3 rounded-lg border border-blue-100">
                                <div class="flex justify-between">
                                    <span class="info-label">Gari:</span>
                                    <span class="info-value font-bold text-blue-600">{{ $parcel->car->carNumber ?? 'N/A' }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Hali ya Kushusha Card -->
        @if($parcel->idadi_shusha > 0 || (int) $parcel->idadi_kupakia > 0 || (int) $parcel->idadi_stoo > 0)
        @php $qty = \App\Support\ParcelQuantitySummary::breakdown($parcel); @endphp
        <div class="bg-white rounded-xl shadow-sm p-6 detail-card border border-gray-100 md:col-span-2">
            <div class="flex items-center gap-3 mb-4 border-b pb-2">
                <div class="p-2 bg-red-50 rounded-lg text-red-600">
                    <i class="icon-check-circle text-xl"></i>
                </div>
                <h3 class="text-lg font-bold">Uchambuzi wa Mzigo</h3>
            </div>
            <div class="space-y-4">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 text-center">
                        <p class="text-xs text-gray-500 uppercase font-bold">Jumla</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $qty['total'] }}</p>
                    </div>
                    <div class="bg-orange-50 p-4 rounded-lg border border-orange-100 text-center">
                        <p class="text-xs text-gray-500 uppercase font-bold">Bado Stoo</p>
                        <p class="text-2xl font-bold text-orange-600">{{ $qty['stoo'] }}</p>
                    </div>
                    <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-100 text-center">
                        <p class="text-xs text-gray-500 uppercase font-bold">Kwenye Gari</p>
                        <p class="text-2xl font-bold text-indigo-600">{{ $qty['on_car'] }}</p>
                    </div>
                    <div class="bg-red-50 p-4 rounded-lg border border-red-100 text-center">
                        <p class="text-xs text-gray-500 uppercase font-bold">Imeshushwa</p>
                        <p class="text-2xl font-bold text-red-600">{{ $qty['dropped'] }}</p>
                    </div>
                </div>

                @if($qty['is_partially_loaded'])
                    <div class="bg-orange-50 p-3 rounded-lg border border-orange-100 text-sm text-orange-900">
                        <strong>Kumbuka:</strong> Hujaondoa mzigo wote kutoka stoo. Idadi <strong>{{ $qty['stoo'] }}</strong> bado iko stoo na inaweza kupakiwa kwenye safari nyingine.
                    </div>
                @endif

                @if($parcel->idadi_shusha > 0)
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 border-t">
                    <div class="bg-red-50 p-4 rounded-lg border border-red-100 text-center">
                        <p class="text-xs text-gray-500 uppercase font-bold">Jumla Iliyoshushwa</p>
                        <p class="text-2xl font-bold text-red-600">{{ $parcel->idadi_shusha }}</p>
                    </div>
                @if(config('app.allow_enroute_drops'))
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 text-center">
                        <p class="text-xs text-gray-500 uppercase font-bold">Njiani (vituo)</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $dropSummary['njiani_qty'] }}</p>
                        @if($dropSummary['njiani_amount'] > 0)
                            <p class="text-xs text-gray-600 mt-1">Malipo: TSh {{ number_format($dropSummary['njiani_amount']) }}</p>
                        @endif
                    </div>
                @endif
                    <div class="bg-purple-50 p-4 rounded-lg border border-purple-100 text-center">
                        <p class="text-xs text-gray-500 uppercase font-bold">Mwisho wa safari</p>
                        <p class="text-2xl font-bold text-purple-700">{{ $dropSummary['mwisho_qty'] }}</p>
                        @if($dropSummary['mwisho_amount'] > 0)
                            <p class="text-xs text-gray-600 mt-1">Malipo: TSh {{ number_format($dropSummary['mwisho_amount']) }}</p>
                        @endif
                    </div>
                </div>

                @if(config('app.allow_enroute_drops') && $dropDetailsNjiani->isNotEmpty())
                    <div class="mt-2 space-y-3">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Vituo vya njiani vilivyoshushwa</p>
                        @foreach($dropDetailsNjiani as $detail)
                            <div class="bg-blue-50 p-3 rounded-lg border border-blue-100">
                                <div class="flex justify-between">
                                    <span class="info-label">Kituo:</span>
                                    <span class="info-value font-bold text-blue-700">{{ $detail->location }}</span>
                                </div>
                                <div class="flex justify-between mt-1">
                                    <span class="info-label">Idadi:</span>
                                    <span class="info-value font-bold">{{ $detail->idadi_kushusha }}</span>
                                </div>
                                @if($detail->amount_received > 0)
                                    <div class="flex justify-between mt-1">
                                        <span class="info-label">Malipo:</span>
                                        <span class="info-value font-bold text-green-700">TSh {{ number_format($detail->amount_received) }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between mt-1 text-xs">
                                    <span class="info-label">Tarehe:</span>
                                    <span class="info-value">{{ \Carbon\Carbon::parse($detail->created_at)->format('Y-m-d H:i') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($dropDetailsMwisho->isNotEmpty())
                    <div class="mt-4 space-y-3">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Mwisho wa safari - mizigo iliyoshushwa</p>
                        @foreach($dropDetailsMwisho as $detail)
                            <div class="bg-purple-50 p-3 rounded-lg border border-purple-100">
                                <div class="flex justify-between">
                                    <span class="info-label">Marudio:</span>
                                    <span class="info-value font-bold text-purple-800">{{ $detail->location }}</span>
                                </div>
                                <div class="flex justify-between mt-1">
                                    <span class="info-label">Idadi:</span>
                                    <span class="info-value font-bold">{{ $detail->idadi_kushusha }}</span>
                                </div>
                                @if($detail->amount_received > 0)
                                    <div class="flex justify-between mt-1">
                                        <span class="info-label">Malipo:</span>
                                        <span class="info-value font-bold text-green-700">TSh {{ number_format($detail->amount_received) }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between mt-1 text-xs">
                                    <span class="info-label">Tarehe:</span>
                                    <span class="info-value">{{ \Carbon\Carbon::parse($detail->created_at)->format('Y-m-d H:i') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($dropDetailsNjiani->isEmpty() && $dropDetailsMwisho->isEmpty() && $dropInfo)
                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-100 mt-3">
                        <div class="flex justify-between">
                            <span class="info-label">Sehemu iliyoshushwa:</span>
                            <span class="info-value">{{ $dropInfo->receipt ?: 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between mt-2">
                            <span class="info-label">Tarehe:</span>
                            <span class="info-value">{{ $dropInfo->created_at ? $dropInfo->created_at->format('Y-m-d H:i:s') : 'N/A' }}</span>
                        </div>
                    </div>
                @endif
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
