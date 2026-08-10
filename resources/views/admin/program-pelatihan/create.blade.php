@extends('layouts.admin')

@section('title', 'Tambah Program Pelatihan')
@section('page_title', 'Tambah Program Pelatihan')

@section('content')
<style>
    /* Elemen Kompetensi dan KUK tidak digunakan pada form program. */
    #unit-wrapper .unit-card > .card-body {
        display: none !important;
    }
</style>

<div class="admin-table">
    <h5 class="mb-3">Tambah Program Pelatihan</h5>

    <form action="{{ route('admin.program-pelatihan.store') }}" method="POST" enctype="multipart/form-data">
        @include('admin.program-pelatihan._form', ['program' => null])
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const unitWrapper = document.getElementById('unit-wrapper');
    const addUnitBtn = document.getElementById('add-unit');

    if (!unitWrapper) {
        return;
    }

    // Hapus UI lama Elemen Kompetensi + KUK dari unit yang sudah dirender.
    // Backend tetap dipertahankan agar perubahan ini hanya menyentuh UI.
    unitWrapper.querySelectorAll('.unit-card > .card-body').forEach(function (body) {
        body.remove();
    });

    function updateUnitNumbers() {
        unitWrapper.querySelectorAll('.unit-card').forEach(function (card, index) {
            const label = card.querySelector('.card-header strong');
            if (label) {
                label.textContent = `Unit ${index + 1}`;
            }
        });
    }

    if (addUnitBtn) {
        addUnitBtn.addEventListener('click', function () {
            const unitCount = unitWrapper.querySelectorAll('.unit-card').length;

            const unitCard = `
                <div class="unit-card card mb-3">
                    <div class="card-header bg-light">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <strong>Unit ${unitCount + 1}</strong>
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="unit_kode[]" class="form-control form-control-sm"
                                       placeholder="Kode Unit" required>
                            </div>
                            <div class="col">
                                <input type="text" name="unit_judul[]" class="form-control form-control-sm"
                                       placeholder="Judul Unit" required>
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-danger btn-sm remove-unit">
                                    <i class="bi bi-trash"></i> Hapus Unit
                                </button>
                            </div>
                        </div>
                    </div>
                </div>`;

            unitWrapper.insertAdjacentHTML('beforeend', unitCard);
            updateUnitNumbers();
        });
    }

    document.addEventListener('click', function (event) {
        const removeButton = event.target.closest('.remove-unit');
        if (!removeButton) {
            return;
        }

        const card = removeButton.closest('.unit-card');
        const unitCards = unitWrapper.querySelectorAll('.unit-card');

        if (card && unitCards.length > 1) {
            card.remove();
            updateUnitNumbers();
        } else {
            alert('Minimal harus ada 1 unit kompetensi');
        }
    });

    updateUnitNumbers();
});
</script>
@endpush
@endsection
