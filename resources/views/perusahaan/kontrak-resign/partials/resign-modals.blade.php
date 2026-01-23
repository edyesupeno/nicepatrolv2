<!-- Modal Approve -->
<div id="approveModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <form id="approveForm">
            @csrf
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-check text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Setujui Pengajuan Resign</h3>
                        <p class="text-sm text-gray-600">Apakah Anda yakin ingin menyetujui pengajuan resign ini?</p>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                    <textarea 
                        name="catatan_approval" 
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                        placeholder="Tambahkan catatan persetujuan..."
                    ></textarea>
                </div>

                <div class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_blacklist" value="1" class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Masukkan ke blacklist</span>
                    </label>
                </div>

                <div id="blacklistReasonDiv" class="mb-4 hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Blacklist <span class="text-red-500">*</span></label>
                    <textarea 
                        name="blacklist_reason" 
                        rows="2"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                        placeholder="Jelaskan alasan blacklist..."
                    ></textarea>
                </div>

                <div class="flex space-x-3">
                    <button type="button" onclick="closeModal('approveModal')" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition">
                        Setujui
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Reject -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <form id="rejectForm">
            @csrf
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-times text-red-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Tolak Pengajuan Resign</h3>
                        <p class="text-sm text-gray-600">Berikan alasan penolakan pengajuan resign</p>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                    <textarea 
                        name="catatan_approval" 
                        rows="3"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                        placeholder="Jelaskan alasan penolakan..."
                    ></textarea>
                </div>

                <div class="flex space-x-3">
                    <button type="button" onclick="closeModal('rejectModal')" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition">
                        Tolak
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>