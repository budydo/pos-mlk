@extends('layouts.main')

@section('title','Pengguna')
@section('header', 'Manajemen Pengguna')

@section('content')
<div class="card-elevated">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold mb-1">👥 Daftar Pengguna</h2>
            <p class="text-gray-600 text-sm">Kelola pengguna aplikasi POS MLK</p>
        </div>
        <div>
            @if(auth()->user()?->role === 'pemilik')
                <a href="{{ route('users.create') }}" class="btn btn-primary no-underline">+ Tambah Pengguna</a>
            @endif
        </div>
    </div>

    @if($users->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full text-sm table-striped">
            <thead class="bg-gray-50 border-b-2 border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left font-bold text-gray-700">Nama</th>
                    <th class="px-4 py-3 text-left font-bold text-gray-700">Email</th>
                    <th class="px-4 py-3 text-center font-bold text-gray-700">Role</th>
                    <th class="px-4 py-3 text-center font-bold text-gray-700">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $u)
                    <tr class="hover:bg-emerald-50 transition-colors">
                        <td class="px-4 py-3 font-medium text-gray-900">
                            {{ $u->name }}
                            @if(auth()->check() && auth()->user()->id === $u->id)
                            <span class="ml-2 text-xs font-semibold text-accent-green">(Anda)</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $u->email }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="badge badge-success">
                                {{ ucfirst($u->role) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex gap-2 items-center justify-center">
                                <a href="{{ route('users.edit', $u->id) }}" class="text-blue-600 font-medium text-sm hover:underline no-underline">Edit</a>
                                @if(auth()->user()?->role === 'pemilik' && auth()->user()->id !== $u->id)
                                    <form method="POST" action="{{ route('users.destroy', $u->id) }}" onsubmit="return confirm('Hapus pengguna ini?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 font-medium text-sm hover:underline">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="text-center py-12">
        <p class="text-gray-500 text-lg mb-4">👥 Tidak ada pengguna</p>
        @if(auth()->user()?->role === 'pemilik')
            <a href="{{ route('users.create') }}" class="btn btn-primary no-underline">Tambah Pengguna Pertama</a>
        @endif
    </div>
    @endif
</div>
@endsection