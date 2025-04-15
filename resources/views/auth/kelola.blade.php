@extends('layouts.master')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">Kelola User</h1>
        <a href="{{ route('users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow-md transition">
            + Tambah Akun
        </a>
    </div>

    <table class="min-w-full divide-y-2 divide-gray-200" id="userTable" data-sort-col="0" data-sort-order="asc">
        <thead class="bg-yellow-200">
            <tr class="*:font-medium *:text-gray-900 text-left">
                <th class="px-3 py-2 cursor-pointer" onclick="sortTable(0)">No <span class="sort-icon"></span></th>
                <th class="px-3 py-2 cursor-pointer" onclick="sortTable(1)">Nama <span class="sort-icon"></span></th>
                <th class="px-3 py-2 cursor-pointer" onclick="sortTable(2)">Email <span class="sort-icon"></span></th>
                <th class="px-3 py-2 cursor-pointer" onclick="sortTable(3)">Role <span class="sort-icon"></span></th>
                <th class="px-3 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 *:even:bg-gray-50">
            @foreach($users as $index => $user)
                <tr class="*:text-gray-900 *:first:font-medium">
                    <td class="px-3 py-2">{{ $index + 1 }}</td>
                    <td class="px-3 py-2">{{ $user->name }}</td>
                    <td class="px-3 py-2">{{ $user->email }}</td>
                    <td class="px-3 py-2">
                        @switch($user->role)
                            @case(1)
                                <span class="text-blue-600 font-semibold">Superadmin</span>
                                @break
                            @case(2)
                                <span class="text-yellow-600 font-semibold">Admin</span>
                                @break
                            @default
                                <span class="text-green-600 font-semibold">User</span>
                        @endswitch
                    </td>
                    <td class="px-3 py-2 space-x-1 flex items-center">
                        <!-- Tombol Edit -->
                        <a href="{{ route('users.edit', $user->id) }}" 
                           title="Edit"
                           class="bg-yellow-100 hover:bg-yellow-200 text-yellow-700 p-2 rounded-full transition duration-200 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5l-2 2m0 0L7 14v3h3L19.5 6.5l-2-2z" />
                            </svg>
                        </a>

                        <!-- Tombol Hapus -->
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="bg-red-100 hover:bg-red-200 text-red-600 p-2 rounded-full transition duration-200 shadow-sm"
                                    title="Hapus">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4h6v3" />
                                </svg>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    function sortTable(colIndex) {
        const table = document.getElementById("userTable");
        const tbody = table.querySelector("tbody");
        const rows = Array.from(tbody.querySelectorAll("tr"));

        const currentOrder = table.getAttribute("data-sort-order") === "asc" ? "desc" : "asc";
        table.setAttribute("data-sort-order", currentOrder);
        table.setAttribute("data-sort-col", colIndex);

        rows.sort((a, b) => {
            const cellA = a.children[colIndex].innerText.trim().toLowerCase();
            const cellB = b.children[colIndex].innerText.trim().toLowerCase();
            return currentOrder === "asc" ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
        });

        rows.forEach(row => tbody.appendChild(row));
    }
</script>
@endsection
