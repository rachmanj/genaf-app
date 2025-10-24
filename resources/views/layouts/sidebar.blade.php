<!-- Sidebar -->
<div class="drawer-side">
    <label for="drawer-toggle" aria-label="close sidebar" class="drawer-overlay"></label>

    <aside
        class="min-h-full w-80 bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 border-r border-slate-700/50 shadow-2xl backdrop-blur-sm">
        <!-- Sidebar Header -->
        <div class="p-6 border-b border-slate-700/30">
            <div class="flex items-center space-x-4">
                <div class="relative group">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-blue-500 via-purple-600 to-indigo-700 rounded-2xl flex items-center justify-center shadow-xl group-hover:shadow-2xl transition-all duration-300">
                        <span class="text-white font-bold text-2xl">G</span>
                    </div>
                    <div
                        class="absolute -top-1 -right-1 w-5 h-5 bg-emerald-500 rounded-full border-3 border-slate-900 animate-pulse shadow-lg">
                    </div>
                    <div
                        class="absolute inset-0 bg-gradient-to-br from-blue-400 to-purple-600 rounded-2xl opacity-0 group-hover:opacity-20 transition-opacity duration-300">
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl font-bold text-white tracking-tight">GENAF</h1>
                    <p class="text-sm text-slate-400 font-medium">Enterprise Platform</p>
                    <div class="flex items-center mt-1">
                        <div class="w-2 h-2 bg-emerald-500 rounded-full mr-2 animate-pulse"></div>
                        <span class="text-xs text-emerald-400 font-medium">System Online</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <div class="px-4 py-6 space-y-1 overflow-y-auto">
            <!-- Dashboard -->
            <div class="mb-2">
                <a href="{{ route('dashboard') }}"
                    class="flex items-center px-4 py-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('dashboard') ? 'bg-blue-600/90 text-white shadow-lg backdrop-blur-sm' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
                    <div
                        class="flex items-center justify-center w-11 h-11 rounded-xl {{ request()->routeIs('dashboard') ? 'bg-blue-500/80 backdrop-blur-sm' : 'bg-slate-700/80 group-hover:bg-slate-600/80' }} transition-all duration-300 backdrop-blur-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        </svg>
                    </div>
                    <span class="ml-4 font-semibold text-sm">Dashboard</span>
                </a>
            </div>

            <!-- User Management -->
            @can('view users')
                <div class="mb-2">
                    <a href="{{ route('users.index') }}"
                        class="flex items-center px-4 py-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('users.*') ? 'bg-blue-600/90 text-white shadow-lg backdrop-blur-sm' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
                        <div
                            class="flex items-center justify-center w-11 h-11 rounded-xl {{ request()->routeIs('users.*') ? 'bg-blue-500/80 backdrop-blur-sm' : 'bg-slate-700/80 group-hover:bg-slate-600/80' }} transition-all duration-300 backdrop-blur-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                                </path>
                            </svg>
                        </div>
                        <span class="ml-4 font-semibold text-sm">User Management</span>
                    </a>
                </div>
            @endcan

            <!-- Operations Section -->
            <div class="mb-4">
                <div class="px-4 py-2 mb-3">
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Operations</h3>
                </div>

                <!-- Office Supplies -->
                @can('view supplies')
                    <div class="mb-2">
                        <a href="#"
                            class="flex items-center px-4 py-3 rounded-xl transition-all duration-300 group text-slate-300 hover:bg-slate-700/60 hover:text-white">
                            <div
                                class="flex items-center justify-center w-11 h-11 rounded-xl bg-slate-700/80 group-hover:bg-slate-600/80 transition-all duration-300 backdrop-blur-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <span class="ml-4 font-semibold text-sm">Office Supplies</span>
                            <div class="ml-auto">
                                <span
                                    class="px-2 py-1 text-xs bg-orange-500/20 text-orange-400 rounded-full font-medium">New</span>
                            </div>
                        </a>
                    </div>
                @endcan

                <!-- Ticket Reservations -->
                @can('view ticket reservations')
                    <div class="mb-2">
                        <a href="#"
                            class="flex items-center px-4 py-3 rounded-xl transition-all duration-300 group text-slate-300 hover:bg-slate-700/60 hover:text-white">
                            <div
                                class="flex items-center justify-center w-11 h-11 rounded-xl bg-slate-700/80 group-hover:bg-slate-600/80 transition-all duration-300 backdrop-blur-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
                                    </path>
                                </svg>
                            </div>
                            <span class="ml-4 font-semibold text-sm">Ticket Reservations</span>
                        </a>
                    </div>
                @endcan

                <!-- Property Management -->
                @can('view rooms')
                    <div class="mb-2">
                        <a href="#"
                            class="flex items-center px-4 py-3 rounded-xl transition-all duration-300 group text-slate-300 hover:bg-slate-700/60 hover:text-white">
                            <div
                                class="flex items-center justify-center w-11 h-11 rounded-xl bg-slate-700/80 group-hover:bg-slate-600/80 transition-all duration-300 backdrop-blur-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                    </path>
                                </svg>
                            </div>
                            <span class="ml-4 font-semibold text-sm">Property Management</span>
                        </a>
                    </div>
                @endcan

                <!-- Vehicle Administration -->
                @can('view vehicles')
                    <div class="mb-2">
                        <a href="#"
                            class="flex items-center px-4 py-3 rounded-xl transition-all duration-300 group text-slate-300 hover:bg-slate-700/60 hover:text-white">
                            <div
                                class="flex items-center justify-center w-11 h-11 rounded-xl bg-slate-700/80 group-hover:bg-slate-600/80 transition-all duration-300 backdrop-blur-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2">
                                    </path>
                                </svg>
                            </div>
                            <span class="ml-4 font-semibold text-sm">Vehicle Administration</span>
                        </a>
                    </div>
                @endcan

                <!-- Asset Inventory -->
                @can('view assets')
                    <div class="mb-2">
                        <a href="#"
                            class="flex items-center px-4 py-3 rounded-xl transition-all duration-300 group text-slate-300 hover:bg-slate-700/60 hover:text-white">
                            <div
                                class="flex items-center justify-center w-11 h-11 rounded-xl bg-slate-700/80 group-hover:bg-slate-600/80 transition-all duration-300 backdrop-blur-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <span class="ml-4 font-semibold text-sm">Asset Inventory</span>
                        </a>
                    </div>
                @endcan
            </div>

            <!-- Analytics Section -->
            <div class="mb-4">
                <div class="px-4 py-2 mb-3">
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Analytics</h3>
                </div>

                <!-- Reports -->
                @can('view reports')
                    <div class="mb-2">
                        <a href="#"
                            class="flex items-center px-4 py-3 rounded-xl transition-all duration-300 group text-slate-300 hover:bg-slate-700/60 hover:text-white">
                            <div
                                class="flex items-center justify-center w-11 h-11 rounded-xl bg-slate-700/80 group-hover:bg-slate-600/80 transition-all duration-300 backdrop-blur-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                    </path>
                                </svg>
                            </div>
                            <span class="ml-4 font-semibold text-sm">Reports & Analytics</span>
                        </a>
                    </div>
                @endcan
            </div>

            <!-- System Section -->
            <div class="mb-4">
                <div class="px-4 py-2 mb-3">
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">System</h3>
                </div>

                <!-- System Settings -->
                @can('view system settings')
                    <div class="mb-2">
                        <a href="#"
                            class="flex items-center px-4 py-3 rounded-xl transition-all duration-300 group text-slate-300 hover:bg-slate-700/60 hover:text-white">
                            <div
                                class="flex items-center justify-center w-11 h-11 rounded-xl bg-slate-700/80 group-hover:bg-slate-600/80 transition-all duration-300 backdrop-blur-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <span class="ml-4 font-semibold text-sm">System Settings</span>
                        </a>
                    </div>
                @endcan
            </div>
        </div>

        <!-- Sidebar Footer -->
        <div
            class="absolute bottom-0 left-0 right-0 p-6 border-t border-slate-700/30 bg-slate-900/50 backdrop-blur-sm">
            <!-- Dark Mode Toggle -->
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-semibold text-slate-300">Theme</span>
                <button onclick="toggleTheme()"
                    class="relative inline-flex h-6 w-11 items-center rounded-full bg-slate-600 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-900">
                    <span
                        class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform translate-x-1 shadow-lg"></span>
                </button>
            </div>

            <!-- User Info -->
            <div class="space-y-3">
                <div
                    class="flex items-center space-x-3 p-4 bg-slate-800/60 rounded-2xl border border-slate-700/50 backdrop-blur-sm">
                    <div class="relative">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                            <span class="text-white font-bold text-sm">{{ substr(Auth::user()->name, 0, 2) }}</span>
                        </div>
                        <div
                            class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 rounded-full border-2 border-slate-800 shadow-lg">
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ Auth::user()->email }}</p>
                    </div>
                    <div class="flex flex-col space-y-1">
                        @foreach (Auth::user()->roles as $role)
                            <span
                                class="px-2 py-1 text-xs font-bold rounded-lg
                                @if ($role->name === 'admin') bg-red-500/20 text-red-400 border border-red-500/30
                                @elseif($role->name === 'manager') bg-yellow-500/20 text-yellow-400 border border-yellow-500/30
                                @else bg-green-500/20 text-green-400 border border-green-500/30 @endif">
                                {{ ucfirst($role->name) }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <!-- Logout Button -->
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center justify-center px-4 py-3 bg-red-600/20 hover:bg-red-600/30 text-red-400 hover:text-red-300 rounded-xl border border-red-500/30 hover:border-red-500/50 transition-all duration-200 group">
                        <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform duration-200"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                        <span class="font-semibold text-sm">Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>
</div>
