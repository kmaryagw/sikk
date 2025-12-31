<?php

namespace App\Exports;

use App\Models\target_indikator;
use App\Models\tahun_kerja;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class IkuExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents
{
    protected $tahunId, $prodiId, $keyword;
    protected $unitId; // Unit Kerja

    public function __construct($tahunId = null, $prodiId = null, $unitId = null, $keyword = null)
    {
        $this->tahunId = $tahunId;
        $this->prodiId = $prodiId;
        $this->unitId = $unitId;
        $this->keyword = $keyword;
    }

    public function collection()
    {
        $query = target_indikator::with([
                'indikatorKinerja.unitKerja',
                'monitoringDetail'
            ])
            ->select(
                'target_indikator.*',
                'program_studi.nama_prodi',
                'tahun_kerja.th_tahun'
            )
            ->leftJoin('program_studi', 'program_studi.prodi_id', '=', 'target_indikator.prodi_id')
            ->leftJoin('tahun_kerja', 'tahun_kerja.th_id', '=', 'target_indikator.th_id');

        // Filter Tahun
        if ($this->tahunId) {
            $query->where('tahun_kerja.th_id', $this->tahunId);
        } else {
            $tahunAktif = tahun_kerja::where('th_is_aktif', 'y')->first();
            if ($tahunAktif) {
                $query->where('tahun_kerja.th_id', $tahunAktif->th_id);
            }
        }

        // Filter Prodi
        if ($this->prodiId) {
            $query->where('program_studi.prodi_id', $this->prodiId);
        }

        // Filter Unit Kerja (Commented)
        if ($this->unitId) {
            $query->whereHas('indikatorKinerja.unitKerja', function ($q) {
                $q->where('unit_kerja.unit_id', $this->unitId);
            });
        }

        // Filter Keyword
        if ($this->keyword) {
            $query->whereHas('indikatorKinerja', function ($q) {
                $q->where('ik_nama', 'like', '%' . $this->keyword . '%');
            });
        }

        // --- SORTING NATURAL (A-Z, C.1.1 sebelum C.1.10) ---
        // Kita ambil data dulu dengan get(), baru di-sort menggunakan collection method
        // karena SQL order by string seringkali tidak akurat untuk penomoran desimal (1.1 vs 1.10).
        $data = $query->get();

        return $data->sortBy(function ($item) {
            return $item->indikatorKinerja->ik_kode ?? '';
        }, SORT_NATURAL | SORT_FLAG_CASE);
    }

    public function headings(): array
    {
        return [
            'Tahun',
            'Prodi',
            'Unit Kerja', 
            'Kode Indikator',      
            'Indikator Kinerja',
            'Target',
            'Capaian',
            'Status',
        ];
    }

    public function map($row): array
    {
        $unitKerja = $row->indikatorKinerja->unitKerja->pluck('unit_nama')->join(', '); 

        $detail = $row->monitoringDetail;
        $capaian = $detail->mtid_capaian ?? 'Belum Ada';
        
        // Hitung Status
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
            $row->th_tahun ?? '-',
            $row->nama_prodi ?? '-',
            $unitKerja ?: '-', 
            $row->indikatorKinerja->ik_kode ?? '-',
            $row->indikatorKinerja->ik_nama ?? '-',
            $row->ti_target ?? '-',
            $capaian,
            $status,
        ];
    }

   
    //  * TAMPILAN VISUAL
    public function styles(Worksheet $sheet)
    {
        $lastColumn = $sheet->getHighestColumn();
        $lastRow = $sheet->getHighestRow();

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial');
        $sheet->getParent()->getDefaultStyle()->getFont()->setSize(10);
        
        $sheet->getStyle("A1:{$lastColumn}{$lastRow}")
              ->getAlignment()
              ->setVertical(Alignment::VERTICAL_CENTER)
              ->setWrapText(true);

        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size' => 11,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['argb' => 'FFC00000'], // Merah Marun
            ],
        ];
        $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(40); 
        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFCCCCCC'],
                ],
                'outline' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                ]
            ],
        ];
        $sheet->getStyle("A1:{$lastColumn}{$lastRow}")->applyFromArray($borderStyle);

        // 4. Alignment Spesifik per Kolom
        // Urutan Kolom berdasarkan Headings:
        // A: Tahun | B: Prodi | C: Unit Kerja | D: Kode IK | E: Indikator | F: Target | G: Capaian | H: Status

        // Rata Tengah (Center): Tahun(A), Kode IK(D), Target(F), Capaian(G), Status(H)
        $sheet->getStyle("A2:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("D2:D{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("F2:H{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Rata Kiri (Left): Prodi(B), Unit Kerja(C), Indikator(E) 
        // (Default biasanya sudah kiri, tapi kita pertegas agar rapi)
        $sheet->getStyle("B2:C{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("E2:E{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // 5. Lebar Kolom Manual (Disesuaikan dengan konten)
        $sheet->getColumnDimension('A')->setWidth(10);  // Tahun (Pendek)
        $sheet->getColumnDimension('B')->setWidth(30);  // Prodi (Agak Panjang)
        $sheet->getColumnDimension('C')->setWidth(40);  // Unit Kerja (Sedang)
        $sheet->getColumnDimension('D')->setWidth(12);  // Kode IK (Pendek, misal: 1.1.2)
        $sheet->getColumnDimension('E')->setWidth(60);  // Indikator (Paling Panjang/Deskripsi)
        $sheet->getColumnDimension('F')->setWidth(18);  // Target (Angka/Persen)
        $sheet->getColumnDimension('G')->setWidth(18);  // Capaian (Angka/Persen)
        $sheet->getColumnDimension('H')->setWidth(20);  // Status (Tercapai/Tidak)

        return [];
    }

    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = $sheet->getHighestColumn();
                $lastRow = $sheet->getHighestRow();

                // 1. Auto Filter
                $sheet->setAutoFilter("A1:{$lastColumn}{$lastRow}");

                // 2. Freeze Pane
                $sheet->freezePane('A2');

                // 3. Zebra Striping
                for ($row = 2; $row <= $lastRow; $row++) {
                    if ($row % 2 == 0) {
                        $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'color' => ['argb' => 'FFF9FAFB'],
                            ],
                        ]);
                    }
                }
            },
        ];
    }

    private function parseNumber($value)
    {
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

    private function hitungStatus($capaian, $target, $jenis)
    {
        $jenis = strtolower(trim($jenis));
        
        if (is_null($capaian) || trim($capaian) === '') {
            return 'Belum Ada';
        }

        if (in_array($jenis, ['nilai', 'persentase'])) {
            $valCapaian = $this->parseNumber($capaian);
            $valTarget  = $this->parseNumber($target);
            $epsilon = 0.00001;
            if (abs($valCapaian - $valTarget) < $epsilon) {
                return 'Tercapai';
            } elseif ($valCapaian > $valTarget) {
                return 'Terlampaui';
            } else {
                return 'Tidak Tercapai';
            }
        }

        if ($jenis === 'rasio') {
            $capaianStr = preg_replace('/\s+/', '', $capaian);
            $targetStr  = preg_replace('/\s+/', '', $target);
            $partsCapaian = explode(':', $capaianStr);
            $partsTarget  = explode(':', $targetStr);

            if (count($partsCapaian) == 2 && count($partsTarget) == 2) {
                $rightCapaian = $this->parseNumber($partsCapaian[1]);
                $rightTarget  = $this->parseNumber($partsTarget[1]);
                if ($rightCapaian > $rightTarget) {
                    return 'Terlampaui';
                } elseif ($rightCapaian == $rightTarget) {
                    return 'Tercapai';
                } else {
                    return 'Tidak Tercapai';
                }
            }
            return 'Tidak Tercapai';
        }

        if ($jenis === 'ketersediaan') {
            $capaianLower = strtolower(trim($capaian));
            if ($capaianLower === 'ada') {
                return 'Tercapai';
            }
            return 'Tidak Tercapai';
        }
        return 'Tidak Tercapai';
    }
}