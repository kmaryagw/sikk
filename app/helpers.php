<?php

if (!function_exists('hitungStatus')) {
    function hitungStatus($capaian, $target, $jenis)
    {
        if (
            ($capaian === null || trim((string)$capaian) === '') || 
            ($target === null || trim((string)$target) === '')
        ) {
            return null;
        }

        $jenis = strtolower($jenis);

        if (in_array($jenis, ['persentase', 'nilai'])) {
            $cap = (float) str_replace(['%', ' '], '', $capaian); 
            $tar = (float) str_replace(['%', ' '], '', $target);

            if ($cap > $tar) {
                return 'terlampaui';
            } elseif ($cap == $tar) {
                return 'tercapai';
            } else {
                return 'tidak tercapai';
            }
        }

        if (
            $jenis === 'rasio' &&
            preg_match('/^\s*(\d+)\s*:\s*(\d+)\s*$/', $target, $tMatch) &&
            preg_match('/^\s*(\d+)\s*:\s*(\d+)\s*$/', $capaian, $cMatch)
        ) {
            $targetRight = (int) $tMatch[2];
            $capaianRight = (int) $cMatch[2];

            if ($capaianRight > $targetRight) {
                return 'terlampaui';
            } elseif ($capaianRight == $targetRight) {
                return 'tercapai';
            } else {
                return 'tidak tercapai';
            }
        }

        if ($jenis === 'ketersediaan') {
            return strtolower($capaian) === 'ada' ? 'tercapai' : 'tidak tercapai';
        }

        return null;
    }
}