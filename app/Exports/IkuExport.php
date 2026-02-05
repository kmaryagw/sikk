<?php

namespace App\Exports;

use App\Models\target_indikator;
use App\Models\tahun_kerja;
use App\Models\program_studi;
use App\Models\UnitKerja; // Pastikan model ini ada
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class IkuExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    WithStyles, 
    WithEvents,
    WithCustomStartCell
{
    protected $tahunId, $prodiId, $unitId, $keyword;
    protected $headerInfo = [];

    public function __construct($tahunId = null, $prodiId = null, $unitId = null, $keyword = null)
    {
        $this->tahunId = $tahunId;
        $this->prodiId = $prodiId;
        $this->unitId = $unitId;
        $this->keyword = $keyword;

        // Ambil data untuk Header Identitas
        $this->prepareHeaderInfo();
    }

    private function prepareHeaderInfo()
    {
        // 1. Ambil Nama Tahun
        if ($this->tahunId) {
            $t = tahun_kerja::find($this->tahunId);
            $this->headerInfo['tahun'] = $t ? $t->th_tahun : '-';
        } else {
            $t = tahun_kerja::where('th_is_aktif', 'y')->first();
            $this->headerInfo['tahun'] = $t ? $t->th_tahun . ' (Aktif)' : 'Semua Tahun';
        }

        // 2. Ambil Nama Prodi
        if ($this->prodiId) {
            $p = program_studi::where('prodi_id', $this->prodiId)->first();
            $this->headerInfo['prodi'] = $p ? $p->nama_prodi : '-';
        } else {
            $this->headerInfo['prodi'] = 'Semua Program Studi';
        }

        // 3. Ambil Nama Unit
        if ($this->unitId) {
            $u = UnitKerja::where('unit_id', $this->unitId)->first();
            $this->headerInfo['unit'] = $u ? $u->unit_nama : '-';
        } else {
            $this->headerInfo['unit'] = 'Semua Unit Kerja';
        }
    }

    public function startCell(): string
    {
        return 'A6'; // Tabel dimulai dari baris ke-6
    }

    public function collection()
    {
        $query = target_indikator::with([
                'indikatorKinerja.unitKerja',
                'monitoringDetail'
            ])
            ->leftJoin('program_studi', 'program_studi.prodi_id', '=', 'target_indikator.prodi_id')
            ->leftJoin('tahun_kerja', 'tahun_kerja.th_id', '=', 'target_indikator.th_id');

        if ($this->tahunId) {
            $query->where('target_indikator.th_id', $this->tahunId);
        }
        if ($this->prodiId) {
            $query->where('target_indikator.prodi_id', $this->prodiId);
        }
        if ($this->unitId) {
            $query->whereHas('indikatorKinerja.unitKerja', function ($q) {
                $q->where('unit_kerja.unit_id', $this->unitId);
            });
        }
        if ($this->keyword) {
            $query->whereHas('indikatorKinerja', function ($q) {
                $q->where('ik_nama', 'like', '%' . $this->keyword . '%');
            });
        }

        $data = $query->get();

        return $data->sortBy(function ($item) {
            return $item->indikatorKinerja->ik_kode ?? '';
        }, SORT_NATURAL | SORT_FLAG_CASE);
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Indikator',      
            'Indikator Kinerja',
            'Target',
            'Capaian',
            'Status',
        ];
    }

    public function map($row): array
    {
        static $no = 1;
        $detail = $row->monitoringDetail;
        $capaian = $detail->mtid_capaian ?? 'Belum Ada';
        
        if ($detail && !empty($detail->mtid_status) && $detail->mtid_status !== 'Draft') {
            $status = ucfirst($detail->mtid_status);
        } else {
            $status = $detail ? $this->hitungStatus(
                $detail->mtid_capaian,
                $row->ti_target,
                $row->indikatorKinerja->ik_ketercapaian
            ) : 'Belum Ada';
        }

        return [
            $no++,
            $row->indikatorKinerja->ik_kode ?? '-',
            $row->indikatorKinerja->ik_nama ?? '-',
            $row->ti_target ?? '-',
            $capaian,
            $status,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $startRow = 6;

        // Styling Identitas Header (Baris 1-4)
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2:A4')->getFont()->setBold(true);

        // Styling Table Headings (Baris 6)
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFC00000']],
        ];
        $sheet->getStyle("A{$startRow}:F{$startRow}")->applyFromArray($headerStyle);
        $sheet->getRowDimension($startRow)->setRowHeight(30);

        // Border Tabel
        $sheet->getStyle("A{$startRow}:F{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']],
            ],
        ]);

        // Alignment Data
        $sheet->getStyle("A7:B{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("D7:F{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("C7:C{$lastRow}")->getAlignment()->setWrapText(true);

        // Lebar Kolom
        $sheet->getColumnDimension('A')->setWidth(5);   // No
        $sheet->getColumnDimension('B')->setWidth(15);  // Kode
        $sheet->getColumnDimension('C')->setWidth(60);  // Indikator
        $sheet->getColumnDimension('D')->setWidth(20);  // Target
        $sheet->getColumnDimension('E')->setWidth(20);  // Capaian
        $sheet->getColumnDimension('F')->setWidth(20);  // Status

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = 'F'; // Sesuaikan dengan kolom terakhir tabel Anda (A sampai F)
                
                // 1. Menulis Identitas
                $sheet->setCellValue('A1', 'LAPORAN CAPAIAN INDIKATOR KINERJA UTAMA / TAMBAHAN INSTIKI');
                $sheet->setCellValue('A2', 'Tahun Akademik : ' . $this->headerInfo['tahun']);
                $sheet->setCellValue('A3', 'Program Studi : ' . $this->headerInfo['prodi']);
                $sheet->setCellValue('A4', 'Unit Kerja : ' . $this->headerInfo['unit']);

                // 2. Menengahka Tulisan (Merge & Center)
                // Kita merge dari kolom A sampai F agar teks berada tepat di tengah tabel
                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->mergeCells("A2:{$lastColumn}2");
                $sheet->mergeCells("A3:{$lastColumn}3");
                $sheet->mergeCells("A4:{$lastColumn}4");

                // Berikan style rata tengah untuk baris 1 sampai 4
                $sheet->getStyle("A1:{$lastColumn}4")->getAlignment()->applyFromArray([
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ]);

                // Tambahan: Menebalkan Judul Utama
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

                // 3. Pengaturan Tabel (Filter & Freeze)
                $lastRow = $sheet->getHighestRow();
                $sheet->setAutoFilter("A6:{$lastColumn}{$lastRow}");
                $sheet->freezePane('A7');

                // 4. Zebra Striping (Warna selang-seling)
                for ($row = 7; $row <= $lastRow; $row++) {
                    if ($row % 2 == 0) {
                        $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->setStartColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFF9FAFB'));
                    }
                }
            },
        ];
    }

    private function parseNumber($value) {
        if (is_null($value) || $value === '') return 0;
        $string = (string) $value;
        $clean = preg_replace('/[^0-9.,-]/', '', $string);
        if (strpos($clean, '.') !== false && strpos($clean, ',') !== false) {
            $clean = str_replace('.', '', $clean);
            $clean = str_replace(',', '.', $clean);
        } elseif (strpos($clean, ',') !== false) {
            $clean = str_replace(',', '.', $clean);
        }
        return (float) $clean;
    }

    private function hitungStatus($capaian, $target, $jenis) {
        $jenis = strtolower(trim($jenis));
        if (is_null($capaian) || trim($capaian) === '') return 'Belum Ada';
        if (in_array($jenis, ['nilai', 'persentase'])) {
            $valCapaian = $this->parseNumber($capaian);
            $valTarget  = $this->parseNumber($target);
            return ($valCapaian >= $valTarget) ? ($valCapaian > $valTarget ? 'Terlampaui' : 'Tercapai') : 'Tidak Tercapai';
        }
        if ($jenis === 'rasio') {
            $partsCapaian = explode(':', preg_replace('/\s+/', '', $capaian));
            $partsTarget  = explode(':', preg_replace('/\s+/', '', $target));
            if (count($partsCapaian) == 2 && count($partsTarget) == 2) {
                $rC = $this->parseNumber($partsCapaian[1]);
                $rT = $this->parseNumber($partsTarget[1]);
                return ($rC >= $rT) ? ($rC > $rT ? 'Terlampaui' : 'Tercapai') : 'Tidak Tercapai';
            }
        }
        if ($jenis === 'ketersediaan') {
            return strtolower(trim($capaian)) === 'ada' ? 'Tercapai' : 'Tidak Tercapai';
        }
        return 'Tidak Tercapai';
    }
}