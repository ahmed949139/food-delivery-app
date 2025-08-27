<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->latest()->get();
        return response()->json($notifications);
    }

    public function markAsRead(Request $request,$id)
    {
        $notification = $request->user()->notifications()->where('id', $id)->first();
        if(!$notification) return response()->json(['message'=> 'Not found'], 404);
        $notification->markAsRead();
        return response()->json(['message'=>'Marked as read']);
    }
}
