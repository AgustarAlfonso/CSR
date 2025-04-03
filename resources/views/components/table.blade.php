<div class="overflow-x-auto">
    <table class="min-w-full divide-y-2 divide-gray-200" id="csrTable" data-sort-col="0" data-sort-order="desc">
        <thead class="ltr:text-left rtl:text-right bg-yellow-200">
            <tr class="*:font-medium *:text-gray-900">
                <th class="px-3 py-2 whitespace-nowrap cursor-pointer" onclick="sortTable(0)">No <span class="sort-icon"></span></th>
                <th class="px-3 py-2 whitespace-normal break-words max-w-xs cursor-pointer" onclick="sortTable(1)">Nama Program <span class="sort-icon"></span></th>
                <th class="px-3 py-2 whitespace-nowrap cursor-pointer" onclick="sortTable(2)">Bidang Kegiatan <span class="sort-icon"></span></th>
                <th class="px-3 py-2 whitespace-nowrap cursor-pointer" onclick="sortTable(3)">Pemegang Saham <span class="sort-icon"></span></th>
                <th class="px-3 py-2 whitespace-nowrap cursor-pointer" onclick="sortTable(4)">Bulan <span class="sort-icon"></span></th>
                <th class="px-3 py-2 whitespace-nowrap cursor-pointer" onclick="sortTable(5)">Tahun <span class="sort-icon"></span></th>
                <th class="px-3 py-2 whitespace-nowrap cursor-pointer" onclick="sortTable(6)">Realisasi CSR <span class="sort-icon"></span></th>
                <th class="px-3 py-2 whitespace-nowrap">Keterangan</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 *:even:bg-gray-50">
            @foreach($data as $index => $row)
                <tr class="*:text-gray-900 *:first:font-medium">
                    <td class="px-3 py-2 whitespace-nowrap">{{ $index + 1 }}</td>
                    <td class="px-3 py-2 whitespace-normal break-words max-w-xs">{{ $row->nama_program }}</td>
                    <td class="px-3 py-2 whitespace-nowrap">{{ $row->bidang_kegiatan }}</td>
                    <td class="px-3 py-2 whitespace-nowrap">{{ $row->pemegang_saham }}</td>
                    <td class="px-3 py-2 whitespace-nowrap">{{ $row->bulan }}</td>
                    <td class="px-3 py-2 whitespace-nowrap">{{ $row->tahun }}</td>
                    <td class="px-3 py-2 whitespace-nowrap font-semibold text-green-600">Rp{{ number_format($row->realisasi_csr, 0, ',', '.') }}</td>
                    <td class="px-3 py-2 whitespace-normal break-words max-w-xs">{{ $row->ket == 'nan' ? '-' : $row->ket }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="bg-green-200 font-semibold text-gray-800">
            <tr>
                <td colspan="6" class="px-3 py-2 text-right">Total Realisasi CSR:</td>
                <td class="px-3 py-2 text-green-600">Rp{{ number_format($totalRealisasi, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    <div class="mt-4">
        {{ $data->links('pagination::tailwind') }}
    </div>
</div>
