<div class="form-group mb-4">
    <label class="font-weight-bold">Nasabah</label>
    <select name="nasabah_id" id="nasabah_id" class="form-control" required>
        <option value="">Pilih Nasabah</option>
        @foreach ($nasabahs as $nasabah)
            <option value="{{ $nasabah->id }}">{{ $nasabah->nama }}</option>
        @endforeach
    </select>
</div>

<hr>
<h6 class="font-weight-bold mb-3">Item Sampah</h6>

<div id="sampah-container">
    <div class="row sampah-row mb-3 align-items-end">
        <div class="col-md-3 col-sm-4">
            <label>Kategori</label>
            <select class="form-control jenis-sampah-select form-control-sm">
                <option value="">Pilih Kategori</option>
                @foreach ($kategoriSampah as $kategori)
                    <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 col-sm-4">
            <label>Jenis Sampah</label>
            <select name="sampah_id[]" class="form-control sampah-select form-control-sm" required>
                <option value="">Pilih Jenis Sampah</option>
            </select>
        </div>
        <div class="col-md-2 col-sm-4">
            <label>Harga/kg</label>
            <input type="text" class="form-control harga-per-kg form-control-sm" readonly>
        </div>
        <div class="col-md-1 col-sm-4">
            <label class="text-small">Berat(kg)</label>
            <input type="number" name="berat[]" class="form-control berat-input form-control-sm" step="0.01"
                min="0.1" required>
        </div>
        <div class="col-md-2 col-sm-4">
            <label>Subtotal</label>
            <input type="text" class="form-control subtotal form-control-sm" readonly>
        </div>
        <div class="col-md-auto col-sm-4 d-flex align-items-end">
            <button type="button" class="btn btn-danger btn-sm btn-remove-row px-2">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>
</div>

<button type="button" id="add-row" class="btn btn-sm btn-info mb-3">
    <i class="fas fa-plus"></i> Tambah Baris
</button>

<div class="row mb-4">
    <div class="col-md-8"></div>
    <div class="col-md-4">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text font-weight-bold">Total</span>
            </div>
            <input type="text" id="total-harga" class="form-control font-weight-bold text-primary" readonly
                value="Rp0">
        </div>
    </div>
</div>
