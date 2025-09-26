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

        // Pastikan default is_main = false kalau tidak dicentang
        $validated['is_main'] = $request->has('is_main');

        // Upload gambar jika ada
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('announcements', 'public');
        }

        // Jika pengumuman utama baru, reset semua yg lama
        if ($validated['is_main']) {
            Announcement::where('is_main', true)->update(['is_main' => false]);
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

        // Pastikan default is_main = false kalau tidak dicentang
        $validated['is_main'] = $request->has('is_main');

        // Upload gambar baru jika ada
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($announcement->image && Storage::disk('public')->exists($announcement->image)) {
                Storage::disk('public')->delete($announcement->image);
            }

            $validated['image'] = $request->file('image')->store('announcements', 'public');
        }

        // Jika pengumuman utama baru, reset semua yg lama
        if ($validated['is_main']) {
            Announcement::where('is_main', true)->update(['is_main' => false]);
        }

        $announcement->update($validated);

        return redirect()
            ->route('announcement.index')
            ->with('success', 'Pengumuman berhasil diperbarui');
    }

    /**
     * Publik: halaman pengumuman (index-announcement.blade.php)
     */
    public function publicPage()
    {
        // Ambil pengumuman utama (paling baru jika lebih dari 1 ditandai utama)
        $mainAnnouncement = Announcement::where('is_main', true)
            ->orderBy('date', 'desc')
            ->first();

        // Ambil pengumuman lainnya (tidak termasuk utama), maksimal 10 terbaru
        $otherAnnouncements = Announcement::when($mainAnnouncement, function ($query) use ($mainAnnouncement) {
                $query->where('id', '!=', $mainAnnouncement->id);
            })
            ->orderBy('date', 'desc')
            ->take(10)
            ->get();

        // Semua pengumuman untuk grid bawah (tanpa duplikasi)
        $allAnnouncements = Announcement::when($mainAnnouncement, function ($query) use ($mainAnnouncement) {
                $query->where('id', '!=', $mainAnnouncement->id);
            })
            ->orderBy('date', 'desc')
            ->paginate(12);

        return view('pages.index-announcement', compact(
            'mainAnnouncement',
            'otherAnnouncements',
            'allAnnouncements'
        ));
    }

    /**
     * Admin: hapus pengumuman
     */
    public function destroy(Announcement $announcement)
    {
        try {
            // Hapus gambar dari storage jika ada
            if ($announcement->image && Storage::disk('public')->exists($announcement->image)) {
                Storage::disk('public')->delete($announcement->image);
            }
        } catch (\Exception $e) {
            // Bisa log error kalau perlu
            \Log::error('Gagal hapus gambar pengumuman: ' . $e->getMessage());
        }

        // Hapus data pengumuman dari DB
        $announcement->delete();

        return redirect()
            ->route('announcement.index')
            ->with('success', 'Pengumuman berhasil dihapus');
    }

}
