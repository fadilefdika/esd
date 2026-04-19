<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg"> <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
            
            <div class="modal-header border-0 bg-light pt-4 px-4 pb-2">
                <div class="d-flex align-items-center">
                    <div class="bg-danger bg-opacity-10 p-3 rounded-4 me-3">
                        <i class="bi bi-exclamation-triangle-fill text-danger fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="reportModalLabel">Formulir Laporan Kendala</h5>
                        <p class="text-muted small mb-0">Pastikan data yang Anda masukkan sudah sesuai dengan kondisi riil.</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('employee.report.store') }}" method="POST" enctype="multipart/form-data" id="reportForm">
                @csrf
                <div class="modal-body px-4 py-3">
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase text-secondary tracking-wider">NPK Karyawan</label>
                            <input type="text" class="form-control form-control-lg border-0 bg-light" 
                                value="{{ auth()->user()->npk ?? 'N/A' }}" readonly style="border-radius: 12px; font-size: 0.95rem;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase text-secondary tracking-wider">Nama Lengkap</label>
                            <input type="text" class="form-control form-control-lg border-0 bg-light" 
                                value="{{ auth()->user()->fullname ?? 'Guest User' }}" readonly style="border-radius: 12px; font-size: 0.95rem;">
                        </div>
                    </div>

                    <hr class="my-4 opacity-25">

                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-uppercase text-secondary tracking-wider">Jenis Laporan</label>
                            <select class="form-select form-select-lg border-0 bg-light shadow-none" name="report_type" id="report_type" required style="border-radius: 12px; font-size: 0.95rem;">
                                <option value="" disabled selected>Pilih Jenis Kendala</option>
                                <option value="rusak">ESD Rusak</option>
                                <option value="hilang">ESD Hilang</option>
                            </select>
                        </div>

                        <div class="col-12 mb-4">
                            <label class="form-label small fw-bold text-uppercase text-secondary tracking-wider mb-3">
                                Pilih Item yang Terkendala
                            </label>
                            
                            <div class="items-selection-container" style="max-height: 300px; overflow-y: auto; padding-right: 5px;">
                                @if(isset($sets))
                                    @foreach($sets as $setNo => $items)
                                        <div class="set-group mb-3">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="badge bg-dark rounded-pill me-2">SET {{ $setNo }}</span>
                                                <hr class="flex-grow-1 my-0 opacity-10">
                                            </div>
                                            
                                            <div class="row g-2">
                                                @foreach($items as $i)
                                                    @php
                                                        $isReported = isset($i->pivot->status) && in_array(strtolower($i->pivot->status), ['hilang', 'rusak']);
                                                    @endphp
                                                    <div class="col-md-6 col-lg-4">
                                                        <div class="item-checkpoint p-2 border rounded-3 {{ $isReported ? 'bg-light opacity-75' : 'bg-white hover-shadow-sm transition-all' }}">
                                                            <div class="form-check d-flex align-items-start mb-0">
                                                                <input class="form-check-input me-2 item-checkbox mt-1" 
                                                                    type="checkbox" 
                                                                    name="selected_items[]" 
                                                                    value="{{ $i->id }}" 
                                                                    id="item_{{ $i->id }}_{{ $setNo }}"
                                                                    data-set="{{ $setNo }}"
                                                                    {{ $isReported ? 'disabled' : '' }}
                                                                    style="width: 1.2rem; height: 1.2rem; cursor: {{ $isReported ? 'not-allowed' : 'pointer' }};">
                                                                <label class="form-check-label w-100" for="item_{{ $i->id }}_{{ $setNo }}" style="cursor: {{ $isReported ? 'not-allowed' : 'pointer' }};">
                                                                    <div class="fw-bold {{ $isReported ? 'text-secondary' : 'text-dark' }}" style="font-size: 0.85rem;">{{ $i->item_name }}</div>
                                                                    <div class="text-muted" style="font-size: 0.75rem;">Size: {{ $i->pivot->size ?? '-' }}</div>
                                                                    @if($isReported)
                                                                        <div class="text-danger mt-1 fw-medium" style="font-size: 0.7rem; line-height: 1.2;">
                                                                            <i class="bi bi-info-circle me-1"></i>Dilaporkan {{ ucfirst($i->pivot->status) }}<br>
                                                                            pada {{ $i->pivot->updated_at ? \Carbon\Carbon::parse($i->pivot->updated_at)->format('d M Y, H:i') : '-' }}
                                                                        </div>
                                                                    @endif
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-4 bg-light rounded-4">
                                        <i class="bi bi-box-seam fs-2 text-muted"></i>
                                        <p class="text-muted small mt-2">Data item tidak ditemukan.</p>
                                    </div>
                                @endif
                            </div>
                            
                            {{-- Hidden input untuk menyimpan data set_no dalam format JSON agar mudah dibaca Controller --}}
                            <input type="hidden" name="items_metadata" id="items_metadata">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-uppercase text-secondary tracking-wider">Deskripsi Kerusakan</label>
                        <textarea class="form-control border-0 bg-light shadow-none" name="details" rows="3" 
                                placeholder="Contoh: Strap pada baju ESD sobek di bagian bahu kanan..." required 
                                style="border-radius: 12px; font-size: 0.95rem; resize: none;"></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-uppercase text-secondary tracking-wider">
                            Unggah Bukti Foto <span class="text-lowercase fw-normal">(Maks. 5 foto)</span>
                        </label>
                        
                        <div class="upload-wrapper">
                            <div class="p-4 border-2 border-dashed rounded-4 bg-light text-center border-secondary border-opacity-25 position-relative mb-3" 
                                id="drop-zone" style="transition: all 0.3s ease;">
                                <i class="bi bi-images fs-1 text-secondary opacity-50"></i>
                                <p class="mb-0 small text-muted mt-2">Tarik foto ke sini atau klik untuk memilih</p>
                                <input type="file" class="position-absolute opacity-0 w-100 h-100 top-0 start-0" 
                                    id="evidence_input" accept="image/*" multiple style="cursor: pointer;">
                            </div>

                            <div id="preview-grid" class="row g-2">
                                </div>

                            <div id="max-error" class="alert alert-danger d-none mt-2 small p-2" style="border-radius: 10px;">
                                <i class="bi bi-exclamation-circle-fill me-2"></i> Maksimal hanya boleh 5 foto.
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer border-0 px-4 pb-4 bg-light bg-opacity-50">
                    <button type="button" class="btn btn-link text-secondary text-decoration-none fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger px-5 py-2 shadow-sm d-flex align-items-center" style="border-radius: 12px; font-weight: 600;">
                        <i class="bi bi-send-fill me-2"></i> Kirim Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Inisialisasi Elemen
    const reportForm = document.getElementById('reportForm');
    const reportType = document.getElementById('report_type');
    const chronologySection = document.getElementById('chronology_section');
    const chronologyInput = document.getElementById('chronology_input');
    const input = document.getElementById('evidence_input');
    const previewGrid = document.getElementById('preview-grid');
    const errorMsg = document.getElementById('max-error');
    const metadataInput = document.getElementById('items_metadata');
    const checkboxes = document.querySelectorAll('.item-checkbox');

    let selectedFiles = []; // Penampung file

    // 2. Toggle Kronologi (Jika Masih Dipakai)
    if (reportType) {
        reportType.addEventListener('change', function() {
            if (this.value === 'hilang') {
                chronologySection?.classList.remove('d-none');
                chronologyInput?.setAttribute('required', 'required');
            } else {
                chronologySection?.classList.add('d-none');
                chronologyInput?.removeAttribute('required');
            }
        });
    }

    // 3. Update Metadata Checklist
    function updateMetadata() {
        const selected = [];
        checkboxes.forEach(cb => {
            if (cb.checked) {
                selected.push({
                    id: cb.value,
                    set_no: cb.getAttribute('data-set')
                });
            }
        });
        if (metadataInput) metadataInput.value = JSON.stringify(selected);
    }

    checkboxes.forEach(cb => cb.addEventListener('change', updateMetadata));

    // 4. Handling Upload & Preview
    if (input) {
        input.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            
            if (selectedFiles.length + files.length > 5) {
                errorMsg.classList.remove('d-none');
                return;
            }
            
            errorMsg.classList.add('d-none');
            files.forEach(file => {
                if (!selectedFiles.some(f => f.name === file.name)) {
                    selectedFiles.push(file);
                    renderPreview(file);
                }
            });
            input.value = ""; // Reset agar bisa pilih file yang sama
        });
    }

    function renderPreview(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'col-4 col-md-2 position-relative preview-item mb-2';
            div.innerHTML = `
                <div class="ratio ratio-1x1 rounded-3 overflow-hidden border shadow-sm">
                    <img src="${e.target.result}" class="object-fit-cover" alt="preview">
                </div>
                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle p-0 m-1 shadow" 
                        style="width: 20px; height: 20px;" 
                        onclick="removeFile('${file.name}', this)">
                    <i class="bi bi-x" style="font-size: 14px;"></i>
                </button>
            `;
            previewGrid.appendChild(div);
        }
        reader.readAsDataURL(file);
    }

    // Fungsi Hapus (Global agar bisa diakses onclick)
    window.removeFile = function(fileName, element) {
        selectedFiles = selectedFiles.filter(f => f.name !== fileName);
        element.closest('.preview-item').remove();
        errorMsg.classList.add('d-none');
    }

    // 5. Intersepsi Submit (Kirim via AJAX)
    reportForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validasi Checklist
        const selectedItems = JSON.parse(metadataInput.value || "[]");
        if (selectedItems.length === 0) {
            Swal.fire('Peringatan', 'Pilih minimal satu item yang terkendala.', 'warning');
            return;
        }

        if (selectedFiles.length === 0) {
            Swal.fire('Peringatan', 'Mohon unggah minimal 1 foto bukti.', 'warning');
            return;
        }

        const formData = new FormData(this);
        formData.delete('evidence'); // Hapus bawaan browser
        selectedFiles.forEach((file, index) => {
            formData.append(`evidence[${index}]`, file);
        });

        // Kirim Data
        submitData(formData);
    });

    function submitData(formData) {
        Swal.fire({
            title: 'Mengirim Laporan...',
            didOpen: () => { Swal.showLoading() },
            allowOutsideClick: false
        });

        fetch("{{ route('employee.report.store') }}", {
            method: "POST",
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
            } else {
                Swal.fire('Gagal', data.message || 'Terjadi kesalahan sistem', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Gagal terhubung ke server.', 'error');
        });
    }
});
</script>