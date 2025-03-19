<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Csr;

class CsrController extends Controller
{
    public function index()
    {
        $csrs = Csr::all();
        $sponsors = Csr::distinct()->pluck('sponsor');
        $years = Csr::distinct()->pluck('tahun');
        $months = Csr::distinct()->pluck('bulan');

        return view('dashboard', compact('csrs', 'sponsors', 'years', 'months'));
    }

    public function filter(Request $request)
    {
        $query = Csr::query();

        if (!empty($request->sponsor)) {
            $query->whereIn('sponsor', $request->sponsor);
        }

        if (!empty($request->tahun)) {
            $query->whereIn('tahun', $request->tahun);
        }

        if (!empty($request->bulan)) {
            $query->whereIn('bulan', $request->bulan);
        }

        return response()->json($query->get());
    }
}
