<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Csr;

class CsrController extends Controller
{
    public function index()
    {
        $csrs = Csr::all();
        $pemegang_saham = Csr::distinct()->pluck('pemegang_saham');
        $years = Csr::distinct()->pluck('tahun');
        $months = Csr::distinct()->pluck('bulan');

        return view('dashboard', compact('csrs', 'pemegang_saham', 'years', 'months'));
    }

/*************  ✨ Codeium Command ⭐  *************/
    /**
     * Filter the CSR records based on request parameters and return the filtered results as JSON.
     *
     * @param Request $request The HTTP request containing filter parameters.
     *                         - sponsor: array of sponsors to filter by.
     *                         - tahun: array of years to filter by.
     *                         - bulan: array of months to filter by.
     * @return \Illuminate\Http\JsonResponse The filtered CSR records.
     */

/******  e1c7f3fd-5b5e-4b16-aeb8-dc4c17c700f0  *******/    public function filter(Request $request)
    {
        $query = Csr::query();

        if (!empty($request->pemegang_saham)) {
            $query->whereIn('pemegang_saham', $request->pemegang_saham);
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
