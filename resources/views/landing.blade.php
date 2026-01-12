@extends('layouts.app')

@section('title','POS MLK - Beranda')
@section('header', 'Selamat Datang di POS MLK')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Hero Section -->
    <div class="card-elevated md:col-span-2">
        <h1 class="text-3xl md:text-4xl font-bold mb-4">
            Selamat Datang di <span class="text-accent-green">POS MLK</span>
        </h1>
        <p class="text-gray-600 text-lg mb-6">
            Sistem Penjualan Terpadu (POS) profesional yang dirancang untuk memudahkan bisnis Anda. Kelola produk, stok, dan transaksi dengan mudah dan efisien.
        </p>
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('cashier') }}" class="btn btn-primary no-underline">
                🛒 Mulai Transaksi
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-outline no-underline">
                📊 Lihat Dashboard
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="card">
        <div class="mb-4">
            <div class="text-3xl font-bold text-accent-green">📦</div>
            <h3 class="font-bold mt-2">Manajemen Produk</h3>
        </div>
        <p class="text-gray-600 text-sm mb-4">Kelola semua produk Anda dengan mudah. Tambah, edit, atau hapus produk sesuai kebutuhan.</p>
        <a href="{{ route('products.index') }}" class="text-accent-green font-medium hover:text-emerald-700 no-underline">
            Kelola Produk →
        </a>
    </div>

    <div class="card">
        <div class="mb-4">
            <div class="text-3xl font-bold text-accent-green">📚</div>
            <h3 class="font-bold mt-2">Manajemen Stok</h3>
        </div>
        <p class="text-gray-600 text-sm mb-4">Pantau stok produk secara real-time dan cegah kehabisan stok dengan fitur alert.</p>
        <a href="{{ route('stock-entries.index') }}" class="text-accent-green font-medium hover:text-emerald-700 no-underline">
            Kelola Stok →
        </a>
    </div>

    <!-- Features -->
    <div class="card md:col-span-2">
        <h3 class="font-bold text-lg mb-4">Fitur Unggulan</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex gap-3">
                <div class="text-2xl">✓</div>
                <div>
                    <h4 class="font-bold">Transaksi Cepat</h4>
                    <p class="text-sm text-gray-600">Proses penjualan yang cepat dan akurat</p>
                </div>
            </div>
            <div class="flex gap-3">
                <div class="text-2xl">✓</div>
                <div>
                    <h4 class="font-bold">Laporan Lengkap</h4>
                    <p class="text-sm text-gray-600">Analisis penjualan dan stok secara detail</p>
                </div>
            </div>
            <div class="flex gap-3">
                <div class="text-2xl">✓</div>
                <div>
                    <h4 class="font-bold">Multi Pengguna</h4>
                    <p class="text-sm text-gray-600">Kelola tim dengan berbagai level akses</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction History -->
    <div class="card md:col-span-2">
        <h3 class="font-bold text-lg mb-4">Transaksi Terbaru</h3>
        <p class="text-gray-600 text-sm">
            Lihat semua transaksi dan kelola riwayat penjualan Anda.
        </p>
        <a href="{{ route('transactions.index') }}" class="inline-block mt-4 text-accent-green font-medium hover:text-emerald-700 no-underline">
            Lihat Semua Transaksi →
        </a>
    </div>
</div>
@endsection