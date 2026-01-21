@extends('perusahaan.layouts.app')

@section('title', 'Kuesioner Tamu')
@section('page-title', 'Kuesioner Tamu')
@section('page-subtitle', 'Isi kuesioner untuk tamu')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl shadow-lg mb-6 overflow-hidden">
        <div class="px-6 py-8 text-white">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-4">
                    <i class="fas fa-clipboard-list text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold" id="questionnaire-title">Kuesioner Tamu</h1>
                    <div class="flex items-center text-blue-100 text-sm mt-1">
                        <i class="fas fa-building mr-2"></i>
                        <span id="project-info">Loading...</span>
                        <i class="fas fa-map-marker-alt ml-4 mr-2"></i>
                        <span id="area-info">Loading...</span>
                    </div>
                </div>
            </div>
            <p class="text-blue-100" id="questionnaire-description">
                Isi kuesioner
            </p>
        </div>
    </div>

    <!-- Guest Info Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6 p-6">
        <div class="flex items-center">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                <i class="fas fa-user text-blue-600 text-2xl"></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900" id="guest-name">Loading...</h3>
                <p class="text-gray-600" id="guest-company">Loading...</p>
                <p class="text-sm text-gray-500" id="guest-details">Loading...</p>
            </div>
        </div>
    </div>

    <!-- Loading State -->
    <div id="questionnaire-loading" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
        <div class="flex flex-col items-center">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-spinner fa-spin text-blue-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Memuat Kuesioner</h3>
            <p class="text-gray-600">Mohon tunggu sebentar...</p>
        </div>
    </div>

    <!-- Error State -->
    <div id="questionnaire-error" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center" style="display: none;">
        <div class="flex flex-col items-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Kuesioner Tidak Tersedia</h3>
            <p class="text-gray-600 mb-6">Kuesioner tidak ditemukan untuk area ini</p>
            <a href="{{ route('perusahaan.buku-tamu.index') }}" 
               class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Questionnaire Form -->
    <div id="questionnaire-content" style="display: none;">
        <form id="questionnaire-form" class="space-y-6">
            <div id="questionnaire-questions">
                <!-- Questions will be loaded here -->
            </div>

            <!-- Action Buttons -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span id="required-info">0 pertanyaan wajib diisi</span>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('perusahaan.buku-tamu.index') }}" 
                           class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                            <i class="fas fa-times mr-2"></i>Batal
                        </a>
                        <button type="button" 
                                onclick="saveQuestionnaire()" 
                                class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition shadow-lg">
                            <i class="fas fa-paper-plane mr-2"></i>Kirim Jawaban
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Get URL parameters
const urlParams = new URLSearchParams(window.location.search);
const guestHashId = urlParams.get('guest');
const projectId = urlParams.get('project');
const areaId = urlParams.get('area');

let currentQuestionnaire = null;

// Load questionnaire on page load
document.addEventListener('DOMContentLoaded', function() {
    if (!guestHashId || !projectId || !areaId) {
        showError('Parameter tidak lengkap');
        return;
    }
    
    loadGuestInfo();
    loadQuestionnaire();
});

async function loadGuestInfo() {
    try {
        // Get guest info from the table data or make an API call
        // For now, we'll extract from URL or make a simple call
        const response = await fetch(`/perusahaan/buku-tamu/guest-info?guest=${guestHashId}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            if (data.success) {
                document.getElementById('guest-name').textContent = data.data.nama_tamu;
                document.getElementById('guest-company').textContent = data.data.perusahaan_tamu || '-';
                document.getElementById('guest-details').textContent = `${data.data.keperluan} • Check-in: ${data.data.check_in_formatted}`;
                document.getElementById('project-info').textContent = data.data.project?.nama || 'Project';
                document.getElementById('area-info').textContent = data.data.area?.nama || 'Area';
            }
        }
    } catch (error) {
        console.error('Error loading guest info:', error);
        // Use fallback data from URL or session storage
        document.getElementById('guest-name').textContent = 'Tamu';
        document.getElementById('project-info').textContent = 'Project';
        document.getElementById('area-info').textContent = 'Area';
    }
}

async function loadQuestionnaire() {
    try {
        const response = await fetch(`/perusahaan/buku-tamu/kuesioner-by-area?project_id=${projectId}&area_id=${areaId}&guest_id=${guestHashId}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });
        
        const data = await response.json();
        
        if (data.success && data.data) {
            currentQuestionnaire = data.data;
            renderQuestionnaire(data.data);
        } else {
            showError('Kuesioner tidak ditemukan untuk area ini');
        }
    } catch (error) {
        console.error('Error loading questionnaire:', error);
        showError('Terjadi kesalahan saat memuat kuesioner');
    }
}

