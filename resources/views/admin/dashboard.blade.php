@extends('layouts.header')

@section('content')

<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 p-6 relative">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <h1 class="text-2xl font-bold text-slate-800">Service Requests</h1>

        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Search name / phone / problem..."
                class="px-4 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-200 focus:outline-none">

            <button type="submit" class="bg-blue-700 text-white px-4 py-2 rounded-xl text-sm hover:bg-blue-800 transition-colors cursor-pointer">
                Search
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($requests as $r)
        <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 border border-slate-200 overflow-hidden flex flex-col">

            <div class="h-2 w-full {{ $r->status == 'solved' ? 'bg-green-500' : 'bg-yellow-500' }}"></div>

            <div class="p-4 flex flex-col flex-1">

                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full overflow-hidden border-2 border-blue-200 bg-gray-100 flex items-center justify-center shrink-0">
                        @if($r->user && $r->user->profile_image)
                            <img src="{{ asset($r->user->profile_image) }}" class="w-full h-full object-cover" alt="Profile">
                        @else
                            <span class="text-blue-600 font-bold text-lg">
                                {{ strtoupper(substr($r->user->name ?? 'U', 0, 1)) }}
                            </span>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-800 truncate">
                            {{ $r->user->name ?? 'Unknown User' }}
                        </p>
                        <p class="text-xs text-slate-500 truncate">
                            {{ $r->user->email ?? 'No email' }}
                        </p>
                        <p class="text-xs text-slate-400 truncate">
                            {{ $r->user->phone ?? 'No phone' }}
                        </p>
                    </div>

                    <span class="text-xs px-2 py-1 rounded-full font-semibold shrink-0 {{ $r->status == 'solved' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ strtoupper($r->status) }}
                    </span>
                </div>

                <div class="mt-4 flex-1">
                    <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-3 border border-blue-100 h-full">
                        <p class="text-sm font-semibold text-slate-700">
                            {{ $r->problem_type }}
                        </p>
                        <p class="text-xs text-slate-600 mt-1 line-clamp-2" title="{{ $r->description }}">
                            {{ $r->description ?? 'No description provided' }}
                        </p>
                    </div>
                </div>

                <div class="mt-3">
                    <p class="text-xs text-slate-400 truncate" title="{{ $r->address }}">
                        📍 {{ \Illuminate\Support\Str::limit($r->address, 80) }}
                    </p>
                </div>

                <div class="flex justify-between items-center mt-4">
                    <div class="flex gap-2">
                        @if($r->audio_path)
                        <button onclick="openAudio('{{ asset($r->audio_path) }}')" class="bg-blue-100 hover:bg-blue-200 text-blue-700 p-2 rounded-lg transition-colors cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-400" aria-label="Play Audio">
                            <i class="fas fa-volume-up"></i>
                        </button>
                        @endif

                        @if($r->video_path)
                        <button onclick="openVideo('{{ asset($r->video_path) }}')" class="bg-purple-100 hover:bg-purple-200 text-purple-700 p-2 rounded-lg transition-colors cursor-pointer focus:outline-none focus:ring-2 focus:ring-purple-400" aria-label="Play Video">
                            <i class="fas fa-video"></i>
                        </button>
                        @endif
                    </div>

                    <a href="{{ route('admin.requests.show', $r->id) }}" class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-400">
                       View Details
                    </a>
                </div>

                <div class="mt-3 text-right text-xs text-slate-400">
                    {{ $r->created_at->diffForHumans() }}
                </div>

            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $requests->links() }}
    </div>

</div>

