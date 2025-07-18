
@extends('layouts.app')
@php
    $id_proposal = $proposal->id_proposal;
    // Route untuk kembali ke detail proposal
    if (auth('admin')->check()) {
        $routeTambahPanitia = route('panitia.create', $id_proposal);
        $routeShowPanitia = route('panitia.byProposal', $id_proposal); 
        $routeEditStatus =  $proposal->persetujuans 
                            ? route('persetujuans.editStatus', $proposal->persetujuans->id_persetujuan)
                            : null;
        $routeCreateRundown = route('rundowns.createRundown', $id_proposal);
    } elseif (auth('panitia')->check()) {
        $jabatan = strtolower(auth('panitia')->user()->jabatan_panitia);

        if (in_array($jabatan, ['ketua', 'sekretaris', 'bendahara', 'akademik'])) {
            $routeTambahPanitia = route('panitia.Supercreate', $id_proposal);
            $routeShowPanitia = route('panitia.SuperbyProposal', $id_proposal); 
            $routeEditStatus = $proposal->persetujuans 
                               ? route('persetujuans.SupereditStatus', $proposal->persetujuans->id_persetujuan)
                               : null;
            $routeCreateRundown = route('rundowns.SuperCreateRundown', $id_proposal);
        } else {
            $routeBack = route('proposal.panitia.show.read', ['id' => $id_proposal]);
            $routeTambahPanitia = route('panitia.create', $id_proposal);
        }
    } else {
        // default untuk debugging mode (misal akses publik)
        $routeBack = route('proposals.show', $id_proposal);
        $routeTambahPanitia = '#'; // atau nonaktifkan
    }
@endphp

