@extends('layouts.master')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Kelola User</h1>

    <table class="min-w-full divide-y-2 divide-gray-200" id="userTable" data-sort-col="0" data-sort-order="asc">
        <thead class="bg-yellow-200">
            <tr class="*:font-medium *:text-gray-900">
                <th class="px-3 py-2 cursor-pointer" onclick="sortTable(0)">No <span class="sort-icon"></span></th>
                <th class="px-3 py-2 cursor-pointer" onclick="sortTable(1)">Nama <span class="sort-icon"></span></th>
                <th class="px-3 py-2 cursor-pointer" onclick="sortTable(2)">Email <span class="sort-icon"></span></th>
                <th class="px-3 py-2 cursor-pointer" onclick="sortTable(3)">Role <span class="sort-icon"></span></th>
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
