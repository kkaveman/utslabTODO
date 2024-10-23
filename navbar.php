<!-- navbar.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="bg-indigo-600">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <!-- Logo and Desktop Menu -->
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h2 class="text-white text-lg"><b><i>ToDO</i></b></h2>
                    </div>
                    <div class="hidden md:block">
                        <div class="ml-10 flex items-baseline space-x-4">
                            <a href="dashboard.php" class="bg-indigo-700 text-white rounded-md px-3 py-2 text-sm font-medium">Dashboard</a>
                            <a href="profile.php" class="text-white hover:bg-indigo-500 hover:bg-opacity-75 rounded-md px-3 py-2 text-sm font-medium">Profile</a>
                        </div>
                    </div>
                </div>

                <!-- Desktop Logout Button -->
                <div class="hidden md:block">
                    <div class="ml-4 flex items-center md:ml-6">
                        <a href="logout.php" class="rounded-full bg-indigo-600 p-1 text-indigo-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-indigo-600">
                            <span class="sr-only">Logout</span>
                            <i class="fas fa-sign-out-alt w-6 h-6"></i>
                        </a>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <button onclick="toggleMenu()" class="md:hidden text-white">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden">
                <div class="space-y-1 px-2 pb-3 pt-2">
                    <a href="dashboard.php" class="block text-white hover:bg-indigo-500 hover:bg-opacity-75 rounded-md px-3 py-2 text-base font-medium">Dashboard</a>
                    <a href="profile.php" class="block text-white hover:bg-indigo-500 hover:bg-opacity-75 rounded-md px-3 py-2 text-base font-medium">Profile</a>
                    <a href="logout.php" class="block text-white hover:bg-indigo-500 hover:bg-opacity-75 rounded-md px-3 py-2 text-base font-medium">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <script>
        function toggleMenu() {
            const mobileMenu = document.getElementById('mobile-menu');
            if (mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.remove('hidden');
            } else {
                mobileMenu.classList.add('hidden');
            }
        }
    </script>
</body>
</html>