<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data CSR {{ $data->first()->pemegang_saham ?? '' }} - {{ $data->first()->bulan ?? '' }} {{ $data->first()->tahun ?? '' }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <style>
        .dataTables_wrapper .dataTables_length {
            margin-bottom: 10px;
        }
    
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 10px;
        }
    </style>
    
</head>
<body class="bg-red-200">

<div class="container mx-auto mt-10 p-6 bg-white shadow rounded-lg">
    <h3 class="text-xl font-semibold text-gray-800 mb-4">
        Data CSR {{ $data->first()->pemegang_saham ?? '' }} - {{ $data->first()->bulan ?? '' }} {{ $data->first()->tahun ?? '' }}
    </h3>
    <div class="overflow-x-auto">
        <table id="csrTable" class="w-full table-auto border-collapse border border-gray-200">
            <thead class="bg-yellow-200 text-gray-800">
                <tr>
                    <th class="py-2 px-4 text-left">No</th>
                    <th class="py-2 px-4 text-left">Nama Program</th>
                    <th class="py-2 px-4 text-left">Bidang Kegiatan</th>
                    <th class="py-2 px-4 text-left">Pemegang Saham</th>
                    <th class="py-2 px-4 text-left">Bulan</th>
                    <th class="py-2 px-4 text-left">Tahun</th>
                    <th class="py-2 px-4 text-left">Realisasi CSR</th>
                    <th class="py-2 px-4 text-left">Keterangan</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 divide-y divide-gray-200">
                @foreach($data->reverse() as $index => $row)
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 px-4">{{ $index + 1 }}</td>
                        <td class="py-2 px-4">{{ $row->nama_program }}</td>
                        <td class="py-2 px-4">{{ $row->bidang_kegiatan }}</td>
                        <td class="py-2 px-4">{{ $row->pemegang_saham }}</td>
                        <td class="py-2 px-4">{{ $row->bulan }}</td>
                        <td class="py-2 px-4">{{ $row->tahun }}</td>
                        <td class="py-2 px-4 font-semibold text-green-600">Rp{{ number_format($row->realisasi_csr, 0, ',', '.') }}</td>
                        <td class="py-2 px-4">{{ $row->ket == 'nan' ? '-' : $row->ket }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-pastel-green font-semibold text-gray-800">
                <tr>
                    <td colspan="6" class="py-2 px-4 text-right">Total Realisasi CSR:</td>
                    <td class="py-2 px-4 text-green-600">Rp{{ number_format($data->sum('realisasi_csr'), 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <a href='{{ route("dashboard") }}' class="block w-full text-center mt-4 bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 rounded">Kembali</a>
</div>

<script>
    $(document).ready(function() {
        $('#csrTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "order": [[0, "asc"]] // Nomor terbaru menjadi 1
        });
    });
</script>

</body>
</html>
