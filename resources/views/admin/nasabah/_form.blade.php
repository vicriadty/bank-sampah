<div class="row">
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="nik">NIK</label>
            <input type="number" inputmode="numeric" name="nik" id="nik"
                class="form-control @error('nik') is-invalid @enderror" value="{{ old('nik') }}">
            @error('nik')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="nama">Nama Lengkap</label>
            <input type="text" name="nama" id="nama"
                class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}">
            @error('nama')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="username">Username</label>
            <input type="text" name="username" id="username"
                class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}">
            @error('username')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="email">Email</label>
            <input type="email" name="email" id="email"
                class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
            @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="password">Password</label>
            <input type="password" name="password" id="password"
                class="form-control @error('password') is-invalid @enderror" value="{{ old('password') }}">
            @error('password')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="jenis_kelamin">Jenis Kelamin</label>
            <select name="jenis_kelamin" id="jenis_kelamin"
                class="form-control @error('jenis_kelamin') is-invalid @enderror">
                <option value="" disabled selected>-- Pilih --</option>
                <option value="Laki-laki" @selected(old('jenis_kelamin') == 'Laki-laki')>Laki-laki</option>
                <option value="Perempuan" @selected(old('jenis_kelamin') == 'Perempuan')>Perempuan</option>
            </select>
            @error('jenis_kelamin')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="tanggal_lahir">Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" id="tanggal_lahir"
                class="form-control @error('tanggal_lahir') is-invalid @enderror" value="{{ old('tanggal_lahir') }}">
            @error('tanggal_lahir')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="tempat_lahir">Tempat Lahir</label>
            <input type="text" name="tempat_lahir" id="tempat_lahir"
                class="form-control @error('tempat_lahir') is-invalid @enderror" value="{{ old('tempat_lahir') }}">
            @error('tempat_lahir')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="col-12">
        <div class="form-group mb-3">
            <label for="alamat">Alamat</label>
            <textarea name="alamat" id="alamat" cols="15" rows="4"
                class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat') }}</textarea>
            @error('alamat')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="no_hp">No. Handphone</label>
            <input type="number" name="no_hp" id="no_hp"
                class="form-control @error('no_hp') is-invalid @enderror" value="{{ old('no_hp') }}">
            @error('no_hp')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>
    </div>
</div>
