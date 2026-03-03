@extends('layouts.header')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

<div class="max-w-6xl mx-auto p-4 sm:p-6 space-y-6">

    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-slate-800">
            Service Request #{{ $serviceRequest->id }}
        </h2>
        <a href="{{ url()->previous() }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg transition-colors">
            &larr; Back to List
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
<div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4">User Information</h3>
            
            <div class="flex items-center gap-4 mb-4 pb-4 border-b border-slate-100">
                
                <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-blue-200 bg-slate-100 flex items-center justify-center shrink-0">
                    @if($serviceRequest->user && $serviceRequest->user->profile_image)
                        <img src="{{ asset($serviceRequest->user->profile_image) }}" class="w-full h-full object-cover" alt="{{ $serviceRequest->user->name }} Profile">
                    @else
                        <span class="text-blue-600 font-bold text-2xl">
                            {{ strtoupper(substr($serviceRequest->user->name ?? 'U', 0, 1)) }}
                        </span>
                    @endif
                </div>
                
                <div>
                    <h4 class="text-lg font-bold text-slate-800">{{ $serviceRequest->user->name ?? 'Unknown User' }}</h4>
                    <p class="text-sm text-slate-500">{{ $serviceRequest->user->email ?? 'No email provided' }}</p>
                </div>

            </div>

            <div class="space-y-3">
                <div class="flex justify-between border-b border-slate-50 pb-2">
                    <span class="text-slate-500">Contact Number</span>
                    <span class="font-medium text-slate-800">{{ $serviceRequest->contact ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between pt-1 items-center">
                    <span class="text-slate-500">Current Status</span>
                    <span class="px-3 py-1 text-xs font-bold rounded-full 
                        {{ $serviceRequest->status === 'solved' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ strtoupper($serviceRequest->status ?? 'PENDING') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4">Request Details</h3>
            <div class="space-y-3">
                <div class="flex justify-between border-b border-slate-50 pb-2">
                    <span class="text-slate-500">Problem Type</span>
                    <span class="font-semibold text-slate-800">{{ $serviceRequest->problem_type }}</span>
                </div>
                <div class="flex flex-col border-b border-slate-50 pb-2">
                    <span class="text-slate-500 mb-1">Address</span>
                    <span class="text-sm text-slate-800">{{ $serviceRequest->address }}</span>
                </div>
                <div class="flex justify-between pt-1">
                    <span class="text-slate-500">GPS Coordinates</span>
                    <span class="text-xs font-mono text-slate-600 bg-slate-100 px-2 py-1 rounded">
                        {{ $serviceRequest->gps_lat ?? 'N/A' }}, {{ $serviceRequest->gps_lng ?? 'N/A' }}
                    </span>
                </div>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4">Description</h3>
            <p class="text-slate-700 whitespace-pre-line bg-slate-50 p-4 rounded-xl text-sm">
                {{ $serviceRequest->description ?: 'No description provided.' }}
            </p>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 space-y-6">
            <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-2">Attached Media</h3>
            
            @if($serviceRequest->audio_path)
            <div>
                <span class="text-xs font-semibold text-slate-500 mb-1 block">Audio Recording</span>
                <audio controls class="w-full h-10 rounded">
                    <source src="{{ asset($serviceRequest->audio_path) }}">
                    Your browser does not support the audio element.
                </audio>
            </div>
            @endif

            @if($serviceRequest->video_path)
            <div>
                <span class="text-xs font-semibold text-slate-500 mb-1 block">Video Evidence</span>
                <div class="relative w-full aspect-video bg-black rounded-xl overflow-hidden">
                    <video controls class="absolute inset-0 w-full h-full object-contain">
                        <source src="{{ asset($serviceRequest->video_path) }}">
                    </video>
                </div>
            </div>
            @endif

            @if(!$serviceRequest->audio_path && !$serviceRequest->video_path)
                <p class="text-sm text-slate-400 italic">No media attached to this request.</p>
            @endif
        </div>
    </div>

    @if($serviceRequest->gps_lat && $serviceRequest->gps_lng)
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Location Map</h3>
            
            <div id="distance-container" class="hidden bg-blue-50 border border-blue-200 text-blue-800 px-4 py-2 rounded-lg text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span id="distance-text">Calculating distance...</span>
            </div>
            <div id="location-error" class="hidden text-xs text-red-600 bg-red-50 px-3 py-1 rounded"></div>
        </div>
        
        <div id="map" class="h-96 w-full z-0"></div>
    </div>
    @else
    <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-xl text-yellow-800 text-sm">
        Map cannot be displayed because GPS coordinates are missing for this request.
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mt-8">
        <div class="p-6 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-800">History for {{ $serviceRequest->user->name ?? 'User' }}</h3>
            <p class="text-sm text-slate-500">Other requests submitted by this user.</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                        <th class="p-4 font-semibold">ID</th>
                        <th class="p-4 font-semibold">Date</th>
                        <th class="p-4 font-semibold">Problem Type</th>
                        <th class="p-4 font-semibold">Status</th>
                        <th class="p-4 font-semibold text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($previousRequests as $prev)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="p-4 text-sm text-slate-800">#{{ $prev->id }}</td>
                        <td class="p-4 text-sm text-slate-500">{{ $prev->created_at->format('M d, Y h:i A') }}</td>
                        <td class="p-4 text-sm text-slate-800">{{ $prev->problem_type }}</td>
                        <td class="p-4">
                            <span class="px-2 py-1 text-xs font-bold rounded-full 
                                {{ $prev->status === 'solved' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ strtoupper($prev->status) }}
                            </span>
                        </td>
                        <td class="p-4 text-right">
                            <a href="{{ route('admin.requests.show', $prev->id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                                View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-400 text-sm">
                            No previous requests found for this user.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        // Ensure lat/lng exist before attempting to render the map
        const targetLat = {{ $serviceRequest->gps_lat ?? 'null' }};
        const targetLng = {{ $serviceRequest->gps_lng ?? 'null' }};

        if (targetLat === null || targetLng === null) {
            return; // Abort map initialization
        }

        // 1. Initialize Leaflet Map
        const map = L.map('map').setView([targetLat, targetLng], 14);

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        // Add marker for the Service Request
        const targetMarker = L.marker([targetLat, targetLng]).addTo(map)
            .bindPopup('<b>Service Request Location</b><br>{{ $serviceRequest->problem_type }}').openPopup();

        // 2. Geolocation logic
        const distContainer = document.getElementById('distance-container');
        const distText = document.getElementById('distance-text');
        const errContainer = document.getElementById('location-error');

        if ("geolocation" in navigator) {
            distContainer.classList.remove('hidden');
            
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const adminLat = position.coords.latitude;
                    const adminLng = position.coords.longitude;
                    const accuracy = position.coords.accuracy; // Note: Desktop accuracy is often poor

                    // Add marker for Admin
                    const adminMarker = L.marker([adminLat, adminLng], {
                        icon: L.icon({
                            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                            iconSize: [25, 41],
                            iconAnchor: [12, 41],
                            popupAnchor: [1, -34],
                            shadowSize: [41, 41]
                        })
                    }).addTo(map).bindPopup('<b>Your Location</b><br>Accuracy: ~' + Math.round(accuracy) + 'm');

                    // Calculate distance using Leaflet's built-in Haversine implementation
                    const targetLatLng = L.latLng(targetLat, targetLng);
                    const adminLatLng = L.latLng(adminLat, adminLng);
                    const distanceMeters = adminLatLng.distanceTo(targetLatLng);
                    
                    const distanceKm = (distanceMeters / 1000).toFixed(2);
                    distText.innerHTML = `Distance: <b>${distanceKm} km</b> away`;

                    // Draw a line between the two points
                    const latlngs = [adminLatLng, targetLatLng];
                    const polyline = L.polyline(latlngs, {color: 'red', dashArray: '5, 5'}).addTo(map);

                    // Adjust map bounds to show both markers
                    map.fitBounds(polyline.getBounds(), { padding: [50, 50] });
                },
                function(error) {
                    // Handle Geolocation errors objectively
                    distContainer.classList.add('hidden');
                    errContainer.classList.remove('hidden');
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errContainer.innerText = "Distance calculation unavailable: Location permission denied.";
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errContainer.innerText = "Distance calculation unavailable: Location information is unavailable.";
                            break;
                        case error.TIMEOUT:
                            errContainer.innerText = "Distance calculation unavailable: The request timed out.";
                            break;
                        default:
                            errContainer.innerText = "Distance calculation unavailable: An unknown error occurred.";
                            break;
                    }
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        } else {
            errContainer.classList.remove('hidden');
            errContainer.innerText = "Geolocation is not supported by your browser.";
        }
    });
</script>
@endsection