@extends('layouts.main')

@section('header', 'Profil Saya')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-lg font-bold mb-4">Informasi Pengguna</h2>
        <div class="space-y-3">
            <div><strong>Nama:</strong> {{ $user->name }}</div>
            <div><strong>Email:</strong> {{ $user->email }}</div>
            <div><strong>Tanggal Bergabung:</strong> {{ optional($user->created_at)->format('d F Y') }}</div>
        </div>
    </div>
</div>
@endsection
