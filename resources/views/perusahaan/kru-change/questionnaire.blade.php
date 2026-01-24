@extends('perusahaan.layouts.app')

@section('title', 'Kuesioner Kru Change')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Kuesioner {{ $tipeJawaban === 'keluar' ? 'Tim Keluar' : 'Tim Masuk' }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('perusahaan.kru-change.show', $kruChange->hash_id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <form action="{{ route('perusahaan.kru-change.questionnaire.submit', [$kruChange->hash_id, $tipeJawaban]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <!-- Handover Info -->
                        <div class="alert alert-info">
                            <h5><i class="fas fa-info-circle"></i> Informasi Handover</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Area:</strong> {{ $kruChange->areaPatrol->nama }}<br>
                                    <strong>Tim {{ $tipeJawaban === 'keluar' ? 'Keluar' : 'Masuk' }}:</strong> 
                                    {{ $tipeJawaban === 'keluar' ? $kruChange->timKeluar->nama_tim : $kruChange->timMasuk->nama_tim }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Waktu Handover:</strong> {{ $kruChange->waktu_mulai_handover->format('d/m/Y H:i') }}<br>
                                    <strong>Status:</strong> {!! $kruChange->status_badge !!}
                                </div>
                            </div>
                        </div>

                        @if($kuesioners->count() > 0)
                            @foreach($kuesioners as $kuesioner)
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="card-title">{{ $kuesioner->judul }}</h5>
                                        @if($kuesioner->deskripsi)
                                            <p class="card-text text-muted">{{ $kuesioner->deskripsi }}</p>
                                        @endif
                                    </div>
                                    <div class="card-body">
                                        @foreach($kuesioner->pertanyaans as $pertanyaan)
                                            <div class="form-group">
                                                <label for="answer_{{ $pertanyaan->id }}">
                                                    {{ $pertanyaan->urutan }}. {{ $pertanyaan->pertanyaan }}
                                                    @if($pertanyaan->is_required)
                                                        <span class="text-danger">*</span>
                                                    @endif
                                                </label>

                                                @if($pertanyaan->tipe_jawaban === 'text')
                                                    <input type="text" 
                                                           name="answers[{{ $pertanyaan->id }}]" 
                                                           id="answer_{{ $pertanyaan->id }}"
                                                           class="form-control @error('answers.'.$pertanyaan->id) is-invalid @enderror"
                                                           value="{{ old('answers.'.$pertanyaan->id, $existingAnswers[$pertanyaan->id]->jawaban ?? '') }}"
                                                           {{ $pertanyaan->is_required ? 'required' : '' }}>

                                                @elseif($pertanyaan->tipe_jawaban === 'textarea')
                                                    <textarea name="answers[{{ $pertanyaan->id }}]" 
                                                              id="answer_{{ $pertanyaan->id }}"
                                                              rows="3"
                                                              class="form-control @error('answers.'.$pertanyaan->id) is-invalid @enderror"
                                                              {{ $pertanyaan->is_required ? 'required' : '' }}>{{ old('answers.'.$pertanyaan->id, $existingAnswers[$pertanyaan->id]->jawaban ?? '') }}</textarea>

                                                @elseif($pertanyaan->tipe_jawaban === 'radio')
                                                    @php
                                                        $options = json_decode($pertanyaan->pilihan_jawaban, true) ?? [];
                                                        $selectedValue = old('answers.'.$pertanyaan->id, $existingAnswers[$pertanyaan->id]->jawaban ?? '');
                                                    @endphp
                                                    @foreach($options as $option)
                                                        <div class="form-check">
                                                            <input type="radio" 
                                                                   name="answers[{{ $pertanyaan->id }}]" 
                                                                   id="answer_{{ $pertanyaan->id }}_{{ $loop->index }}"
                                                                   class="form-check-input"
                                                                   value="{{ $option }}"
                                                                   {{ $selectedValue === $option ? 'checked' : '' }}
                                                                   {{ $pertanyaan->is_required ? 'required' : '' }}>
                                                            <label class="form-check-label" for="answer_{{ $pertanyaan->id }}_{{ $loop->index }}">
                                                                {{ $option }}
                                                            </label>
                                                        </div>
                                                    @endforeach

                                                @elseif($pertanyaan->tipe_jawaban === 'checkbox')
                                                    @php
                                                        $options = json_decode($pertanyaan->pilihan_jawaban, true) ?? [];
                                                        $selectedValues = json_decode(old('answers.'.$pertanyaan->id, $existingAnswers[$pertanyaan->id]->jawaban ?? '[]'), true) ?? [];
                                                    @endphp
                                                    @foreach($options as $option)
                                                        <div class="form-check">
                                                            <input type="checkbox" 
                                                                   name="answers[{{ $pertanyaan->id }}][]" 
                                                                   id="answer_{{ $pertanyaan->id }}_{{ $loop->index }}"
                                                                   class="form-check-input"
                                                                   value="{{ $option }}"
                                                                   {{ in_array($option, $selectedValues) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="answer_{{ $pertanyaan->id }}_{{ $loop->index }}">
                                                                {{ $option }}
                                                            </label>
                                                        </div>
                                                    @endforeach

                                                @elseif($pertanyaan->tipe_jawaban === 'select')
                                                    @php
                                                        $options = json_decode($pertanyaan->pilihan_jawaban, true) ?? [];
                                                        $selectedValue = old('answers.'.$pertanyaan->id, $existingAnswers[$pertanyaan->id]->jawaban ?? '');
                                                    @endphp
                                                    <select name="answers[{{ $pertanyaan->id }}]" 
                                                            id="answer_{{ $pertanyaan->id }}"
                                                            class="form-control @error('answers.'.$pertanyaan->id) is-invalid @enderror"
                                                            {{ $pertanyaan->is_required ? 'required' : '' }}>
                                                        <option value="">Pilih jawaban...</option>
                                                        @foreach($options as $option)
                                                            <option value="{{ $option }}" {{ $selectedValue === $option ? 'selected' : '' }}>
                                                                {{ $option }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @endif

                                                @error('answers.'.$pertanyaan->id)
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror

                                                <!-- Photo Upload -->
                                                @if($pertanyaan->allow_photo)
                                                    <div class="mt-2">
                                                        <label for="photo_{{ $pertanyaan->id }}">Foto Pendukung (Opsional)</label>
                                                        <input type="file" 
                                                               name="photos[{{ $pertanyaan->id }}]" 
                                                               id="photo_{{ $pertanyaan->id }}"
                                                               class="form-control-file @error('photos.'.$pertanyaan->id) is-invalid @enderror"
                                                               accept="image/*">
                                                        @error('photos.'.$pertanyaan->id)
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                        
                                                        @if(isset($existingAnswers[$pertanyaan->id]) && $existingAnswers[$pertanyaan->id]->foto)
                                                            <div class="mt-2">
                                                                <small class="text-muted">Foto saat ini:</small><br>
                                                                <img src="{{ Storage::url($existingAnswers[$pertanyaan->id]->foto) }}" 
                                                                     alt="Foto" class="img-thumbnail" style="max-width: 200px;">
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif

                                                @if($pertanyaan->keterangan)
                                                    <small class="form-text text-muted">{{ $pertanyaan->keterangan }}</small>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                Tidak ada kuesioner yang perlu diisi untuk tim ini.
                            </div>
                        @endif
                    </div>

                    @if($kuesioners->count() > 0)
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Kuesioner
                        </button>
                        <a href="{{ route('perusahaan.kru-change.show', $kruChange->hash_id) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Handle checkbox arrays
    $('input[type="checkbox"]').change(function() {
        const name = $(this).attr('name');
        if (name && name.includes('[]')) {
            const baseName = name.replace('[]', '');
            const checkedValues = [];
            $(`input[name="${name}"]:checked`).each(function() {
                checkedValues.push($(this).val());
            });
            
            // Create hidden input to store JSON array
            $(`input[name="${baseName}"]`).remove();
            if (checkedValues.length > 0) {
                $(this).closest('.form-group').append(
                    `<input type="hidden" name="${baseName}" value='${JSON.stringify(checkedValues)}'>`
                );
            }
        }
    });
    
    // Trigger change event on page load for existing values
    $('input[type="checkbox"]:checked').trigger('change');
});
</script>
@endpush