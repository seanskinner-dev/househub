<nav x-data="{ open: false }" class="bg-white border-b border-gray-200">

    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <!-- LEFT SIDE -->
            <div class="flex">

                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="/points">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- NAV LINKS -->
                <div class="hidden sm:flex sm:items-center sm:ms-10 space-x-6">

                    <a href="/points" class="nav-link">Points</a>
                    <a href="/dashboard" class="nav-link">Dashboard</a>

                    <span class="divider">|</span>

                    <a href="/tv" class="nav-link">TV</a>
                    <a href="/tv/house-race" class="nav-link">Race</a>
                    <a href="/tv/house-momentum" class="nav-link">Momentum</a>
                    <a href="/tv/top-students" class="nav-link">Top Students</a>
                    <a href="/tv/teachers" class="nav-link">Teachers</a>

                    <span class="divider">|</span>

                    <a href="/tv/house-trends" class="nav-link">Trends</a>
                    <a href="/tv/house-month" class="nav-link">Month</a>

                </div>
            </div>

            <!-- RIGHT SIDE -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">

                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-600 bg-white hover:text-gray-900 transition">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                          clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Profile
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Log Out
                            </x-dropdown-link>
                        </form>
                    </x-slot>

                </x-dropdown>
            </div>

            <!-- MOBILE MENU BUTTON -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open }"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': !open, 'inline-flex': open }"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

        </div>
    </div>

    <!-- MOBILE MENU -->
    <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1 px-4">

            <a href="/points" class="mobile-link">Points</a>
            <a href="/dashboard" class="mobile-link">Dashboard</a>

            <a href="/tv" class="mobile-link">TV</a>
            <a href="/tv/house-race" class="mobile-link">Race</a>
            <a href="/tv/house-momentum" class="mobile-link">Momentum</a>
            <a href="/tv/top-students" class="mobile-link">Top Students</a>
            <a href="/tv/teachers" class="mobile-link">Teachers</a>

            <a href="/tv/house-trends" class="mobile-link">Trends</a>
            <a href="/tv/house-month" class="mobile-link">Month</a>

        </div>
    </div>

</nav>

<style>
.nav-link {
    color: #374151;
    text-decoration: none;
    font-weight: 600;
    position: relative;
    padding-bottom: 4px;
}

.nav-link:hover {
    color: #111827;
}

.nav-link::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0%;
    height: 2px;
    background: #38bdf8;
    transition: width 0.3s ease;
}

.nav-link:hover::after {
    width: 100%;
}

.divider {
    color: #9ca3af;
}

.mobile-link {
    display: block;
    padding: 8px 0;
    font-weight: 600;
    color: #374151;
}
</style>