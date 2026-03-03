<?php

// app/Http/Controllers/Admin/AdminRequestController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;

class AdminRequestController extends Controller
{
  public function index(Request $request)
{
    $search = $request->search;

    $requests = \App\Models\ServiceRequest::with('user')
        ->when($search, function ($q) use ($search) {
            $q->where('problem_type', 'like', "%$search%")
              ->orWhereHas('user', function ($uq) use ($search) {
                  $uq->where('name', 'like', "%$search%")
                     ->orWhere('phone', 'like', "%$search%");
              });
        })
        ->latest()
        ->paginate(20);

    return view('admin.dashboard', compact('requests'));
}

    public function solve(ServiceRequest $serviceRequest)
    {
        // delete audio/video if exists
        if ($serviceRequest->audio_path) {
            $audioFull = public_path($serviceRequest->audio_path);
            if (file_exists($audioFull)) @unlink($audioFull);
            $serviceRequest->audio_path = null;
        }

        if ($serviceRequest->video_path) {
            $videoFull = public_path($serviceRequest->video_path);
            if (file_exists($videoFull)) @unlink($videoFull);
            $serviceRequest->video_path = null;
        }

        $serviceRequest->status = 'solved';
        $serviceRequest->solved_at = now();
        $serviceRequest->save();

        return back()->with('success', 'Marked as solved. Media deleted.');
    }

    public function destroy(ServiceRequest $serviceRequest)
    {
        // delete media if exists
        if ($serviceRequest->audio_path) {
            $audioFull = public_path($serviceRequest->audio_path);
            if (file_exists($audioFull)) @unlink($audioFull);
        }
        if ($serviceRequest->video_path) {
            $videoFull = public_path($serviceRequest->video_path);
            if (file_exists($videoFull)) @unlink($videoFull);
        }

        $serviceRequest->delete();

        return back()->with('success', 'Request deleted successfully.');
    }
    public function show(ServiceRequest $serviceRequest)
{
    $serviceRequest->load('user');
    return view('admin.request_show', compact('serviceRequest'));
}
}