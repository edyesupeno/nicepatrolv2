@extends('perusahaan.layouts.app')

@section('title', 'Kontrak & Resign')
@section('page-title', 'Kontrak & Resign')
@section('page-subtitle', 'Kelola kontrak karyawan dan pengajuan resign')

@section('content')
<!-- Tab Navigation -->
<div class="mb-6">
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
            <a href="{{ route('perusahaan.kontrak-resign.index', ['tab' => 'kontrak-habis']) }}" 
               class="tab-link {{ $activeTab === 'kontrak-habis' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                <i class="fas fa-calendar-times mr-2"></i>
                Kontrak Habis
                @if(isset($stats['expired']) && $stats['expired'] > 0)
                    <span class="ml-2 bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full font-bold">{{ $stats['expired'] }}</span>
                @endif
            </a>
            <a href="{{ route('perusahaan.kontrak-resign.index', ['tab' => 'resign']) }}" 
               class="tab-link {{ $activeTab === 'resign' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                <i class="fas fa-user-times mr-2"></i>
                Resign
                @if(isset($stats['pending']) && $stats['pending'] > 0)
                    <span class="ml-2 bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full font-bold">{{ $stats['pending'] }}</span>
                @endif
            </a>
        </nav>
    </div>
</div>

@if($activeTab === 'kontrak-habis')
    @include('perusahaan.kontrak-resign.partials.kontrak-habis')
@else
    @include('perusahaan.kontrak-resign.partials.resign')
@endif

@endsection

@push('scripts')
<script>
// Tab switching functionality
document.addEventListener('DOMContentLoaded', function() {
    // Handle tab clicks
    document.querySelectorAll('.tab-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = this.href;
        });
    });
});
</script>
@endpush