<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead(); 
            
            return redirect($notification->data['url'] ?? route('dashboard'));
        }

        return back();
    }

    public function delete($id)
    {
        $notification = \Auth::user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->delete();
            // Return JSON agar JavaScript tahu proses berhasil
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error'], 404);
    }
}