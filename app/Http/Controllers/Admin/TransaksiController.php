<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;


use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function setor()
    {
        return view('admin.transaksi.setor');
    }

    public function tarik()
    {
        return view('admin.transaksi.tarik');
    }
}
