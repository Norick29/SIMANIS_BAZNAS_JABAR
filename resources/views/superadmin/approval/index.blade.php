@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-clipboard-check"></i> Approval Perpindahan Aset
    </h1>

    {{-- STATUS ALERT --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-success">
                <i class="fas fa-list"></i> Daftar Permintaan Approval
            </h6>
        </div>

        <div class="card-body">
            @if ($approvals->isEmpty())
                <div class="alert alert-info text-center">
                    <i class="fas fa-inbox"></i> Tidak ada permintaan approval yang menunggu
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th>Kode Aset</th>
                                <th>Barang</th>
                                <th>Dari</th>
                                <th>Ke</th>
                                <th>Peminta</th>
                                <th>Stage</th>
                                <th>Tgl Permintaan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($approvals as $approval)
                                <tr>
                                    <td>
                                        <span class="badge badge-info">
                                            {{ $approval->pindahAset->asset->kode_aset }}
                                        </span>
                                    </td>
                                    <td>{{ $approval->pindahAset->asset->barang->nama_barang }}</td>
                                    <td>
                                        <small>
                                            <strong>Lokasi:</strong> {{ $approval->pindahAset->asset->lokasi->nama_lokasi }}<br>
                                            <strong>PJ:</strong> {{ $approval->pindahAset->amilSebelum->nama }}
                                        </small>
                                    </td>
                                    <td>
                                        <small>
                                            <strong>Lokasi:</strong> {{ $approval->pindahAset->lokasiBaru->nama_lokasi }}<br>
                                            <strong>PJ:</strong> {{ $approval->pindahAset->amilSesudah->nama }}
                                        </small>
                                    </td>
                                    <td>{{ $approval->requestedBy->name ?? '-' }}</td>
                                    <td>
                                        @if ($approval->stage === 1)
                                            <span class="badge badge-warning">Stage 1 - Kadept</span>
                                        @else
                                            <span class="badge badge-info">Stage 2 - Kadiv</span>
                                        @endif
                                    </td>
                                    <td>{{ $approval->created_at->format('d M Y H:i') }}</td>
                                    <td>
                                        @if(auth()->user()->role !== 'super admin')
                                            <a href="{{ route('approvals.show', $approval->id_approval) }}" 
                                            class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> Review
                                            </a>
                                        @else
                                            <span class="badge badge-secondary"><i class="fas fa-lock"></i> Hanya Pantauan</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection