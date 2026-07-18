<div class="form-group mb-3">
    <label for="kategori_id">Kategori Sampah</label>
    <select name="kategori_id" id="kategori_id" class="form-control @error('kategori_id') is-invalid @enderror" required>
        <option value="">-- Pilih Kategori --</option>
        @foreach ($kategoriSampahs as $kategori)
            <option value="{{ $kategori->id }}" @selected(old('kategori_id') == $kategori->id)>
                {{ $kategori->nama_kategori }}</option>
        @endforeach
    </select>
    @error('kategori_id')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

<div class="form-group mb-3">
    <label for="nama_jenis">Jenis Sampah</label>
    <input type="text" name="nama_jenis" id="nama_jenis"
        class="form-control 
    @error('nama_jenis') is-invalid @enderror" value="{{ old('nama_jenis') }}" required>
    @error('nama_jenis')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

<div class="form-group mb-3">
    <label for="harga_per_kg">Harga per Kg</label>
    <input type="number" name="harga_per_kg" id="harga_per_kg" step="0.01"
        class="form-control @error('harga_per_kg') is-invalid @enderror" value="{{ old('harga_per_kg') }}" required>
    @error('harga_per_kg')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>
