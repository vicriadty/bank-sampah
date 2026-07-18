@extends('layouts.app')

@section('title', 'Bank Sampah - Sampah')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Sampah</h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Sampah Baru</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.sampah.store') }}" method="post">
                        @csrf
                        @method('POST')
                        @include('admin.sampah._form')
                </div>


                <div class="form-group row mt-4 mr-2">
                    <div class="col-sm-12 d-flex justify-content-end" style="gap: 10px;">
                        <a href="{{ route('admin.sampah.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
@endsection
