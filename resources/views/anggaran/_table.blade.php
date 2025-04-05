<div id="tableWrapper">
<table class="min-w-full divide-y-2 divide-gray-200" id="anggaranTable">
    {{-- ...thead seperti sebelumnya... --}}
    <thead class="bg-yellow-200">
      <tr class="*:font-medium *:text-gray-900">
        <th class="px-3 py-2">No</th>
        <th class="px-3 py-2">Pemegang Saham</th>
        <th class="px-3 py-2">Bulan</th>
        <th class="px-3 py-2">Tahun</th>
        <th class="px-3 py-2">Jumlah Anggaran</th>
        <th class="px-3 py-2">Sisa Anggaran</th>
        <th class="px-3 py-2">Aksi</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-200 *:even:bg-gray-50">
      @foreach ($anggaran as $row)
        <tr class="*:text-gray-900 *:first:font-medium">
          <td>{{ $loop->iteration }}</td>
          <td>{{ $row->pemegang_saham }}</td>
          <td>{{ $row->bulan }}</td>
          <td>{{ $row->tahun }}</td>
          <td>Rp{{ number_format($row->jumlah_anggaran, 0, ',', '.') }}</td>
          <td>
              Rp{{ number_format($row->hitungSisaAnggaranTotal(), 0, ',', '.') }}
          </td>
          <td class="px-3 py-2 space-x-2">
            <a href="{{ route('anggaran.edit', $row->id) }}" class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded text-sm">Edit</a>
            <form action="{{ route('anggaran.destroy', $row->id) }}" method="POST" class="inline">
              @csrf
              @method('DELETE')
              <button onclick="return confirm('Yakin ingin menghapus?')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">Hapus</button>
            </form>
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
          Rp{{ number_format($anggaran->sum(fn($a) => $a->hitungSisaAnggaranTotal()), 0, ',', '.') }}
        </td>
        <td></td>
      </tr>
    </tfoot>
    
  </table>
  
  <div class="mt-4">
    {{ $anggaran->links('pagination::tailwind') }}
  </div>
</div>
  