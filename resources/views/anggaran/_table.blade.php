<div id="tableWrapper" x-data="{ showModalId: null, showConfirmId: null }">
  <table class="min-w-full table-auto divide-y-2 divide-gray-200" id="anggaranTable">
    <thead class="bg-yellow-200">
      <tr class="*:font-medium *:text-gray-900">
        <th class="px-3 py-2">No</th>
        <th class="px-3 py-2">Pemegang Saham</th>
        <th class="px-3 py-2">Tahun</th>
        <th class="px-3 py-2">Dana Baru</th>
        <th class="px-3 py-2">Jumlah Anggaran</th>
        <th class="px-3 py-2">Sisa Anggaran</th>
        <th class="px-3 py-2">Aksi</th>
      </tr>
    </thead>

    <tbody class="divide-y divide-gray-200 *:even:bg-gray-50 *:text-gray-900 *:first:font-medium *:align-top">      
      @foreach ($anggaran as $row)

        <tr class="*:text-gray-900 *:first:font-medium">
          <td class="px-3 py-2 align-top">{{ $loop->iteration }}</td>
          <td class="px-3 py-2 align-top">
            {{ $row->pemegang_saham }}
            @if (!empty($row->sisa_dari_tahun_lalu))
                <small class="text-red-500 block italic">* memakai sisa anggaran terdahulu</small>
            @endif
        </td>
        <td class="px-3 py-2 align-top">{{ $row->tahun }}</td>

        <td class="px-3 py-2 align-top text-sm text-gray-800">
          Rp{{ number_format(empty($row->sisa_dari_tahun_lalu) ? $row->jumlah_anggaran : 0, 0, ',', '.') }}
      </td>
      
        
        
        <td class="px-3 py-2 align-top text-sm text-gray-800">
          Rp{{ number_format($row->total_anggaran_tampilan ?? $row->jumlah_anggaran, 0, ',', '.') }}
      </td>
      
      <td class="px-3 py-2 align-top text-sm text-gray-800">
          Rp{{ number_format($row->sisa_anggaran_tampilan ?? $row->hitungSisaAnggaranTotal(), 0, ',', '.') }}
      </td>
      
          <td class="px-3 py-2 flex space-x-2 items-center">

            <!-- Histori Penambahan -->
            <a 
            href="{{ route('csr.riwayat', ['tahun' => $row->tahun, 'pemegang_saham' => $row->pemegang_saham]) }}"
            class="bg-blue-100 hover:bg-blue-200 text-blue-600 p-2 rounded-full transition duration-200 shadow-sm"
            title="Lihat Riwayat Penambahan Anggaran"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405M4 4v16c0 .55.45 1 1 1h14a1 1 0 001-1V7.83a1 1 0 00-.293-.707l-4.83-4.83A1 1 0 0014.17 2H5a1 1 0 00-1 1z" />
            </svg>
          </a>

                    @php
            $isFallback = !empty($row->sisa_dari_tahun_lalu); // boolean flag
          @endphp
            @if (!$isFallback)
            <!-- Edit -->
            <a href="{{ route('anggaran.edit', $row->id) }}" 
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
            @else

            @endif

          


 
</div>
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
                  <form action="{{ route('anggaran.destroy', $row->id) }}" method="POST">
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
        <td colspan="5" class="px-3 py-2 text-right">Total Anggaran:</td>
        <td class="px-3 py-2 text-blue-600">Rp{{ number_format($totalAnggaran, 0, ',', '.') }}</td>
        <td></td>
      </tr>
      <tr>
        <td colspan="5" class="px-3 py-2 text-right">Total Sisa Anggaran:</td>
        <td class="px-3 py-2 text-red-600">
          Rp{{ number_format($anggaran->sum('sisa_anggaran_tampilan'), 0, ',', '.') }}
        </td>
        <td></td>
      </tr>
    </tfoot>
    
    @if ($fallback)
    <div class="bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700 p-4 mb-4">
        <p class="font-semibold">Catatan:</p>
        <p>Data anggaran tahun <strong>{{ $tahunFilter }}</strong> belum tersedia.</p>
        <p>Menampilkan <strong>sisa anggaran tahun {{ $tahunFilter - 1 }}</strong> sebagai referensi sementara.</p>
    </div>
    @endif
  </table>


</div>

  