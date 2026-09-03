@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-950">Pengguna Mobile</h1>
            <p class="mt-1 text-gray-500">Akun petugas lapangan yang terdaftar di aplikasi mobile LILA.</p>
        </div>
        <form method="GET" class="flex gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau no. HP..."
                class="w-64 rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 transition">
                Cari
            </button>
        </form>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 p-4 border border-green-200 text-sm font-medium text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-lg border border-gray-200 bg-white overflow-hidden shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No. HP</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Terdaftar</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Terakhir Masuk</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Perjalanan</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Temuan</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-32">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($users as $user)
                    <tr class="{{ $user->is_active ? '' : 'bg-gray-50 opacity-60' }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $user->phone ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->created_at?->format('d M Y') ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->last_login_at?->format('d M Y, H:i') ?? 'Belum pernah' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-700">{{ number_format($user->sessions_count) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-700">{{ number_format($user->events_count) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if ($user->is_active)
                                <span class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700">Aktif</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <form action="{{ route('mobile-users.toggle-active', $user) }}" method="POST"
                                onsubmit="return confirm('{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan kembali' }} akun \'{{ $user->name }}\'?{{ $user->is_active ? ' Perangkat yang sedang login akan langsung ter-logout.' : '' }}')">
                                @csrf
                                @method('PATCH')
                                @if ($user->is_active)
                                    <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Nonaktifkan</button>
                                @else
                                    <button type="submit" class="text-green-600 hover:text-green-900 font-medium">Aktifkan</button>
                                @endif
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center text-sm text-gray-500">
                            Belum ada pengguna mobile yang terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
@endsection
