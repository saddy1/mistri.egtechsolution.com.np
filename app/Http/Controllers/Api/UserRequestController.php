<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class UserRequestController extends Controller
{
    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'problem_type' => 'required|string',
            'gps_lat' => 'required',
            'gps_lng' => 'required',
            'description' => 'nullable|string',
            'audio' => 'nullable|file|max:20480',
            'video' => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/3gpp|max:51200',
        ]);

   $data = $request->only([
    'problem_type',
    'description',
    'gps_lat',
    'gps_lng',
]);

// Always set contact
$data['contact'] = auth()->user()->phone ?? 'N/A';
$id=(auth()->user()->id);
$data['user_id'] = $id;

        /*
        |--------------------------------------------------------------------------
        | FREE REVERSE GEOCODING (OpenStreetMap)
        |--------------------------------------------------------------------------
        */

        try {

            $response = Http::withHeaders([
                'User-Agent' => 'MistriApp/1.0'
            ])->get('https://nominatim.openstreetmap.org/reverse', [
                'format' => 'json',
                'lat' => $request->gps_lat,
                'lon' => $request->gps_lng,
            ]);

            if ($response->successful()) {

                $geoData = $response->json();

                $data['address'] =
                    $geoData['display_name'] ?? null;
            }

        } catch (\Exception $e) {

            // if API fails still continue saving request
            $data['address'] = null;
        }

        /*
        |--------------------------------------------------------------------------
        | CREATE FOLDERS IF NOT EXISTS
        |--------------------------------------------------------------------------
        */

        $audioPath = public_path('Request/audio');
        $videoPath = public_path('Request/video');

        if (!File::exists($audioPath)) {
            File::makeDirectory($audioPath, 0755, true);
        }

        if (!File::exists($videoPath)) {
            File::makeDirectory($videoPath, 0755, true);
        }

        /*
        |--------------------------------------------------------------------------
        | AUDIO UPLOAD
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('audio')) {

            $audio = $request->file('audio');

            $audioName =
                uniqid() . '_audio.' .
                $audio->getClientOriginalExtension();

            $audio->move($audioPath, $audioName);

            $data['audio_path'] =
                'Request/audio/' . $audioName;
        }

        /*
        |--------------------------------------------------------------------------
        | VIDEO UPLOAD
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('video')) {

            $video = $request->file('video');

            $videoName =
                uniqid() . '_video.' .
                $video->getClientOriginalExtension();

            $video->move($videoPath, $videoName);

            $data['video_path'] =
                'Request/video/' . $videoName;
        }

        /*
        |--------------------------------------------------------------------------
        | SAVE REQUEST
        |--------------------------------------------------------------------------
        */

        $service = ServiceRequest::create($data);

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,
            'message' => 'Request sent successfully',
            'data' => [
                'id' => $service->id,
                'address' => $service->address,
                'audio_url' => $service->audio_path
                    ? asset($service->audio_path)
                    : null,
                'video_url' => $service->video_path
                    ? asset($service->video_path)
                    : null,
            ]
        ]);
    }
}