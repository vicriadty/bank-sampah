<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Bank Sampah - Login</title>

    <!-- Custom fonts for this template-->
    <link rel="icon" href="{{ asset('favicon1.svg') }}" type="image/x-icon">
    <link href="{{ asset('template/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('template/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #ffffff;
        }

        .btn-primary {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-primary:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
    </style>

</head>

<body>


    <div class="container">

        <!-- Outer Row -->

        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="p-5">
                                    <div class="text-center">
                                        <img src="{{ asset('favicon1.svg') }}" alt="Recycle Logo" width="72">
                                        <h1 class="h4 text-gray-900 mb-4 mt-3">BANK SAMPAH</h1>
                                        <h2 class="h5 text-gray-900 mb-4">Selamat Datang</h2>
                                    </div>

                                    @if (session('error'))
                                        <script>
                                            Swal.fire({
                                                title: 'Gagal',
                                                text: '{{ session('error') }}',
                                                icon: 'error'
                                            });
                                        </script>
                                    @endif

                                    @if ($errors->any())
                                        <script>
                                            Swal.fire({
                                                title: "Terjadi Kesalahan!",
                                                html: `{!! implode('<br>', $errors->all()) !!}`,
                                                icon: "error"
                                            });
                                        </script>
                                    @endif


                                    <form class="user" method="POST" action="{{ route('login') }}">
                                        @csrf
                                        @method('POST')
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-user" id="username"
                                                aria-describedby="emailHelp" name="username" placeholder="Username"
                                                required>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user" id="password"
                                                name="password" placeholder="Password" required>
                                        </div>
                                        <div class="form-group">

                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            Login
                                        </button>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <span>Belum punya akun?</span> <a href="/register">Register</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>


    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('template/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('template/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('template/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('template/js/sb-admin-2.min.js') }}"></script>

</body>

</html>
