<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Portal')</title>

    @vite('resources/css/app.css')
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

    <style>
        /* Card animation */
        .fade-slide {
            animation: fadeSlide 0.7s ease forwards;
        }

        @keyframes fadeSlide {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Autofill password animation */
        .typed-animation {
            letter-spacing: 3px;
            animation: typing 0.4s steps(1) 3;
        }

        @keyframes typing {
            0% { opacity: 0.5; }
            50% { opacity: 1; }
            100% { opacity: 0.5; }
        }
    </style>
</head>

<body class="antialiased bg-gray-50 text-gray-900">

    <!-- HEADER -->
    <header class="fixed inset-x-0 top-0 h-16 bg-white shadow-lg z-50">
        <div class="h-full px-4 md:px-6 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-2">
                <img src="{{ asset('asset/logo.png') }}" class="h-10 w-auto" alt="Logo">
                <span class="text-lg md:text-xl font-bold text-blue-900">MISTRI</span>
            </a>
        </div>
    </header>

    <!-- MAIN WRAPPER -->
    <div class="min-h-screen flex flex-col pt-16">

        <div
            class="flex-grow flex items-center justify-center bg-gradient-to-r from-slate-100 via-blue-100 to-slate-200 p-4">

            <div
                class="w-full max-w-md bg-white p-8 rounded-2xl shadow-xl border border-slate-200 fade-slide min-h-[520px]">

                <!-- Heading -->
                <div class="text-center mb-6">
                    <h1 class="text-3xl font-bold text-blue-900">Admin Login</h1>
                    <p class="text-sm text-slate-600 mt-2">
                        Use your admin email and password to access the dashboard.
                    </p>
                </div>

                <!-- Session Error -->
                @if(session('error'))
                    <div
                        class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded-lg mb-4 text-sm animate-pulse">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('admin.login') }}" class="space-y-5">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium mb-1 text-slate-700">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full rounded-lg border border-slate-300 focus:border-blue-500 focus:ring focus:ring-blue-200 p-3 text-sm transition"
                            placeholder="admin@example.com" />
                        @error('email')
                            <p class="text-red-600 text-xs mt-1 animate-pulse">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-medium mb-1 text-slate-700">Password</label>
                        <div class="relative">
                            <input type="password" id="passwordField" name="password" required
                                class="w-full rounded-lg border border-slate-300 focus:border-blue-500 focus:ring focus:ring-blue-200 p-3 text-sm transition"
                                placeholder="••••••••" />
                            <i class="fa fa-eye absolute right-3 top-3 cursor-pointer text-gray-400"
                                onclick="togglePassword()"></i>
                        </div>
                        @error('password')
                            <p class="text-red-600 text-xs mt-1 animate-pulse">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full bg-blue-700 text-white py-3 rounded-lg hover:bg-blue-800 transition duration-300 shadow-md hover:scale-[1.02]">
                        Sign In
                    </button>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white text-center py-4">
            <p class="text-sm">© {{ date('Y') }} IOE Purwanchal Campus. All Rights Reserved.</p>
            <p class="text-sm mt-1">
                Developed by
                <a href="https://sadanandpaneru.com.np"
                    class="underline hover:text-gray-300">Sadanand Paneru</a>
            </p>
        </footer>
    </div>

    <!-- JS -->
    <script>
        function togglePassword() {
            const field = document.getElementById("passwordField");
            field.type = field.type === "password" ? "text" : "password";
        }

        // Detect autofill and show typing animation
        window.addEventListener("load", function () {
            const passField = document.getElementById("passwordField");

            setTimeout(() => {
                if (passField.value !== "") {
                    passField.classList.add("typed-animation");
                }
            }, 500);
        });
    </script>

</body>

</html>