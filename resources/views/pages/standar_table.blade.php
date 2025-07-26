@if ($standars->isEmpty())
    <tr>
        <td colspan="5" class="text-center">
            @if (request('q') || request('nama') || request('kategori'))
                Data tidak ditemukan untuk pencarian atau filter yang dipilih.
            @else
                Tidak ada data tersedia.
            @endif
        </td>
    </tr>
@else
    @php $no = $standars->firstItem(); @endphp
    @foreach ($standars as $standar)
        <tr>
            <td class="text-center">{{ $no++ }}</td>
            <td class="text-center">{{ $standar->std_kategori }}</td>
            <td class="text-center">{{ $standar->std_nama }}</td>
            <td style="padding-top: 2rem; padding-bottom: 2rem;">{{ $standar->std_deskripsi }}</td>
            <td>
                <div class="dropdown">
                    <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="dropdownMenuButton-{{ $standar->std_id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Aksi
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton-{{ $standar->std_id }}">
                        <a class="dropdown-item text-warning" href="{{ route('standar.edit', $standar->std_id) }}">
                            <i class="fa-solid fa-edit"></i> Ubah
                        </a>
                        <form id="delete-form-{{ $standar->std_id }}" method="POST" action="{{ route('standar.destroy', $standar->std_id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item text-danger font-weight-bold" onclick="return confirm('Yakin ingin menghapus?')">
                                <i class="fa-solid fa-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </td>
        </tr>
    @endforeach
@endif
