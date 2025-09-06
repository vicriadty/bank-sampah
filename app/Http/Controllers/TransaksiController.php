<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function setor()
    {
        return view('pages.transaksi.setor');
    }

    public function tarik()
    {
        return view('pages.transaksi.tarik');
    }
}
