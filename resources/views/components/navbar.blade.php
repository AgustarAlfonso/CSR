<header class="bg-white">
    <div class="mx-auto max-w-screen-xl px-4 sm:px-6 lg:px-8">
      <div class="flex h-16 items-center justify-between">
        <div class="md:flex md:items-center md:gap-12">
          <a class="block text-teal-600" href="#">
            <span class="sr-only">Home</span>
            <img src="{{ asset('images/logo.jpg') }}" alt="Logo" class="h-10">
          </a>
        </div>
  
        <div class="hidden md:block">
          <nav aria-label="Global">
            <ul class="flex items-center gap-6 text-sm">
              <li>
                <a class="text-gray-500 transition hover:text-gray-500/75" href="{{ route("dashboard") }}"> Dashboard </a>
              </li>
  
              @if(in_array(auth()->user()->role, [1, 2]))
              <li>
                <a class="text-gray-500 transition hover:text-gray-500/75" href="{{ route("csr.create") }}"> Tambah Program Kemitaraan </a>
              </li>
              @endif
  
              <li>
                <a class="text-gray-500 transition hover:text-gray-500/75" href="{{ route("anggaran.index") }}"> Anggaran Kemitaraan </a>
              </li>
              
              <li>
                <a class="text-gray-500 transition hover:text-gray-500/75" href="{{ route("csr.riwayat") }}"> Riwayat Kemitaraan </a>
              </li>

              <li>
                <a class="text-gray-500 transition hover:text-gray-500/75" href="{{ route("auth.profile") }}"> Profile </a>
              </li>
              @if(auth()->user()->role == 1)
                <li>
                  <a class="text-gray-500 transition hover:text-gray-500/75" href="{{ route('auth.kelola') }}"> Kelola User </a>
                </li>
              @endif
  
            </ul>
          </nav>
        </div>
  
        <div class="flex items-center gap-4">
          <div class="sm:flex sm:gap-4">
  
            <div class="hidden sm:flex">
              <a
                class="rounded-md bg-gray-100 px-5 py-2.5 text-sm font-medium text-teal-600"
                href="{{ route('logout') }}"
              >
                Logout
              </a>
            </div>
          </div>
  
          <div class="block md:hidden">
            <button
              class="rounded-sm bg-gray-100 p-2 text-gray-600 transition hover:text-gray-600/75"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="size-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
              >
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </header>
  