<div id="audioModal" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm hidden items-center justify-center z-[100] p-4 transition-all duration-300">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm relative animate-fadeIn overflow-hidden border border-slate-200">
        
        <button onclick="closeAudio()" class="absolute top-4 right-4 z-20 text-white/80 hover:text-white transition-colors cursor-pointer focus:outline-none" aria-label="Close Audio Modal">
            <i class="fas fa-times text-lg drop-shadow-md"></i>
        </button>

        <div class="h-32 bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center relative overflow-hidden">
            <div class="absolute inset-0 flex items-center justify-center opacity-20">
                <div class="w-48 h-48 border border-white rounded-full animate-ping" style="animation-duration: 3s;"></div>
            </div>
            <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center z-10">
                 <i class="fas fa-microphone-alt text-2xl text-white"></i>
            </div>
        </div>

        <div class="p-6">
            <p class="text-center text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4">Voice Note Recording</p>
            <audio id="audioPlayer" controls class="w-full focus:outline-none"></audio>
        </div>
        
    </div>
</div>

<div id="videoModal" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm hidden items-center justify-center z-[100] p-4 transition-all duration-300">
    <div class="bg-black rounded-2xl shadow-2xl w-full max-w-3xl relative animate-fadeIn overflow-hidden ring-1 ring-slate-700/50">
        
        <button onclick="closeVideo()" class="absolute top-4 right-4 z-20 bg-black/40 hover:bg-black/80 text-white rounded-full w-8 h-8 flex items-center justify-center backdrop-blur-md transition-all cursor-pointer focus:outline-none" aria-label="Close Video Modal">
            <i class="fas fa-times"></i>
        </button>
        
        <div class="relative w-full aspect-video bg-slate-950 flex items-center justify-center">
            <video id="videoPlayer" controls class="absolute inset-0 w-full h-full object-contain focus:outline-none"></video>
        </div>
        
    </div>
</div>

<style>
.animate-fadeIn {
    animation: fadeIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
@keyframes fadeIn {
    from { 
        opacity: 0; 
        transform: translateY(10px) scale(0.98);
    }
    to { 
        opacity: 1; 
        transform: translateY(0) scale(1);
    }
}
/* Utility class to hide elements visually but keep them accessible if needed, though 'hidden' handles the display */
.modal-active {
    overflow: hidden; /* Prevents background scrolling when modal is open */
}
</style>

<script>
// Prevent background scrolling when modals are open
function toggleBodyScroll(disable) {
    if (disable) {
        document.body.classList.add('modal-active');
    } else {
        document.body.classList.remove('modal-active');
    }
}

function openAudio(src) {
    const modal = document.getElementById('audioModal');
    const player = document.getElementById('audioPlayer');
    
    player.src = src;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    toggleBodyScroll(true);
    
    // Attempt autoplay, catch rejection if browser policies block it
    player.play().catch(e => console.warn("Audio autoplay blocked by browser policy."));
}

function closeAudio() {
    const modal = document.getElementById('audioModal');
    const player = document.getElementById('audioPlayer');
    
    player.pause();
    player.currentTime = 0; // Reset progress
    player.src = ''; // Clear source to stop downloading
    
    modal.classList.remove('flex');
    modal.classList.add('hidden');
    toggleBodyScroll(false);
}

function openVideo(src) {
    const modal = document.getElementById('videoModal');
    const player = document.getElementById('videoPlayer');
    
    player.src = src;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    toggleBodyScroll(true);
    
    // Attempt autoplay, catch rejection if browser policies block it
    player.play().catch(e => console.warn("Video autoplay blocked by browser policy."));
}

function closeVideo() {
    const modal = document.getElementById('videoModal');
    const player = document.getElementById('videoPlayer');
    
    player.pause();
    player.currentTime = 0; // Reset progress
    player.src = ''; // Clear source to stop downloading
    
    modal.classList.remove('flex');
    modal.classList.add('hidden');
    toggleBodyScroll(false);
}

// Close modals if clicking outside the modal content (on the backdrop)
document.getElementById('audioModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAudio();
    }
});

document.getElementById('videoModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeVideo();
    }
});

// Allow escaping modals via keyboard
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        if (!document.getElementById('videoModal').classList.contains('hidden')) {
            closeVideo();
        }
        if (!document.getElementById('audioModal').classList.contains('hidden')) {
            closeAudio();
        }
    }
});
</script>

@endsection