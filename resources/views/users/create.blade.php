@extends('layouts.main')

@section('title','Tambah User')

@section('content')
<div class="card max-w-md">
    <h2 class="text-xl font-semibold">Tambah User</h2>
    <form method="POST" action="{{ route('users.store') }}" class="mt-4">
        @csrf
        <div class="grid gap-3">
            <input name="name" placeholder="Nama" required class="border p-2 rounded" />
            <input name="email" placeholder="Email" required class="border p-2 rounded" />
            <input name="password" placeholder="Password" required class="border p-2 rounded" />
            <select name="role" class="border p-2 rounded">
                <option value="karyawan">Karyawan</option>
                <option value="pemilik">Pemilik</option>
            </select>
            <div>
                <button class="bg-green-600 text-white px-4 py-2 rounded">Simpan</button>
                <a href="{{ route('users.index') }}" class="ml-2 text-gray-600">Batal</a>
            </div>
        </div>
    </form>
</div>
@endsection