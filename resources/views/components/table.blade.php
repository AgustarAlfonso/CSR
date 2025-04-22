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
                <th class="px-3 py-2 whitespace-normal break-words max-w-xs">Keterangan</th>
                <th class="px-3 py-2 whitespace-nowrap">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 *:even:bg-gray-50" x-data="{ showConfirmId: null }">
            @foreach($data as $index => $row)
                <tr class="*:text-gray-900 *:first:font-medium">
                    <td class="px-3 py-2 whitespace-nowrap">{{ $index + 1 }}</td>
                    <td class="px-3 py-2 whitespace-normal break-words max-w-xs">{{ $row->nama_program }}</td>
                    <td class="px-3 py-2 whitespace-nowrap">{{ $row->bidang_kegiatan }}</td>
                    <td class="px-3 py-2 whitespace-nowrap">{{ $row->pemegang_saham }}</td>
                    @php
                    $namaBulan = [
                        1 => 'Januari',
                        2 => 'Februari',
                        3 => 'Maret',
                        4 => 'April',
                        5 => 'Mei',
                        6 => 'Juni',
                        7 => 'Juli',
                        8 => 'Agustus',
                        9 => 'September',
                        10 => 'Oktober',
                        11 => 'November',
                        12 => 'Desember',
                    ];
                @endphp
                
                <td class="px-3 py-2 whitespace-nowrap">
                    {{ $namaBulan[(int) $row->bulan] ?? 'Bulan Tidak Valid' }}
                </td>
                                    <td class="px-3 py-2 whitespace-nowrap">{{ $row->tahun }}</td>
                    <td class="px-3 py-2 whitespace-nowrap font-semibold text-green-600">Rp{{ number_format($row->realisasi_csr, 0, ',', '.') }}</td>
                    <td class="px-3 py-2 whitespace-normal break-words max-w-xs">{{ $row->ket == 'nan' ? '-' : $row->ket }}</td>
                    <td class="px-3 py-2">
                      <div class="flex space-x-2 items-center">
                        @if(in_array(Auth::user()->role, [1, 2]))
                            <!-- Edit -->
                            <a href="{{ route('csr.edit', $row->id) }}" 
                              title="Edit"
                              class="bg-yellow-100 hover:bg-yellow-200 text-yellow-700 p-2 rounded-full transition duration-200 shadow-sm">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5l-2 2m0 0L7 14v3h3L19.5 6.5l-2-2z" />
                              </svg>
                            </a>
                    
                            <!-- Hapus -->
                            <button @click="showConfirmId = {{ $row->id }}"
                              class="bg-red-100 hover:bg-red-200 text-red-600 p-2 rounded-full transition duration-200 shadow-sm"
                              title="Hapus">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4h6v3" />
                              </svg>
                            </button>
                        @endif
                    </div>
                    

                        <!-- Modal Konfirmasi -->
                        <div
                          x-show="showConfirmId === {{ $row->id }}"
                          x-cloak
                          class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
                          x-transition
                        >
                          <div class="bg-white rounded-lg shadow-lg p-6 w-80">
                            <h2 class="text-lg font-semibold text-gray-800 mb-4">Konfirmasi Hapus</h2>
                            <p class="text-sm text-gray-600 mb-6">Yakin ingin menghapus data ini?</p>
                            <div class="flex justify-end space-x-3">
                              <button @click="showConfirmId = null"
                                class="px-4 py-1.5 bg-gray-200 hover:bg-gray-300 rounded text-sm">Batal</button>
                              <form action="{{ route('csr.destroy', $row->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                  class="px-4 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded text-sm">Hapus</button>
                              </form>
                            </div>
                          </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="bg-green-200 font-semibold text-gray-800">
            <tr>
                <td colspan="6" class="px-3 py-2 text-right">Total Realisasi CSR:</td>
                <td class="px-3 py-2 text-green-600">Rp{{ number_format($totalRealisasi, 0, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
    <div class="mt-4">
      {{ $data->appends(request()->query())->links('pagination::tailwind') }}
  </div>
  
</div>