function renderQuestionnaire(questionnaire) {
    // Hide loading, show content
    document.getElementById('questionnaire-loading').style.display = 'none';
    document.getElementById('questionnaire-content').style.display = 'block';
    
    // Update header
    document.getElementById('questionnaire-title').textContent = questionnaire.judul;
    document.getElementById('questionnaire-description').textContent = questionnaire.deskripsi || 'Silakan jawab pertanyaan berikut dengan lengkap dan jujur.';
    
    // Check if there are existing answers
    const hasExistingAnswers = questionnaire.existing_answers && Object.keys(questionnaire.existing_answers).length > 0;
    
    // Add status indicator if form was previously filled
    if (hasExistingAnswers) {
        const statusHtml = `
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-check text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-green-800 font-medium">Kuesioner Sudah Pernah Diisi</p>
                        <p class="text-green-600 text-sm">Form di bawah menampilkan jawaban yang sudah tersimpan. Anda dapat mengubah jawaban jika diperlukan.</p>
                    </div>
                </div>
            </div>
        `;
        
        const questionsContainer = document.getElementById('questionnaire-questions');
        questionsContainer.insertAdjacentHTML('beforebegin', statusHtml);
    }
    
    // Render questions
    const questionsContainer = document.getElementById('questionnaire-questions');
    questionsContainer.innerHTML = '';
    
    let requiredCount = 0;
    let filledRequiredCount = 0;
    
    if (questionnaire.pertanyaans && questionnaire.pertanyaans.length > 0) {
        questionnaire.pertanyaans.forEach((pertanyaan, index) => {
            if (pertanyaan.is_required) {
                requiredCount++;
                // Check if this required question has an answer
                if (questionnaire.existing_answers && questionnaire.existing_answers[pertanyaan.id]) {
                    filledRequiredCount++;
                }
            }
            const questionDiv = createQuestionElement(pertanyaan, index);
            questionsContainer.appendChild(questionDiv);
        });
    }
    
    // Update required info with progress
    const requiredInfoText = hasExistingAnswers ? 
        `${filledRequiredCount}/${requiredCount} pertanyaan wajib sudah diisi` :
        `${requiredCount} pertanyaan wajib diisi`;
    
    document.getElementById('required-info').innerHTML = `
        <i class="fas fa-info-circle mr-2"></i>
        <span>${requiredInfoText}</span>
    `;
    
    // Update button text if form was previously filled
    const submitButton = document.querySelector('button[onclick="saveQuestionnaire()"]');
    if (submitButton && hasExistingAnswers) {
        submitButton.innerHTML = '<i class="fas fa-sync-alt mr-2"></i>Perbarui Jawaban';
    }
}

function createQuestionElement(pertanyaan, index) {
    const div = document.createElement('div');
    div.className = 'bg-white rounded-xl shadow-sm border border-gray-100 p-6';
    
    let inputHtml = '';
    const questionId = `question_${pertanyaan.id}`;
    
    // Get existing answer for this question
    const existingAnswer = currentQuestionnaire.existing_answers ? currentQuestionnaire.existing_answers[pertanyaan.id] : null;
    
    // Create input based on question type - matching the preview design
    switch (pertanyaan.tipe_jawaban) {
        case 'text':
            inputHtml = `
                <input type="text" 
                       id="${questionId}" 
                       name="answers[${pertanyaan.id}]"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Tulis jawaban Anda..."
                       value="${existingAnswer || ''}"
                       ${pertanyaan.is_required ? 'required' : ''}>
            `;
            break;
            
        case 'textarea':
            inputHtml = `
                <textarea id="${questionId}" 
                          name="answers[${pertanyaan.id}]"
                          rows="4"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                          placeholder="Tulis jawaban Anda..."
                          ${pertanyaan.is_required ? 'required' : ''}>${existingAnswer || ''}</textarea>
            `;
            break;
            
        case 'pilihan':
            if (pertanyaan.opsi_jawaban && pertanyaan.opsi_jawaban.length > 0) {
                inputHtml = `
                    <div class="space-y-3">
                        ${pertanyaan.opsi_jawaban.map((option, optIndex) => `
                            <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                <input type="radio" 
                                       name="answers[${pertanyaan.id}]" 
                                       value="${option}"
                                       class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                       ${existingAnswer === option ? 'checked' : ''}
                                       ${pertanyaan.is_required ? 'required' : ''}>
                                <span class="ml-3 text-gray-700">${option}</span>
                            </label>
                        `).join('')}
                    </div>
                `;
            }
            break;
            
        case 'checkbox':
            if (pertanyaan.opsi_jawaban && pertanyaan.opsi_jawaban.length > 0) {
                // Handle existing answers for checkboxes (comma-separated values)
                const existingCheckboxAnswers = existingAnswer ? existingAnswer.split(', ') : [];
                
                inputHtml = `
                    <div class="space-y-3">
                        ${pertanyaan.opsi_jawaban.map((option, optIndex) => `
                            <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                <input type="checkbox" 
                                       name="answers[${pertanyaan.id}][]" 
                                       value="${option}"
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                       ${existingCheckboxAnswers.includes(option) ? 'checked' : ''}>
                                <span class="ml-3 text-gray-700">${option}</span>
                            </label>
                        `).join('')}
                    </div>
                `;
            }
            break;
            
        case 'select':
            if (pertanyaan.opsi_jawaban && pertanyaan.opsi_jawaban.length > 0) {
                inputHtml = `
                    <select id="${questionId}" 
                            name="answers[${pertanyaan.id}]"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white"
                            ${pertanyaan.is_required ? 'required' : ''}>
                        <option value="">Pilih jawaban</option>
                        ${pertanyaan.opsi_jawaban.map(option => `
                            <option value="${option}" ${existingAnswer === option ? 'selected' : ''}>${option}</option>
                        `).join('')}
                    </select>
                `;
            }
            break;
            
        default:
            inputHtml = `
                <input type="text" 
                       id="${questionId}" 
                       name="answers[${pertanyaan.id}]"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Tulis jawaban Anda..."
                       value="${existingAnswer || ''}"
                       ${pertanyaan.is_required ? 'required' : ''}>
            `;
    }
    
    div.innerHTML = `
        <div class="flex items-start mb-4">
            <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-semibold mr-4 mt-1 flex-shrink-0">
                ${index + 1}
            </div>
            <div class="flex-1">
                <label class="block text-lg font-medium text-gray-900 mb-4">
                    ${pertanyaan.pertanyaan}
                    ${pertanyaan.is_required ? '<span class="text-red-500 ml-1">*</span>' : ''}
                </label>
                ${pertanyaan.is_required ? '<p class="text-sm text-red-600 mb-3">Wajib diisi</p>' : ''}
                <div>
                    ${inputHtml}
                </div>
            </div>
        </div>
    `;
    
    return div;
}

