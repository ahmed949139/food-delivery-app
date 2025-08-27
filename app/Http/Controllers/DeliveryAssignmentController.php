<?php

namespace App\Http\Controllers;

use App\Models\OrderAssignment;
use Illuminate\Http\Request;

class DeliveryAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $assignments = OrderAssignment::with('order')->where('delivery_men_id', $request->user()->id)->get();
        return response()->json($assignments);
    }

    public function respond(Request $request, $assignmentId)
    {
        $request->validate([
            'status' => 'required|in:accepted,rejected',
        ]);

        $assignment = OrderAssignment::findOrFail($assignmentId);

        if ($assignment->delivery_men_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $assignment->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Response recorded',
            'assignment' => $assignment,
        ]);
    }
}
