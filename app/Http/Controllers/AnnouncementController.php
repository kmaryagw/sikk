<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    /**
     * Admin: daftar pengumuman
     */
    public function index()
    {
        $announcement = Announcement::orderBy('date', 'desc')->paginate(10);
        $type_menu = 'masterdata';
        return view('pages.index-admin-announcement', compact('announcement', 'type_menu'));
    }

    /**
     * Admin: form tambah
     */
    public function create()
    {
        $type_menu = 'masterdata';
        return view('pages.create-admin-announcement', compact('type_menu'));
    }

    /**
     * Admin: simpan data baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'date'    => 'required|date',
            'summary' => 'nullable|string',
            'image'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_main' => 'nullable|boolean',
        ]);

        // Default is_main = false jika checkbox tidak dicentang
        $validated['is_main'] = $request->has('is_main');

        // CATATAN: Logika reset 'is_main' dihapus agar bisa multi-main announcement.

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('announcements', 'public');
        }

        Announcement::create($validated);

        return redirect()
            ->route('announcement.index')
            ->with('success', 'Pengumuman berhasil ditambahkan');
    }

    /**
     * Admin: detail 1 pengumuman
     */
    public function show(Announcement $announcement)
    {
        $type_menu = 'masterdata';
        return view('pages.show-admin-announcement', compact('announcement', 'type_menu'));
    }

    /**
     * Admin: form edit
     */
    public function edit(Announcement $announcement)
    {
        $type_menu = 'masterdata';
        return view('pages.edit-admin-announcement', compact('announcement', 'type_menu'));
    }

    /**
     * Admin: update pengumuman
     */
    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'date'    => 'required|date',
            'summary' => 'nullable|string',
            'image'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_main' => 'nullable|boolean',
        ]);

        $validated['is_main'] = $request->has('is_main');

        // CATATAN: Logika reset 'is_main' dihapus agar bisa multi-main announcement.

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($announcement->image && Storage::disk('public')->exists($announcement->image)) {
                Storage::disk('public')->delete($announcement->image);
            }
            $validated['image'] = $request->file('image')->store('announcements', 'public');
        }

        $announcement->update($validated);

        return redirect()
            ->route('announcement.index')
            ->with('success', 'Pengumuman berhasil diperbarui');
    }

    /**
     * Admin: hapus pengumuman
     */
    public function destroy(Announcement $announcement)
    {
        if ($announcement->image && Storage::disk('public')->exists($announcement->image)) {
            Storage::disk('public')->delete($announcement->image);
        }

        $announcement->delete();

        return redirect()
            ->route('announcement.index')
            ->with('success', 'Pengumuman berhasil dihapus');
    }
    /**
     * Public: halaman pengumuman
     */
    public function publicPage()
    {
        // 1. DATA SLIDER (CAROUSEL) - HANYA PENGUMUMAN UTAMA
        // Menggunakan where('is_main', true) agar yang masuk slider murni hanya yang dicentang 'Main'
        $sliderAnnouncements = Announcement::where('is_main', true)
            ->orderByDesc('date')
            ->get();

        // Ambil ID dari item slider agar tidak muncul dobel di list lain
        // Jika tidak ada pengumuman utama, sliderIds akan kosong (aman)
        $sliderIds = $sliderAnnouncements->pluck('id');

        // 2. DATA SIDEBAR (TIMELINE)
        // Mengambil data selain yang ada di slider
        $otherAnnouncements = Announcement::whereNotIn('id', $sliderIds)
            ->orderByDesc('date')
            ->take(5)
            ->get();

        // 3. DATA GRID BAWAH (PAGINATION)
        // Mengambil data sisa (selain slider)
        $allAnnouncements = Announcement::whereNotIn('id', $sliderIds)
            ->orderByDesc('date')
            ->paginate(12);

        return view('pages.index-announcement', compact(
            'sliderAnnouncements',
            'otherAnnouncements',
            'allAnnouncements'
        ));
    }
}