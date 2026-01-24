@extends('perusahaan.layouts.app')

@section('title', 'Kru Change')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Kru Change</h3>
            <a href="{{ route('perusahaan.kru-change.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                <i class="fas fa-plus mr-2"></i>Tambah Kru Change
            </a>
        </div>

        <div class="p-6">
            <!-- Filter Form -->
            <form method="GET" class="mb-6">
                <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                    <div>
                        <input type="text" name="search" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="Cari area, tim..." value="{{ request('search') }}">
                    </div>
                    <div>
                        <select name="project_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            <option value="">Semua Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="area_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            <option value="">Semua Area</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>
                                    {{ $area->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <input type="date" name="tanggal" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" value="{{ request('tanggal') }}">
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Area</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tim Keluar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tim Masuk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Handover</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approval</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($kruChanges as $kruChange)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $loop->iteration + ($kruChanges->currentPage() - 1) * $kruChanges->perPage() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $kruChange->project->nama }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $kruChange->areaPatrol->nama }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $kruChange->timKeluar->nama_tim }}</div>
                                    <div class="text-sm text-gray-500">{{ $kruChange->timKeluar->jenis_regu }}</div>
                                    @if($kruChange->petugas_keluar_ids && count($kruChange->petugas_keluar_ids) > 0)
                                        <div class="text-xs text-blue-600">{{ count($kruChange->petugas_keluar_ids) }} petugas</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $kruChange->timMasuk->nama_tim }}</div>
                                    <div class="text-sm text-gray-500">{{ $kruChange->timMasuk->jenis_regu }}</div>
                                    @if($kruChange->petugas_masuk_ids && count($kruChange->petugas_masuk_ids) > 0)
                                        <div class="text-xs text-blue-600">{{ count($kruChange->petugas_masuk_ids) }} petugas</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $kruChange->waktu_mulai_handover->format('d/m/Y H:i') }}</div>
                                    @if($kruChange->waktu_selesai_handover)
                                        <div class="text-sm text-gray-500">Selesai: {{ $kruChange->waktu_selesai_handover->format('d/m/Y H:i') }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {!! $kruChange->status_badge !!}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col space-y-1">
                                        <div class="flex items-center text-xs">
                                            <i class="fas fa-{{ $kruChange->approved_keluar ? 'check text-green-500' : 'times text-red-500' }} mr-1"></i>
                                            <span>Keluar</span>
                                        </div>
                                        <div class="flex items-center text-xs">
                                            <i class="fas fa-{{ $kruChange->approved_masuk ? 'check text-green-500' : 'times text-red-500' }} mr-1"></i>
                                            <span>Masuk</span>
                                        </div>
                                        <div class="flex items-center text-xs">
                                            <i class="fas fa-{{ $kruChange->approved_supervisor ? 'check text-green-500' : 'times text-red-500' }} mr-1"></i>
                                            <span>Supervisor</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('perusahaan.kru-change.show', $kruChange->hash_id) }}" 
                                           class="text-blue-600 hover:text-blue-900" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($kruChange->status === 'pending')
                                            <a href="{{ route('perusahaan.kru-change.edit', $kruChange->hash_id) }}" 
                                               class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        @if($kruChange->status !== 'completed')
                                            <form action="{{ route('perusahaan.kru-change.destroy', $kruChange->hash_id) }}" 
                                                  method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" 
                                                        onclick="return confirm('Yakin ingin menghapus?')" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                    Tidak ada data kru change
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $kruChanges->links() }}
            </div>
        </div>
    </div>
</div>
@endsection