@extends('layouts.admin')
@section('title','Kategori')
@section('page-title','Kategori')
@section('page-description','Kelola kategori katalog dan urutan tampil di website.')
@section('page-action')<a href="{{ route('admin.categories.create') }}" class="button button-primary">Tambah Kategori</a>@endsection
@section('content')
@include('partials.admin-page-header')
@if(session('status'))<section class="admin-card">{{ session('status') }}</section>@endif
<div class="admin-tabs"><a href="{{ route('admin.products') }}">Produk</a><a href="{{ route('admin.stock') }}">Stok Bahan</a><a class="active" href="{{ route('admin.categories') }}">Kategori ({{ $categories->total() }})</a></div>
<section class="admin-card"><form class="filter-bar"><input name="q" value="{{ request('q') }}" placeholder="Cari kategori"><button class="button button-primary">Terapkan</button></form><div class="table-wrap"><table class="admin-table"><thead><tr><th>Kategori</th><th>Slug</th><th>Produk</th><th>Status</th><th>Urutan</th><th>Aksi</th></tr></thead><tbody>@forelse($categories as $category)<tr><td><b>{{ $category->name }}</b><small>{{ $category->description }}</small></td><td>{{ $category->slug }}</td><td>{{ $category->products_count }}</td><td><span class="status-pill {{ $category->is_active ? 'success' : 'neutral' }}">{{ $category->is_active ? 'Aktif' : 'Nonaktif' }}</span></td><td>{{ $category->sort_order }}</td><td><div class="admin-row-actions"><a class="table-link" href="{{ route('admin.categories.edit',$category) }}">Edit</a><form method="POST" action="{{ route('admin.categories.status',$category) }}">@csrf @method('PATCH')<button class="table-link danger-link">{{ $category->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button></form></div></td></tr>@empty<tr><td colspan="6">Belum ada kategori.</td></tr>@endforelse</tbody></table></div>{{ $categories->links() }}</section>
@endsection