function showError(message) {
    document.getElementById('questionnaire-loading').style.display = 'none';
    document.getElementById('questionnaire-content').style.display = 'none';
    document.getElementById('questionnaire-error').style.display = 'block';
    
    const errorElement = document.querySelector('#questionnaire-error p');
    if (errorElement) {
        errorElement.textContent = message;
    }
}

async function saveQuestionnaire() {
    const form = document.getElementById('questionnaire-form');
    const formData = new FormData(form);
    
    // Convert FormData to object for easier handling
    const answers = {};
    for (let [key, value] of formData.entries()) {
        if (key.startsWith('answers[')) {
            const questionId = key.match(/answers\[(\d+)\]/)[1];
            if (answers[questionId]) {
                // Handle multiple values (checkboxes)
                if (Array.isArray(answers[questionId])) {
                    answers[questionId].push(value);
                } else {
                    answers[questionId] = [answers[questionId], value];
                }
            } else {
                answers[questionId] = value;
            }
        }
    }
    
    // Validate required fields
    if (currentQuestionnaire && currentQuestionnaire.pertanyaans) {
        const requiredQuestions = currentQuestionnaire.pertanyaans.filter(q => q.is_required);
        const missingAnswers = requiredQuestions.filter(q => !answers[q.id] || answers[q.id] === '');
        
        if (missingAnswers.length > 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Pertanyaan Wajib Belum Dijawab',
                text: `Masih ada ${missingAnswers.length} pertanyaan wajib yang belum dijawab`,
                confirmButtonColor: '#3B82F6'
            });
            return;
        }
    }
    
    // Show loading
    Swal.fire({
        title: 'Menyimpan Jawaban',
        text: 'Mohon tunggu sebentar...',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    try {
        const response = await fetch(`/perusahaan/buku-tamu/${guestHashId}/questionnaire`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                kuesioner_answers: answers
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            const hasExistingAnswers = currentQuestionnaire.existing_answers && Object.keys(currentQuestionnaire.existing_answers).length > 0;
            const successMessage = hasExistingAnswers ? 'Jawaban kuesioner berhasil diperbarui' : 'Jawaban kuesioner telah disimpan';
            
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: successMessage,
                confirmButtonColor: '#3B82F6'
            }).then(() => {
                window.location.href = '{{ route("perusahaan.buku-tamu.index") }}';
            });
        } else {
            throw new Error(data.message || 'Gagal menyimpan jawaban');
        }
    } catch (error) {
        console.error('Error saving questionnaire:', error);
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: error.message || 'Terjadi kesalahan saat menyimpan jawaban',
            confirmButtonColor: '#3B82F6'
        });
    }
}
</script>
@endpush