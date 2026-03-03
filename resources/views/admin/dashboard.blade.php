@extends('layouts.header')

@section('content')

<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 p-6">

    <!-- TOP BAR -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <h1 class="text-2xl font-bold text-slate-800">Service Requests</h1>

        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Search name / phone / problem..."
                class="px-4 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-200">

            <button class="bg-blue-700 text-white px-4 py-2 rounded-xl text-sm hover:bg-blue-800 transition">
                Search
            </button>
        </form>
    </div>

    <!-- GRID -->
   <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

@foreach($requests as $r)
<div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 border border-slate-200 overflow-hidden">

    <!-- STATUS STRIP -->
    <div class="h-2 
        {{ $r->status == 'solved' ? 'bg-green-500' : 'bg-yellow-500' }}">
    </div>

    <div class="p-4">

        <!-- USER INFO -->
        <div class="flex items-center gap-3">

            <!-- PROFILE PHOTO -->
            <div class="w-12 h-12 rounded-full overflow-hidden border-2 border-blue-200 bg-gray-100 flex items-center justify-center">
                @if($r->user && $r->user->profile_image)
                    <img src="{{ asset($r->user->profile_image) }}"
                         class="w-full h-full object-cover">
                @else
                    <span class="text-blue-600 font-bold text-lg">
                        {{ strtoupper(substr($r->user->name ?? 'U',0,1)) }}
                    </span>
                @endif
            </div>

            <!-- NAME + EMAIL -->
            <div class="flex-1">
                <p class="text-sm font-bold text-slate-800">
                    {{ $r->user->name ?? 'Unknown User' }}
                </p>
                <p class="text-xs text-slate-500">
                    {{ $r->user->email ?? 'No email' }}
                </p>
                <p class="text-xs text-slate-400">
                    {{ $r->user->phone ?? 'No phone' }}
                </p>
            </div>

            <!-- STATUS BADGE -->
            <span class="text-xs px-2 py-1 rounded-full font-semibold
                {{ $r->status == 'solved'
                    ? 'bg-green-100 text-green-700'
                    : 'bg-yellow-100 text-yellow-700' }}">
                {{ strtoupper($r->status) }}
            </span>
        </div>

        <!-- PROBLEM SECTION -->
        <div class="mt-4">

            <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-3 border border-blue-100">
                <p class="text-sm font-semibold text-slate-700">
                    {{ $r->problem_type }}
                </p>

                <p class="text-xs text-slate-600 mt-1 line-clamp-2">
                    {{ $r->description ?? 'No description provided' }}
                </p>
            </div>

        </div>

        <!-- ADDRESS -->
        <div class="mt-3">
            <p class="text-xs text-slate-400">
                📍 {{ \Illuminate\Support\Str::limit($r->address, 80) }}
            </p>
        </div>

        <!-- ACTION ROW -->
        <div class="flex justify-between items-center mt-4">

            <!-- MEDIA ICONS -->
            <div class="flex gap-2">

                @if($r->audio_path)
                <button onclick="openAudio('{{ asset($r->audio_path) }}')"
                    class="bg-blue-100 hover:bg-blue-200 text-blue-700 p-2 rounded-lg transition">
                    <i class="fas fa-volume-up"></i>
                </button>
                @endif

                @if($r->video_path)
                <button onclick="openVideo('{{ asset($r->video_path) }}')"
                    class="bg-purple-100 hover:bg-purple-200 text-purple-700 p-2 rounded-lg transition">
                    <i class="fas fa-video"></i>
                </button>
                @endif

            </div>

            <!-- VIEW DETAIL BUTTON -->
            <a href="{{ route('admin.requests.show',$r->id) }}"
               class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg transition">
               View
            </a>
        </div>

        <!-- TIME -->
        <div class="mt-3 text-right text-xs text-slate-400">
            {{ $r->created_at->diffForHumans() }}
        </div>

    </div>
</div>
@endforeach

</div>
    <!-- PAGINATION -->
    <div class="mt-6">
        {{ $requests->links() }}
    </div>

</div>


<!-- AUDIO MODAL -->
<div id="audioModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md relative animate-fadeIn">
        <button onclick="closeAudio()" class="absolute top-3 right-3 text-gray-500">✖</button>
        <audio id="audioPlayer" controls class="w-full mt-6"></audio>
    </div>
</div>

<!-- VIDEO MODAL -->
<div id="videoModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-xl relative animate-fadeIn">
        <button onclick="closeVideo()" class="absolute top-3 right-3 text-gray-500">✖</button>
        <video id="videoPlayer" controls class="w-full mt-6 rounded-xl"></video>
    </div>
</div>

<style>
.animate-fadeIn {
    animation: fadeIn 0.3s ease;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px);}
    to { opacity: 1; transform: translateY(0);}
}
</style>

<script>
function openAudio(src){
    const modal = document.getElementById('audioModal');
    const player = document.getElementById('audioPlayer');
    player.src = src;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeAudio(){
    const modal = document.getElementById('audioModal');
    const player = document.getElementById('audioPlayer');
    player.pause();
    player.src = '';
    modal.classList.add('hidden');
}

function openVideo(src){
    const modal = document.getElementById('videoModal');
    const player = document.getElementById('videoPlayer');
    player.src = src;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeVideo(){
    const modal = document.getElementById('videoModal');
    const player = document.getElementById('videoPlayer');
    player.pause();
    player.src = '';
    modal.classList.add('hidden');
}
</script>

@endsection