@section('content')
    
    @if (session('success'))
        <div style="color: green; margin-bottom: 10px;">
            {{ session('success') }}
        </div>
    @endif
    
    @if (session('error'))
        <div style="color: red; margin-bottom: 10px;">
            {{ session('error') }}
        </div>
    @endif
    <h1>Detail Proposal</h1>

    <p><strong>Nama Acara:</strong> {{ $proposal->nama_acara }}</p>
    <p><strong>Jenis Acara:</strong> {{ $proposal->jenis_acara }}</p>
    <p><strong>Nama Pengusul:</strong> {{ $proposal->nama_pengusul }}</p>
    <p><strong>Judul Proposal:</strong> {{ $proposal->judul_proposal }}</p>
    <p><strong>Status:</strong> {{ $proposal->status_proposal }}</p>
    <p><strong>File Proposal:</strong> 
        @if($proposal->file_proposal)
           <a href="{{ asset($proposal->file_proposal) }}" target="_blank">Lihat File</a>
        @else
            Tidak ada file
        @endif
    </p>
    <p><strong>Estimasi Peserta:</strong> {{ $proposal->estimasi_peserta }}</p>
    <p><strong>Kebutuhan Logistik:</strong> {{ $proposal->kebutuhan_logistik }}</p>
    <p><strong>Tanggal Acara:</strong> {{ $proposal->tanggal_acara }}</p>
    <p><strong>Waktu Acara:</strong> {{ $proposal->waktu_acara }}</p>
    <p><strong>Detail Acara:</strong> {{ $proposal->detail_acara }}</p>
    <p><strong>Tangal pengajuan</strong> {{ $proposal->tanggal_pengajuan}}</p>
    <p><strong>Jenis Acara:</strong> {{ $proposal->is_berbayar ? 'Berbayar' : 'Gratis' }}</p>
    @if ($proposal->is_berbayar)
        <p><strong>Harga Tiket:</strong> {{ $proposal->harga_tiket }}</p>
        <p><strong>Nama Bank:</strong> {{ $proposal->nama_bank }}</p>
        <p><strong>Nomor Rekening:</strong> {{ $proposal->nomor_rekening }}</p>
        <p><strong>Nama Pemilik Rekening:</strong> {{ $proposal->nama_pemilik_rekening }}</p>
    @endif  
    <hr>
    <h3>Panitia Acara</h3>
        <a href="{{$routeTambahPanitia}}">+ Tambah Panitia</a><br>
        <a href="{{$routeShowPanitia}}">Lihat Semua</a>
    @if($proposal->panitia->count() > 0)   
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th>Divisi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($proposal->panitia->take(5) as $p)
                <tr>
                    <td>{{ $p->nama_panitia }}</td>
                    <td>{{ $p->jabatan_panitia }}</td>
                    <td>{{ $p->divisi->nama_divisi ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Belum ada panitia untuk acara ini.</p>
    @endif
    @if($proposal->status_proposal === 'Disetujui' && $proposal->persetujuans)
        <hr>
        <h3>Informasi Persetujuan</h3>
        <p><strong>Pihak Penyetuju:</strong> {{ $proposal->persetujuans->pihak_penyetuju }}</p>
        <p><strong>Tanggal Persetujuan:</strong> {{ $proposal->persetujuans->tanggal_persetujuan }}</p>
        <p><strong>Status Persetujuan:</strong> {{ $proposal->persetujuans->status_persetujuan ?? 'Belum Ditentukan' }}</p>
        <p><a href="{{ $routeEditStatus }}">Ubah Status</a></p>
        <hr>
        <h3>Rundown Acara</h3>
        <a href="{{$routeCreateRundown}}">+ Tambah Rundown</a>
        @if ($proposal->rundowns->count())
            <table border="1" cellpadding="10" cellspacing="0" style="margin-top: 10px; width: 100%;">
                <thead>
                    <tr>
                        <th>Judul Rundown</th>
                        <th>Tanggal Kegiatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($proposal->rundowns as $rundown)
                        <tr>
                            <td>{{ $rundown->judul_rundown }}</td>
                            <td>{{ $rundown->tanggal_kegiatan }}</td>
                            <td>
                                <a href="{{ route('rundowns.show', $rundown->id_rundown) }}">Lihat</a> |
                                <a href="{{ route('rundowns.edit', $rundown->id_rundown) }}">Edit</a> |
                                <a href="{{ route('rekap.rundown', $rundown->id_rundown) }}">Rekap</a> |
                                <form action="{{ route('rundowns.destroy', $rundown->id_rundown) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Yakin hapus?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>Belum ada rundown untuk acara ini.</p>
        @endif
        <hr
        @if ($proposal->kuotaPendaftaran)
            <hr>
            <h3>Kuota Pendaftaran</h3>
            <p><strong>Total Kuota:</strong> {{ $proposal->kuotaPendaftaran->total_kuota }}</p>
            <p><strong>Kuota Terpakai:</strong> {{ $proposal->kuotaPendaftaran->kuota_terpakai }}</p>
            <P><strong>Kuota Tersisa:</strong> {{ $proposal->kuotaPendaftaran->total_kuota - $proposal->kuotaPendaftaran->kuota_terpakai }}</p>
            <p><strong>Status Pendaftaran:</strong> {{ $proposal->kuotaPendaftaran->status_pendaftaran }}</p>
            <a href="{{ route('kuota.edit', $proposal->kuotaPendaftaran->id_kuota_pendaftaran) }}">Edit Kuota</a>
        @endif
        <hr>
     <h3>Peserta Acara</h3>
        <a href="{{ route('peserta.created', ['id_proposal' => $proposal->id_proposal]) }}">+ Tambah Peserta</a><br>
        <a href="{{ route('peserta.byProposal', ['id_proposal' => $proposal->id_proposal]) }}">Lihat Semua</a>
        @if (session('error'))
            <div style="color: red; font-weight: bold;">
                {{ session('error') }}
            </div>
        @endif
        @if($proposal->pesertas->count() > 0)
            <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; margin-top: 10px;">
                <thead>
                    <tr>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($proposal->pesertas->take(5) as $peserta)
                        <tr>
                            <td>{{ $peserta->nim }}</td>
                            <td>{{ $peserta->nama_peserta }}</td>
                            <td>{{ $peserta->email }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>Belum ada peserta yang mendaftar.</p>
        @endif

    @else
        <p>Status proposal: {{ $proposal->status_proposal }}</p>
        <p>Proposal ini belum disetujui.</p>
    @endif
    <hr>
    @if(auth('admin')->check())
    <hr>
    <h3>Pengaturan Divisi yang Boleh Melakukan Absensi</h3>
    <form action="{{ route('absensiDivisi.store', $proposal->id_proposal) }}" method="POST">
            @csrf
            <div>
                <p>Pilih Divisi yang bisa melakukan absensi panitia:</p>
                @foreach ($divisis as $divisi)
                    <label style="display: block; margin-bottom: 5px;">
                        <input type="radio" name="divisi_id[]" value="{{ $divisi->id_divisi }}"
                            {{ $proposal->divisiAbsensi->contains($divisi->id_divisi) ? 'checked' : '' }}>
                        {{ $divisi->nama_divisi }}
                    </label>
                @endforeach
            </div>
            <button type="submit">Simpan Akses Absensi</button>
        </form>
    @endif
    @if (auth('admin')->check())
        <a href="{{ route('proposals.index') }}">Kembali</a>
    @elseif (auth('panitia')->check() && in_array(auth('panitia')->user()->jabatan_panitia, ['ketua', 'sekretaris', 'bendahara']))
        <a href="{{ route('proposal.panitia.show') }}">Kembali ke Proposal Saya</a>
    @endif

@endsection
