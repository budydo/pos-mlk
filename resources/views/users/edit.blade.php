@extends('layouts.main')

@section('title','Edit User')

@section('content')
<div class="card">
    <h2 class="text-xl font-semibold">Edit User</h2>

    <form method="POST" action="{{ route('users.update', $user->id) }}" class="mt-4">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 gap-4 max-w-md">
            <input name="name" value="{{ $user->name }}" class="border p-2 rounded" placeholder="Nama" required />
            <input name="email" value="{{ $user->email }}" class="border p-2 rounded" placeholder="Email" required />
            <input name="password" class="border p-2 rounded" placeholder="Password (kosongkan jika tidak diubah)" />
            <select name="role" class="border p-2 rounded">
                <option value="karyawan" @if($user->role=='karyawan') selected @endif>Karyawan</option>
                <option value="pemilik" @if($user->role=='pemilik') selected @endif>Pemilik</option>
            </select>
        </div>

        <div class="mt-4">
            <button class="bg-green-600 text-white px-4 py-2 rounded">Simpan</button>
            <a href="{{ route('users.index') }}" class="ml-2 text-gray-600">Batal</a>
        </div>
    </form>
</div>
@endsection