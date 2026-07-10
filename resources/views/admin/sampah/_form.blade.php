<div class="form-group mb-3">
    <label for="jenis_sampah_id">Jenis Sampah</label>
    <select name="jenis_sampah_id" id="jenis_sampah_id"
        class="form-control @error('jenis_sampah_id') is-invalid @enderror" required>
        <option value="">-- Pilih Jenis Sampah --</option>
        @foreach ($jenisSampahs as $jenis)
            <option value="{{ $jenis->id }}" @selected(old('jenis_sampah_id') == $jenis->id)>
                {{ $jenis->nama_jenis }}</option>
        @endforeach
    </select>
    @error('jenis_sampah_id')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

<div class="form-group mb-3">
    <label for="nama_sampah">Nama Sampah</label>
    <input type="text" name="nama_sampah" id="nama_sampah"
        class="form-control 
    @error('nama_sampah') is-invalid @enderror"
        value="{{ old('nama_sampah') }}">
    @error('nama_sampah')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

<div class="form-group mb-3">
    <label for="harga_per_kg">Harga per Kg</label>
    <input type="number" name="harga_per_kg" id="harga_per_kg" step="0.01"
        class="form-control @error('harga_per_kg') is-invalid @enderror"
        value="{{ old('harga_per_kg') }}">
    @error('harga_per_kg')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>
