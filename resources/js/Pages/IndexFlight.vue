<template>
    <main class="bg-gradient-to-br from-blue-50 via-white to-indigo-50 min-h-screen p-4 md:p-8" role="main">
        <!-- Header -->
        <div class="max-w-6xl mx-auto mb-8 md:mb-12">
            <h1 class="text-4xl md:text-6xl font-bold text-slate-800 mb-6 md:mb-16 tracking-tight">
                Search hundreds of <span
                    class="text-blue-600 relative after:absolute after:bottom-0 after:left-0 after:w-full after:h-1 after:bg-blue-100">flight
                    sites</span> at once.
            </h1>

            <!-- Flight Search Container -->
            <div class="bg-white/95 backdrop-blur-sm shadow-lg border border-gray-100 rounded-xl overflow-visible relative pb-8"
                role="search" aria-label="Flight search form">
                <!-- Trip Type and Cabin Class Selectors -->
                <div class="border-b relative bg-gradient-to-r from-amber-50 to-yellow-50 flex">
                    <!-- Trip Type Selector -->
                    <div class="relative border-r px-6 py-2">
                        <label for="tripType" class="block text-xs text-gray-500 mb-1">Trip</label>
                        <select id="tripType"
                            class="appearance-none bg-transparent border-none font-medium text-lg text-gray-800 pr-8 focus:outline-none focus:ring-0 cursor-pointer">
                            <option value="oneway">One-way</option>
                            <option value="roundtrip" selected>Round-trip</option>
                            <option value="multicity">Multi-city</option>
                        </select>
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 pt-2 text-amber-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>

                    <!-- Cabin Class Selector -->
                    <div class="relative px-6 py-2">
                        <label for="cabinClass" class="block text-xs text-gray-500 mb-1">Cabin class</label>
                        <select id="cabinClass"
                            class="appearance-none bg-transparent border-none font-medium text-lg text-gray-800 pr-8 focus:outline-none focus:ring-0 cursor-pointer">
                            <option value="economy">Economy</option>
                            <option value="premium-economy" selected>Premium Economy</option>
                            <option value="business">Business</option>
                            <option value="first">First Class</option>
                        </select>
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 pt-2 text-amber-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Search Form -->
                <div class="p-4 md:p-6">
                    <!-- Input Fields Row -->
                    <div class="flex flex-wrap items-center gap-2 md:gap-3">
                        <!-- Origin -->
                        <div class="relative flex-1 min-w-[150px]">
                            <label for="origin" class="block text-xs text-gray-500 mb-1">From</label>
                            <input type="text" id="origin" placeholder="From"
                                class="w-full border-gray-400 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg pl-10 pr-2 py-2 border transition-all hover:border-gray-300">
                            <span class="absolute left-3 top-[60%] transform -translate-y-1/2 text-blue-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 2 2 0 011.523-1.943A5.977 5.977 0 0116 10c0 .34-.028.675-.083 1H15a2 2 0 00-2 2v2.197A5.973 5.973 0 0110 16v-2a2 2 0 00-2-2 2 2 0 01-2-2 2 2 0 00-1.668-1.973z" />
                                </svg>
                            </span>
                        </div>

                        <!-- Destination -->
                        <div class="relative flex-1 min-w-[150px]">
                            <label for="destination" class="block text-xs text-gray-500 mb-1">To</label>
                            <input type="text" id="destination" placeholder="To"
                                class="w-full border-gray-400 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg pl-10 pr-2 py-2 border transition-all hover:border-gray-300">
                            <span class="absolute left-3 top-[60%] transform -translate-y-1/2 text-blue-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path
                                        d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" />
                                </svg>
                            </span>
                        </div>

                        <!-- Departure Date -->
                        <div class="relative flex-1 min-w-[120px]">
                            <label for="departureDate" class="block text-xs text-gray-500 mb-1">Departure</label>
                            <input type="date" id="departureDate"
                                class="w-full border-gray-400 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg pl-10 pr-2 py-2 border transition-all hover:border-gray-300">
                            <span class="absolute left-3 top-[60%] transform -translate-y-1/2 text-blue-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        </div>

                        <!-- Return Date -->
                        <div class="relative flex-1 min-w-[120px]">
                            <label for="returnDate" class="block text-xs text-gray-500 mb-1">Return</label>
                            <input type="date" id="returnDate"
                                class="w-full border-gray-400 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg pl-10 pr-2 py-2 border transition-all hover:border-gray-300">
                            <span class="absolute left-3 top-[60%] transform -translate-y-1/2 text-blue-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        </div>

                        <!-- Passengers -->
                        <div class="relative flex-1 min-w-[180px]">
                            <label for="passengersButton" class="block text-xs text-gray-500 mb-1">Travelers</label>
                            <button type="button" id="passengersButton"
                                class="w-full flex items-center text-left border-gray-400 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg pl-10 pr-2 py-2 border transition-all hover:border-gray-300"
                                onclick="togglePassengersModal()">
                                <span>1 adult, Premium</span>
                            </button>
                            <span class="absolute left-3 top-[60%] transform -translate-y-1/2 text-blue-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Search Button -->
                <div class="absolute -bottom-6 left-0 right-0 flex justify-center">
                    <button
                        class="z-50 flex items-center justify-center bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-14 py-3.5 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-blue-200/50 transform hover:scale-105 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-blue-500 font-medium uppercase tracking-wide">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <span>Search Flights</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Results Section -->
        <div
            class="max-w-6xl mx-auto bg-white/95 backdrop-blur-sm shadow-lg border border-gray-100 rounded-xl overflow-hidden mt-32">
            <!-- Results Header -->
            <div class="border-b relative bg-gradient-to-r from-amber-50 to-yellow-50 px-6 py-4">
                <h2 class="text-xl font-semibold text-gray-800">Flight search results</h2>
                <p class="text-sm text-gray-600 mt-1">We found 39,059 flights matching your search criteria</p>
                <div class="flex flex-wrap items-center gap-3 mt-3">
                    <span
                        class="flex items-center bg-white/80 backdrop-blur-sm px-3 py-1 rounded-full border border-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-blue-500" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                clip-rule="evenodd" />
                        </svg>
                        London (LHR) to Paris (CDG)
                    </span>
                    <span
                        class="flex items-center bg-white/80 backdrop-blur-sm px-3 py-1 rounded-full border border-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-blue-500" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                clip-rule="evenodd" />
                        </svg>
                        Dec 15 - Dec 22
                    </span>
                    <span
                        class="flex items-center bg-white/80 backdrop-blur-sm px-3 py-1 rounded-full border border-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-blue-500" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                clip-rule="evenodd" />
                        </svg>
                        1 adult, Premium Economy
                    </span>
                </div>
            </div>

            <!-- Top Results Bar -->
            <div class="flex items-center justify-between bg-white/80 backdrop-blur-sm px-6 py-3 border-b">
                <div class="flex items-center space-x-4">
                    <span class="text-sm font-medium text-gray-700">39,059 flights found</span>
                    <div class="flex items-center text-sm bg-blue-50 px-3 py-1 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500 mr-1" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-blue-700">Results include flights with mixed cabin classes</span>
                    </div>
                </div>
                <div class="flex items-center">
                    <span class="text-sm text-gray-600 mr-2">View:</span>
                    <div class="flex rounded-lg shadow-sm" role="group">
                        <button
                            class="px-4 py-1.5 text-sm bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-l-lg font-medium">List</button>
                        <button
                            class="px-4 py-1.5 text-sm bg-white border-t border-b border-r border-gray-200 rounded-r-lg text-gray-600 hover:bg-gray-50 transition-colors">Grid</button>
                    </div>
                </div>
            </div>

            <!-- Main Content with Filters and Results -->
            <div class="flex flex-col md:flex-row">
                <!-- Left Sidebar Filters -->
                <div class="w-full md:w-64 bg-white/80 backdrop-blur-sm border-r">
                    <div class="sticky top-0">
                        <div class="p-4 border-b bg-gradient-to-r from-amber-50 to-yellow-50">
                            <h3 class="font-medium text-gray-800">Filter results</h3>
                        </div>
                        
                        <div class="p-4">
                            <h3 class="font-medium text-sm mb-3 text-gray-700">Stops</h3>
                            <div class="space-y-2">
                                <label
                                    class="flex items-center text-sm hover:bg-blue-50/80 p-2 rounded-lg cursor-pointer transition-colors">
                                    <input type="checkbox"
                                        class="form-checkbox h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                    <span class="ml-2 text-gray-700">Nonstop</span>
                                    <span class="ml-auto text-sm font-medium text-blue-600">$1,845</span>
                                </label>
                                <label
                                    class="flex items-center text-sm hover:bg-blue-50/80 p-2 rounded-lg cursor-pointer transition-colors">
                                    <input type="checkbox"
                                        class="form-checkbox h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                    <span class="ml-2 text-gray-700">1 stop</span>
                                    <span class="ml-auto text-sm font-medium text-blue-600">$1,659</span>
                                </label>
                                <label
                                    class="flex items-center text-sm hover:bg-blue-50/80 p-2 rounded-lg cursor-pointer transition-colors">
                                    <input type="checkbox"
                                        class="form-checkbox h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                    <span class="ml-2 text-gray-700">2+ stops</span>
                                    <span class="ml-auto text-sm font-medium text-blue-600">$2,077</span>
                                </label>
                            </div>
                        </div>

                        <!-- Price Range Filter -->
                        <div class="border-t p-4">
                            <h3 class="font-medium text-sm mb-3 text-gray-700">Price</h3>
                            <div class="space-y-4">
                                <div>
                                    <div class="flex justify-between mb-1">
                                        <span class="text-sm text-gray-600">
                                            <span class="font-medium">$850</span> — <span
                                                class="font-medium">$3,200</span>
                                        </span>
                                        <button
                                            class="text-sm text-blue-600 hover:text-blue-700 font-medium">Reset</button>
                                    </div>
                                    <div class="relative h-2 bg-gray-200 rounded-full mt-2">
                                        <div
                                            class="absolute left-[25%] right-[25%] h-full bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full">
                                        </div>
                                        <div
                                            class="absolute left-[25%] top-1/2 -translate-y-1/2 -mt-0.5 w-4 h-4 rounded-full bg-white border-2 border-blue-600 cursor-pointer hover:scale-110 transition-transform">
                                        </div>
                                        <div
                                            class="absolute right-[25%] top-1/2 -translate-y-1/2 -mt-0.5 w-4 h-4 rounded-full bg-white border-2 border-blue-600 cursor-pointer hover:scale-110 transition-transform">
                                        </div>
                                    </div>
                                    <div class="mt-6">
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label class="block text-xs text-gray-500 mb-1">Min Price</label>
                                                <div class="relative">
                                                    <span
                                                        class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                                                    <input type="number"
                                                        class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                                        value="850" min="0" step="10">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-500 mb-1">Max Price</label>
                                                <div class="relative">
                                                    <span
                                                        class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                                                    <input type="number"
                                                        class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                                        value="3200" min="0" step="10">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="pt-1">
                                    <button
                                        class="w-full py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-medium rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all shadow-sm">
                                        Apply price filter
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="border-t p-4">
                            <h3 class="font-medium text-sm mb-3 flex items-center text-gray-700">
                                <span>Airlines</span>
                                <div class="group relative ml-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                    <div
                                        class="hidden group-hover:block absolute left-full ml-2 w-48 p-2 bg-white rounded-lg shadow-lg border border-gray-100 text-xs text-gray-600 z-10">
                                        Select your preferred airlines to filter results
                                    </div>
                                </div>
                            </h3>
                            <div class="space-y-2">
                                <label
                                    class="flex items-center text-sm hover:bg-blue-50/80 p-2 rounded-lg cursor-pointer transition-colors">
                                    <input type="checkbox"
                                        class="form-checkbox h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                    <span class="ml-2 text-gray-700">British Airways</span>
                                    <span class="ml-auto text-sm font-medium text-blue-600">$1,845</span>
                                </label>
                                <label
                                    class="flex items-center text-sm hover:bg-blue-50/80 p-2 rounded-lg cursor-pointer transition-colors">
                                    <input type="checkbox"
                                        class="form-checkbox h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                    <span class="ml-2 text-gray-700">Air France</span>
                                    <span class="ml-auto text-sm font-medium text-blue-600">$1,863</span>
                                </label>
                                <label
                                    class="flex items-center text-sm hover:bg-blue-50/80 p-2 rounded-lg cursor-pointer transition-colors">
                                    <input type="checkbox"
                                        class="form-checkbox h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                    <span class="ml-2 text-gray-700">KLM</span>
                                    <span class="ml-auto text-sm font-medium text-blue-600">$1,659</span>
                                </label>
                            </div>
                            <button
                                class="text-blue-600 text-sm mt-2 hover:text-blue-700 focus:outline-none font-medium">Show
                                more airlines</button>
                        </div>

                        <div class="border-t p-4">
                            <h3 class="font-medium text-sm mb-3 text-gray-700">Times</h3>
                            <div class="space-y-6">
                                <!-- Departure Times -->
                                <div>
                                    <div class="flex justify-between mb-2">
                                        <span class="text-sm font-medium text-gray-700">Departure</span>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-2">
                                        <label
                                            class="flex items-center text-sm hover:bg-blue-50/80 p-2 rounded-lg cursor-pointer border border-gray-200 hover:border-blue-200 transition-colors">
                                            <input type="checkbox"
                                                class="form-checkbox h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                            <div class="ml-2">
                                                <div class="font-medium text-gray-700">Morning</div>
                                                <div class="text-xs text-gray-500">5am - 12pm</div>
                                            </div>
                                        </label>
                                        
                                        <label
                                            class="flex items-center text-sm hover:bg-blue-50/80 p-2 rounded-lg cursor-pointer border border-gray-200 hover:border-blue-200 transition-colors">
                                            <input type="checkbox"
                                                class="form-checkbox h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                            <div class="ml-2">
                                                <div class="font-medium text-gray-700">Afternoon</div>
                                                <div class="text-xs text-gray-500">12pm - 5pm</div>
                                            </div>
                                        </label>
                                        
                                        <label
                                            class="flex items-center text-sm hover:bg-blue-50/80 p-2 rounded-lg cursor-pointer border border-gray-200 hover:border-blue-200 transition-colors">
                                            <input type="checkbox"
                                                class="form-checkbox h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                            <div class="ml-2">
                                                <div class="font-medium text-gray-700">Evening</div>
                                                <div class="text-xs text-gray-500">5pm - 11pm</div>
                                            </div>
                                        </label>
                                        
                                        <label
                                            class="flex items-center text-sm hover:bg-blue-50/80 p-2 rounded-lg cursor-pointer border border-gray-200 hover:border-blue-200 transition-colors">
                                            <input type="checkbox"
                                                class="form-checkbox h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                            <div class="ml-2">
                                                <div class="font-medium text-gray-700">Night</div>
                                                <div class="text-xs text-gray-500">11pm - 5am</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                
                                <!-- Arrival Times -->
                                <div>
                                    <div class="flex justify-between mb-2">
                                        <span class="text-sm font-medium text-gray-700">Arrival</span>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-2">
                                        <label
                                            class="flex items-center text-sm hover:bg-blue-50/80 p-2 rounded-lg cursor-pointer border border-gray-200 hover:border-blue-200 transition-colors">
                                            <input type="checkbox"
                                                class="form-checkbox h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                            <div class="ml-2">
                                                <div class="font-medium text-gray-700">Morning</div>
                                                <div class="text-xs text-gray-500">5am - 12pm</div>
                                            </div>
                                        </label>
                                        
                                        <label
                                            class="flex items-center text-sm hover:bg-blue-50/80 p-2 rounded-lg cursor-pointer border border-gray-200 hover:border-blue-200 transition-colors">
                                            <input type="checkbox"
                                                class="form-checkbox h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                            <div class="ml-2">
                                                <div class="font-medium text-gray-700">Afternoon</div>
                                                <div class="text-xs text-gray-500">12pm - 5pm</div>
                                            </div>
                                        </label>
                                        
                                        <label
                                            class="flex items-center text-sm hover:bg-blue-50/80 p-2 rounded-lg cursor-pointer border border-gray-200 hover:border-blue-200 transition-colors">
                                            <input type="checkbox"
                                                class="form-checkbox h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                            <div class="ml-2">
                                                <div class="font-medium text-gray-700">Evening</div>
                                                <div class="text-xs text-gray-500">5pm - 11pm</div>
                                            </div>
                                        </label>
                                        
                                        <label
                                            class="flex items-center text-sm hover:bg-blue-50/80 p-2 rounded-lg cursor-pointer border border-gray-200 hover:border-blue-200 transition-colors">
                                            <input type="checkbox"
                                                class="form-checkbox h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                            <div class="ml-2">
                                                <div class="font-medium text-gray-700">Night</div>
                                                <div class="text-xs text-gray-500">11pm - 5am</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Flight Results -->
                <div class="flex-1">
                    <!-- Flight Cards -->
                    <div class="divide-y divide-gray-200">
                        <!-- Flight Card 1 -->
                        <div class="bg-white/95 backdrop-blur-sm p-6 hover:bg-blue-50/95 transition-colors">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <span
                                        class="px-3 py-1 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-xs font-medium rounded-full">Best</span>
                                    <div class="flex items-center space-x-2">
                                        <img src="https://content.r9cdn.net/rimg/provider-logos/airlines/v/BA.png?crop=false&width=108&height=92&fallback=default3.png&_v=89077931bb09ce2fc497652a6aaa40b2"
                                            alt="British Airways" class="h-6 w-6 rounded-full border border-gray-200">
                                        <span class="text-sm text-gray-500">
                                            <span class="font-medium text-gray-800">British Airways</span> • Recommended
                                            flight
                                    </span>
                                </div>
                                </div>
                                <div class="flex items-center bg-amber-50 px-3 py-1 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-500"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <span class="ml-1 text-sm font-medium text-amber-700">9.2/10</span>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row justify-between">
                                <!-- Outbound Flight Details -->
                                <div class="flex-1 mb-4 sm:mb-0">
                                    <div class="text-xs uppercase tracking-wide text-gray-500 mb-2">Outbound • Dec 15
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-24 md:w-32">
                                            <div class="text-sm md:text-base font-semibold">10:30 AM</div>
                                            <div class="text-xs text-gray-500">London (LHR)</div>
                                        </div>

                                        <div class="flex flex-col items-center flex-1 px-2">
                                            <div class="flex items-center text-xs text-blue-600 font-medium">
                                                <span>Nonstop</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 ml-1" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                                </svg>
                                            </div>
                                            <div class="w-full h-px bg-gray-200 relative my-1">
                                                <div
                                                    class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-blue-600 to-indigo-600">
                                                </div>
                                            </div>
                                            <div class="text-xs text-gray-500">6h 55m</div>
                                        </div>

                                        <div class="w-24 md:w-32">
                                            <div class="text-sm md:text-base font-semibold">6:15 PM</div>
                                            <div class="text-xs text-gray-500">Paris (CDG)</div>
                                        </div>
                                    </div>

                                    <div class="flex items-center mt-4">
                                        <div class="w-24 md:w-32">
                                            <div class="text-sm md:text-base font-semibold">12:40 PM</div>
                                            <div class="text-xs text-gray-500">Paris (CDG)</div>
                                        </div>

                                        <div class="flex flex-col items-center flex-1 px-2">
                                            <div class="flex items-center text-xs text-blue-600 font-medium">
                                                <span>Nonstop</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 ml-1" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                                </svg>
                                            </div>
                                            <div class="w-full h-px bg-gray-200 relative my-1">
                                                <div
                                                    class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-blue-600 to-indigo-600">
                                                </div>
                                            </div>
                                            <div class="text-xs text-gray-500">6h 35m</div>
                                        </div>

                                        <div class="w-24 md:w-32">
                                            <div class="text-sm md:text-base font-semibold">6:15 PM</div>
                                            <div class="text-xs text-gray-500">London (LHR)</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Price Details -->
                                <div class="w-full sm:w-64 md:w-72 flex flex-col items-end sm:border-l sm:pl-4">
                                    <div class="text-3xl font-bold text-gray-800">$1,845</div>
                                    <div class="text-sm text-gray-500">Roundtrip per traveler</div>
                                    
                                    <div class="mt-4 flex items-center text-sm text-gray-600">
                                        <div class="flex items-center space-x-3">
                                            <span
                                                class="inline-flex items-center text-xs px-2 py-0.5 bg-gradient-to-r from-blue-50 to-indigo-50 text-blue-700 border border-blue-100 rounded-md font-medium">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                    fill="currentColor" class="w-3 h-3 mr-1">
                                                    <path fill-rule="evenodd"
                                                        d="M10 2a.75.75 0 01.75.75v.258a33.186 33.186 0 016.668.83.75.75 0 01-.336 1.461 31.28 31.28 0 00-1.103-.232l1.702 7.545a.75.75 0 01-.387.832A4.981 4.981 0 0115 14c-.825 0-1.606-.2-2.294-.556a.75.75 0 01-.387-.832l1.77-7.849a31.743 31.743 0 00-3.339-.254v11.505a20.01 20.01 0 013.78.501.75.75 0 11-.339 1.462A18.558 18.558 0 0010 17.5c-1.442 0-2.845.165-4.191.477a.75.75 0 01-.338-1.462 20.01 20.01 0 013.779-.501V4.509c-1.129.026-2.243.112-3.34.254l1.771 7.85a.75.75 0 01-.387.831A4.98 4.98 0 015 14a4.98 4.98 0 01-2.294-.556.75.75 0 01-.387-.832L4.02 5.067c-.37.07-.738.148-1.103.232a.75.75 0 01-.336-1.462 33.186 33.186 0 016.668-.83V2.75A.75.75 0 0110 2z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                Premium
                                            </span>
                                            <div class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-500"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path
                                                        d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" />
                                                </svg>
                                                <span class="ml-1.5 text-emerald-700 font-medium">2 x 23kg checked bags
                                                    included</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <button
                                        class="mt-4 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-medium rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all shadow-md hover:shadow-lg w-full sm:w-auto text-center">
                                        View Deal
                                    </button>
                                    <a href="#" class="text-xs text-blue-600 hover:underline mt-1">View other fare
                                        options</a>
                                </div>
                            </div>
                        </div>

                        <!-- Flight Card 2 -->
                        <div class="bg-white/95 backdrop-blur-sm p-6 hover:bg-blue-50/95 transition-colors">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <span
                                        class="px-3 py-1 bg-gradient-to-r from-emerald-600 to-green-600 text-white text-xs font-medium rounded-full">Cheapest</span>
                                    <div class="flex items-center space-x-2">
                                        <img src="https://content.r9cdn.net/rimg/provider-logos/airlines/v/BA.png?crop=false&width=108&height=92&fallback=default3.png&_v=89077931bb09ce2fc497652a6aaa40b2"
                                            alt="British Airways" class="h-6 w-6 rounded-full border border-gray-200">
                                        <span class="text-sm text-gray-500">
                                            <span class="font-medium text-gray-800">British Airways</span> • Best value
                                    </span>
                                </div>
                                </div>
                                <div class="flex items-center bg-amber-50 px-3 py-1 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-500"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <span class="ml-1 text-sm font-medium text-amber-700">8.7/10</span>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row justify-between">
                                <!-- Outbound Flight Details -->
                                <div class="flex-1 mb-4 sm:mb-0">
                                    <div class="text-xs uppercase tracking-wide text-gray-500 mb-2">Outbound • Dec 15
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-24 md:w-32">
                                            <div class="text-sm md:text-base font-semibold">10:05 PM</div>
                                            <div class="text-xs text-gray-500">London (LHR)</div>
                                        </div>

                                        <div class="flex flex-col items-center flex-1 px-2">
                                            <div class="flex items-center text-xs text-amber-600 font-medium">
                                                <span>1 stop • AMS</span>
                                                <span class="mx-1 text-gray-400">•</span>
                                                <span class="text-gray-500">1h 30m</span>
                                            </div>
                                            <div class="w-full h-px bg-gray-200 relative my-1">
                                                <div
                                                    class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-amber-500 to-amber-600">
                                                </div>
                                                <div
                                                    class="absolute top-0 left-1/2 transform -translate-x-1/2 -mt-1 w-2 h-2 rounded-full bg-amber-500">
                                                </div>
                                            </div>
                                            <div class="text-xs text-gray-500">9h 55m</div>
                                        </div>

                                        <div class="w-24 md:w-32">
                                            <div class="text-sm md:text-base font-semibold">9:00 AM</div>
                                            <div class="text-xs text-gray-500">Paris (CDG)</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Price Details -->
                                <div class="w-full sm:w-64 md:w-72 flex flex-col items-end sm:border-l sm:pl-4">
                                    <div class="text-3xl font-bold text-gray-800">$1,659</div>
                                    <div class="text-sm text-gray-500">Roundtrip per traveler</div>
                                    
                                    <div class="mt-4 flex items-center text-sm text-gray-600">
                                        <div class="flex items-center space-x-3">
                                            <span
                                                class="inline-flex items-center text-xs px-2 py-0.5 bg-gradient-to-r from-blue-50 to-indigo-50 text-blue-700 border border-blue-100 rounded-md font-medium">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                    fill="currentColor" class="w-3 h-3 mr-1">
                                                    <path fill-rule="evenodd"
                                                        d="M10 2a.75.75 0 01.75.75v.258a33.186 33.186 0 016.668.83.75.75 0 01-.336 1.461 31.28 31.28 0 00-1.103-.232l1.702 7.545a.75.75 0 01-.387.832A4.981 4.981 0 0115 14c-.825 0-1.606-.2-2.294-.556a.75.75 0 01-.387-.832l1.77-7.849a31.743 31.743 0 00-3.339-.254v11.505a20.01 20.01 0 013.78.501.75.75 0 11-.339 1.462A18.558 18.558 0 0010 17.5c-1.442 0-2.845.165-4.191.477a.75.75 0 01-.338-1.462 20.01 20.01 0 013.779-.501V4.509c-1.129.026-2.243.112-3.34.254l1.771 7.85a.75.75 0 01-.387.831A4.98 4.98 0 015 14a4.98 4.98 0 01-2.294-.556.75.75 0 01-.387-.832L4.02 5.067c-.37.07-.738.148-1.103.232a.75.75 0 01-.336-1.462 33.186 33.186 0 016.668-.83V2.75A.75.75 0 0110 2z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                Premium
                                            </span>
                                            <div class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-500"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path
                                                        d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" />
                                                </svg>
                                                <span class="ml-1.5 text-emerald-700 font-medium">2 x 23kg checked bags
                                                    included</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <button
                                        class="mt-4 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-medium rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all shadow-md hover:shadow-lg w-full sm:w-auto text-center">
                                        View Deal
                                    </button>
                                    <a href="#" class="text-xs text-blue-600 hover:underline mt-1">Premium fare
                                        $2,064</a>
                                </div>
                            </div>
                        </div>

                        <!-- Show More Button -->
                        <div class="text-center p-8 border-t border-gray-200">
                            <button
                                class="bg-white/95 backdrop-blur-sm border border-gray-200 text-gray-700 px-8 py-3 rounded-xl hover:bg-gray-50/95 transition-colors font-medium flex items-center justify-center mx-auto space-x-2">
                                <span>Show more results</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Summary Section -->
        <div class="max-w-6xl mx-auto mt-32">
            <div class="bg-white/95 backdrop-blur-sm shadow-lg border border-gray-100 rounded-xl overflow-hidden">
                <div class="border-b relative bg-gradient-to-r from-amber-50 to-yellow-50 px-6 py-4">
                    <h2 class="text-xl font-semibold text-gray-800">Review your flights</h2>
                    <p class="text-sm text-gray-600 mt-1">Check your selected flights and fare details before proceeding
                    </p>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Selected Flight Details -->
                        <div class="lg:col-span-2 space-y-6">
                            <!-- Outbound Flight -->
                            <div class="space-y-4">
                                <div class="text-sm text-gray-600 uppercase tracking-wide font-medium">Flight Details
                                </div>
                                <div class="bg-gray-50/80 backdrop-blur-sm rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-6">
                                        <h3 class="text-lg font-semibold text-gray-800">Accra (Accra) To London
                                            (Gatwick)</h3>
                                        <div
                                            class="text-sm bg-blue-50 px-3 py-1 rounded-full text-blue-700 font-medium">
                                            6 hours 35 minutes</div>
                                    </div>

                                    <div class="flex items-start space-x-6">
                                        <div class="flex-1">
                                            <div class="flex justify-between items-baseline mb-1">
                                                <div class="text-2xl font-semibold">21:00 ACC</div>
                                                <div class="text-sm text-gray-500">Sun 1 Jun</div>
                                            </div>
                                            <div class="text-sm text-gray-600">Terminal 3</div>
                                        </div>

                                        <div class="flex-1 flex items-center justify-center relative">
                                            <div
                                                class="absolute top-1/2 left-0 right-0 h-0.5 bg-gradient-to-r from-blue-200 via-blue-400 to-blue-200">
                                            </div>
                                            <div
                                                class="absolute left-0 top-1/2 -translate-y-1/2 w-3 h-3 rounded-full border-2 border-blue-500 bg-white">
                                            </div>
                                            <div
                                                class="absolute right-0 top-1/2 -translate-y-1/2 w-3 h-3 rounded-full border-2 border-blue-500 bg-white">
                                            </div>
                                            <div
                                                class="bg-white px-3 py-1 rounded-full border border-blue-100 shadow-sm relative z-10 flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="h-4 w-4 text-blue-500 mr-1" viewBox="0 0 20 20"
                                                    fill="currentColor">
                                                    <path
                                                        d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                                                </svg>
                                                <span class="text-xs font-medium text-blue-700">6h 35m</span>
                                            </div>
                                        </div>

                                        <div class="flex-1">
                                            <div class="flex justify-between items-baseline mb-1">
                                                <div class="text-2xl font-semibold">04:35 LGW</div>
                                                <div class="text-sm text-gray-500">Mon 2 Jun</div>
                                            </div>
                                            <div class="text-sm text-gray-600">Terminal 5</div>
                                        </div>
                                    </div>

                                    <div class="mt-6 flex flex-wrap items-center gap-3">
                                        <div class="flex items-center px-3 py-1.5 bg-blue-50 rounded-full">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                            </svg>
                                            <span class="ml-1.5 text-sm text-blue-700 font-medium">Nonstop flight</span>
                                        </div>
                                        <div class="flex items-center px-3 py-1.5 bg-emerald-50 rounded-full">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="ml-1.5 text-sm text-emerald-700 font-medium">On-time
                                                performance 92%</span>
                                        </div>
                                        <div class="flex items-center px-3 py-1.5 bg-amber-50 rounded-full">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-600"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="ml-1.5 text-sm text-amber-700 font-medium">Evening
                                                departure</span>
                                        </div>
                                    </div>

                                    <div class="mt-6 flex items-center justify-between border-t border-gray-200 pt-4">
                                        <div class="flex items-center">
                                            <img src="https://content.r9cdn.net/rimg/provider-logos/airlines/v/BA.png?crop=false&width=108&height=92&fallback=default3.png&_v=89077931bb09ce2fc497652a6aaa40b2"
                                                alt="British Airways"
                                                class="h-8 w-8 rounded-full border border-gray-200 p-1">
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">British Airways</div>
                                                <div class="text-sm text-gray-500">Flight BA 346</div>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            <span
                                                class="bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-sm font-medium">Premium
                                                Economy</span>
                                            <div class="flex items-center text-emerald-700">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-500"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path
                                                        d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" />
                                                </svg>
                                                <span class="ml-1.5 text-sm font-medium">2 × 23kg</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Return Flight -->
                            <div class="bg-gray-50/80 backdrop-blur-sm rounded-xl p-4">
                                <div class="flex items-center justify-between mb-6">
                                    <h3 class="text-lg font-semibold text-gray-800">London (Heathrow) To Accra (Accra)
                                    </h3>
                                    <div class="text-sm bg-blue-50 px-3 py-1 rounded-full text-blue-700 font-medium">6
                                        hours 35 minutes</div>
                                </div>

                                <div class="flex items-start space-x-6">
                                    <div class="flex-1">
                                        <div class="flex justify-between items-baseline mb-1">
                                            <div class="text-2xl font-semibold">12:40 LHR</div>
                                            <div class="text-sm text-gray-500">Sat 5 Jul</div>
                                        </div>
                                        <div class="text-sm text-gray-600">Terminal 5</div>
                                    </div>

                                    <div class="flex-1 flex items-center justify-center relative">
                                        <div
                                            class="absolute top-1/2 left-0 right-0 h-0.5 bg-gradient-to-r from-blue-200 via-blue-400 to-blue-200">
                                        </div>
                                        <div
                                            class="absolute left-0 top-1/2 -translate-y-1/2 w-3 h-3 rounded-full border-2 border-blue-500 bg-white">
                                        </div>
                                        <div
                                            class="absolute right-0 top-1/2 -translate-y-1/2 w-3 h-3 rounded-full border-2 border-blue-500 bg-white">
                                        </div>
                                        <div
                                            class="bg-white px-3 py-1 rounded-full border border-blue-100 shadow-sm relative z-10 flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500 mr-1"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path
                                                    d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                                            </svg>
                                            <span class="text-xs font-medium text-blue-700">6h 35m</span>
                                        </div>
                                    </div>

                                    <div class="flex-1">
                                        <div class="flex justify-between items-baseline mb-1">
                                            <div class="text-2xl font-semibold">18:15 ACC</div>
                                            <div class="text-sm text-gray-500">Sat 5 Jul</div>
                                        </div>
                                        <div class="text-sm text-gray-600">Terminal 3</div>
                                    </div>
                                </div>

                                <div class="mt-6 flex items-center justify-between border-t border-gray-200 pt-4">
                                        <div class="flex items-center">
                                        <img src="https://content.r9cdn.net/rimg/provider-logos/airlines/v/BA.png?crop=false&width=108&height=92&fallback=default3.png&_v=89077931bb09ce2fc497652a6aaa40b2"
                                            alt="British Airways"
                                            class="h-8 w-8 rounded-full border border-gray-200 p-1">
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">British Airways</div>
                                            <div class="text-sm text-gray-500">Flight BA 347</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <span
                                            class="bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-sm font-medium">Premium
                                            Economy</span>
                                        <div class="flex items-center text-emerald-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-500"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path
                                                    d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" />
                                            </svg>
                                            <span class="ml-1.5 text-sm font-medium">2 × 23kg</span>
                                </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Price Summary -->
                        <div class="lg:col-span-1">
                            <div class="bg-gray-50/80 backdrop-blur-sm rounded-xl p-4">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-800">Your total price</h3>
                                    <div class="text-right">
                                        <div class="text-3xl font-bold text-gray-800">$2,692.80</div>
                                        <div class="text-sm text-gray-500">All taxes and fees included</div>
                                    </div>
                                </div>

                                <!-- Price Breakdown -->
                                <div class="bg-white/80 backdrop-blur-sm rounded-xl p-3 mb-4">
                                    <div class="space-y-2">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Flight fare (2 travelers)</span>
                                            <span class="text-gray-800">$2,400.00</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Taxes and fees</span>
                                            <span class="text-gray-800">$292.80</span>
                                        </div>
                                        <div
                                            class="flex justify-between text-sm font-medium pt-2 border-t border-gray-200">
                                            <span class="text-gray-800">Total</span>
                                            <span class="text-blue-600">$2,692.80</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Apply eVoucher -->
                                <div class="border-t border-gray-200 pt-4 mt-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                            </svg>
                                            <span class="text-sm font-medium text-gray-700">Apply an eVoucher</span>
                                        </div>
                                        <button
                                            class="text-sm text-blue-600 font-medium hover:text-blue-700">ADD</button>
                                    </div>
                                    <p class="text-xs text-gray-500">Use a promotional code or eVoucher</p>
                                </div>

                                <!-- Save with Avios -->
                                <div class="border-t border-gray-200 pt-4 mt-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-sm font-medium text-gray-700">Save with Avios</span>
                                        </div>
                                        <button class="text-sm text-blue-600 font-medium hover:text-blue-700">LOG
                                            IN</button>
                                    </div>
                                    <p class="text-xs text-gray-500">Reduce your fare with Avios</p>
                                </div>

                                <div class="mt-6">
                                    <button
                                        class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-4 py-3 rounded-lg font-medium hover:from-blue-700 hover:to-indigo-700 transition-all shadow-md hover:shadow-lg flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        Agree and Continue
                                    </button>
                                    <p class="text-xs text-gray-500 mt-2 text-center">By submitting continue I agree to
                                        the fare conditions</p>
                                        </div>
                                    </div>
                        </div>
                    </div>
                </div>
            </div>
                                </div>

        <!-- Passenger Details Section -->
        <div class="max-w-6xl mx-auto mt-32">
            <div
                class="bg-white/95 backdrop-blur-sm shadow-lg border border-gray-100 overflow-visible relative pb-8 rounded-xl">
                <!-- Section Header -->
                <div class="border-b relative bg-gradient-to-r from-amber-50 to-yellow-50">
                    <div class="px-6 py-4">
                        <h2 class="text-xl font-semibold text-gray-800">Passenger Information</h2>
                        <p class="text-sm text-gray-600 mt-1">Please enter details exactly as shown on travel documents
                        </p>
                    </div>
                </div>

                <!-- Input Form -->
                <div class="p-4 md:p-6">
                    <form class="space-y-6">
                        <!-- Contact Details Row -->
                        <div class="flex flex-wrap items-center gap-2 md:gap-3">
                            <div class="relative flex-1 min-w-[240px]">
                                <label for="email" class="block text-xs text-gray-500 mb-1">Email Address</label>
                                <input type="email" id="email"
                                    class="w-full border-gray-400 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg pl-10 pr-2 py-2 border transition-all hover:border-gray-300"
                                    placeholder="email@example.com" required>
                                <span class="absolute left-3 top-[60%] transform -translate-y-1/2 text-blue-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path
                                            d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                    </svg>
                                </span>
                            </div>
                            <div class="relative flex-1 min-w-[240px]">
                                <label for="phone" class="block text-xs text-gray-500 mb-1">Phone Number</label>
                                <input type="tel" id="phone"
                                    class="w-full border-gray-400 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg pl-10 pr-2 py-2 border transition-all hover:border-gray-300"
                                    placeholder="+1 (555) 000-0000" required>
                                <span class="absolute left-3 top-[60%] transform -translate-y-1/2 text-blue-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path
                                            d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <!-- Personal Details Row -->
                        <div class="flex flex-wrap items-center gap-2 md:gap-3">
                            <div class="relative flex-1 min-w-[240px]">
                                <label for="firstName" class="block text-xs text-gray-500 mb-1">First Name</label>
                                <input type="text" id="firstName"
                                    class="w-full border-gray-400 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg pl-10 pr-2 py-2 border transition-all hover:border-gray-300"
                                    placeholder="As shown on passport" required>
                                <span class="absolute left-3 top-[60%] transform -translate-y-1/2 text-blue-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </div>
                            <div class="relative flex-1 min-w-[240px]">
                                <label for="lastName" class="block text-xs text-gray-500 mb-1">Last Name</label>
                                <input type="text" id="lastName"
                                    class="w-full border-gray-400 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg pl-10 pr-2 py-2 border transition-all hover:border-gray-300"
                                    placeholder="As shown on passport" required>
                                <span class="absolute left-3 top-[60%] transform -translate-y-1/2 text-blue-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <!-- Additional Details Row -->
                        <div class="flex flex-wrap items-center gap-2 md:gap-3">
                            <div class="relative flex-1 min-w-[180px]">
                                <label for="dob" class="block text-xs text-gray-500 mb-1">Date of Birth</label>
                                <input type="date" id="dob"
                                    class="w-full border-gray-400 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg pl-10 pr-2 py-2 border transition-all hover:border-gray-300"
                                    required>
                                <span class="absolute left-3 top-[60%] transform -translate-y-1/2 text-blue-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </div>
                            <div class="relative flex-1 min-w-[180px]">
                                <label for="gender" class="block text-xs text-gray-500 mb-1">Gender</label>
                                <select id="gender"
                                    class="w-full border-gray-400 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg pl-10 pr-2 py-2 border transition-all hover:border-gray-300"
                                    required>
                                    <option value="">Select gender</option>
                                    <option value="M">Male</option>
                                    <option value="F">Female</option>
                                    <option value="X">Prefer not to say</option>
                                </select>
                                <span class="absolute left-3 top-[60%] transform -translate-y-1/2 text-blue-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </div>
                            <div class="relative flex-1 min-w-[180px]">
                                <label for="nationality" class="block text-xs text-gray-500 mb-1">Nationality</label>
                                <select id="nationality"
                                    class="w-full border-gray-400 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg pl-10 pr-2 py-2 border transition-all hover:border-gray-300"
                                    required>
                                    <option value="">Select nationality</option>
                                    <option value="US">United States</option>
                                    <option value="GB">United Kingdom</option>
                                    <option value="CA">Canada</option>
                                    <option value="AU">Australia</option>
                                    <option value="NZ">New Zealand</option>
                                </select>
                                <span class="absolute left-3 top-[60%] transform -translate-y-1/2 text-blue-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <!-- Passport Details Row -->
                        <div class="flex flex-wrap items-center gap-2 md:gap-3">
                            <div class="relative flex-1 min-w-[200px]">
                                <label for="passportNumber" class="block text-xs text-gray-500 mb-1">Passport
                                    Number</label>
                                <input type="text" id="passportNumber"
                                    class="w-full border-gray-400 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg pl-10 pr-2 py-2 border transition-all hover:border-gray-300"
                                    placeholder="Enter passport number" required>
                                <span class="absolute left-3 top-[60%] transform -translate-y-1/2 text-blue-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm3 1h6v4H7V5zm8 8v2h1v-2h-1zm-2-2H7v4h6v-4zm2 0h1V9h-1v2zm1-4V5h-1v2h1zM5 5v2H4V5h1zm0 4H4v2h1V9zm-1 4h1v2H4v-2z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </div>
                            <div class="relative flex-1 min-w-[200px]">
                                <label for="passportExpiry" class="block text-xs text-gray-500 mb-1">Passport Expiry
                                    Date</label>
                                <input type="date" id="passportExpiry"
                                    class="w-full border-gray-400 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg pl-10 pr-2 py-2 border transition-all hover:border-gray-300"
                                    required>
                                <span class="absolute left-3 top-[60%] transform -translate-y-1/2 text-blue-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </div>
                            <div class="relative flex-1 min-w-[200px]">
                                <label for="issuingCountry" class="block text-xs text-gray-500 mb-1">Issuing
                                    Country</label>
                                <select id="issuingCountry"
                                    class="w-full border-gray-400 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg pl-10 pr-2 py-2 border transition-all hover:border-gray-300"
                                    required>
                                    <option value="">Select country</option>
                                    <option value="US">United States</option>
                                    <option value="GB">United Kingdom</option>
                                    <option value="CA">Canada</option>
                                    <option value="AU">Australia</option>
                                    <option value="NZ">New Zealand</option>
                                </select>
                                <span class="absolute left-3 top-[60%] transform -translate-y-1/2 text-blue-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Action Buttons -->
                <div class="absolute -bottom-6 left-0 right-0 flex justify-center">
                    <button type="submit"
                        class="z-50 flex items-center justify-center bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-14 py-3.5 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-blue-200/50 transform hover:scale-105 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-blue-500 font-medium uppercase tracking-wide">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                        <span>Continue to Payment</span>
                                    </button>
                                </div>
                            </div>
                        </div>

        <!-- Payment Information Section -->
        <div class="max-w-6xl mx-auto mt-32 mb-8">
            <div
                class="bg-white/95 backdrop-blur-sm shadow-lg border border-gray-100 overflow-visible relative pb-8 rounded-xl">
                <!-- Section Header -->
                <div class="border-b relative bg-gradient-to-r from-amber-50 to-yellow-50">
                    <div class="px-6 py-4">
                        <h2 class="text-xl font-semibold text-gray-800">Payment Details</h2>
                        <p class="text-sm text-gray-600 mt-1">Enter your card information to complete the booking</p>
                    </div>
                </div>

                <!-- Input Form -->
                <div class="p-4 md:p-6">
                    <form class="space-y-6">
                        <!-- Card Details Row -->
                        <div class="flex flex-wrap items-center gap-2 md:gap-3">
                            <div class="relative flex-1 min-w-[240px]">
                                <label for="cardNumber" class="block text-xs text-gray-500 mb-1">Card Number</label>
                                <input type="text" id="cardNumber"
                                    class="w-full border-gray-400 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg pl-10 pr-12 py-2 border transition-all hover:border-gray-300"
                                    placeholder="1234 5678 9012 3456" required>
                                <span class="absolute left-3 top-[60%] transform -translate-y-1/2 text-blue-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                                        <path fill-rule="evenodd"
                                            d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                                <div
                                    class="absolute right-3 top-[60%] transform -translate-y-1/2 flex items-center space-x-1">
                                    <img src="https://a.travel-assets.com/dms-svg/payments/cards-cc_visa.svg" alt="Visa"
                                        class="h-4">
            </div>
        </div>
                            <div class="relative flex-1 min-w-[240px]">
                                <label for="cardName" class="block text-xs text-gray-500 mb-1">Cardholder Name</label>
                                <input type="text" id="cardName"
                                    class="w-full border-gray-400 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg pl-10 pr-2 py-2 border transition-all hover:border-gray-300"
                                    placeholder="Name as shown on card" required>
                                <span class="absolute left-3 top-[60%] transform -translate-y-1/2 text-blue-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
    </div>
                        </div>

                        <!-- Expiry and CVV Row -->
                        <div class="flex flex-wrap items-center gap-2 md:gap-3">
                            <div class="relative flex-1 min-w-[120px]">
                                <label for="expiryDate" class="block text-xs text-gray-500 mb-1">Expiry Date</label>
                                <input type="text" id="expiryDate"
                                    class="w-full border-gray-400 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg pl-10 pr-2 py-2 border transition-all hover:border-gray-300"
                                    placeholder="MM/YY" required>
                                <span class="absolute left-3 top-[60%] transform -translate-y-1/2 text-blue-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </div>
                            <div class="relative flex-1 min-w-[120px]">
                                <label for="cvv" class="block text-xs text-gray-500 mb-1">Security Code</label>
                                <input type="text" id="cvv"
                                    class="w-full border-gray-400 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg pl-10 pr-8 py-2 border transition-all hover:border-gray-300"
                                    placeholder="CVV" required>
                                <span class="absolute left-3 top-[60%] transform -translate-y-1/2 text-blue-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                                <span
                                    class="absolute right-3 top-[60%] transform -translate-y-1/2 text-gray-400 cursor-help">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <!-- Save Card Row -->
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" id="saveCard"
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="saveCard" class="text-sm text-gray-700">Save this card for future
                                bookings</label>
                        </div>

                        <!-- Price Summary -->
                        <div class="mt-6 bg-gray-50 rounded-xl p-4">
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Subtotal</span>
                                    <span class="font-medium">$2,400.00</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Taxes & Fees</span>
                                    <span class="font-medium">$292.80</span>
                                </div>
                                <div class="pt-2 border-t border-gray-200">
                                    <div class="flex justify-between text-base">
                                        <span class="font-medium text-gray-900">Total Amount</span>
                                        <span class="font-medium text-blue-600">$2,692.80</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Action Button -->
                <div class="absolute -bottom-6 left-0 right-0 flex justify-center">
                    <button type="submit"
                        class="z-50 flex items-center justify-center bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-14 py-3.5 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-blue-200/50 transform hover:scale-105 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-blue-500 font-medium uppercase tracking-wide">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Complete Payment</span>
                    </button>
                </div>
            </div>
        </div>
    </main>
</template>

<script setup>
// Flight data array
const flights = [
    {
        airline: 'British Airways',
        departureTime: '10:05 pm',
        arrivalTime: '7:50 pm',
        stops: 1,
        price: 1659,
        duration: '9h 45m',
        route: 'ACC → LHR',
        class: 'Premium Economy'
    }
    // Add more flight data as needed
];

// Initialize toggle function for passengers modal
if (typeof window !== 'undefined') {
    window.addEventListener('load', function () {
        window.togglePassengersModal = function () {
            const modal = document.getElementById('passengersModal');
            if (!modal) return;

            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden');
            } else {
                modal.classList.add('hidden');
            }
        };
    });
}
</script>

<style scoped>
/* Hide default date input appearance */
input[type="date"]::-webkit-calendar-picker-indicator {
    opacity: 0;
    position: absolute;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    width: auto;
    height: auto;
    cursor: pointer;
}

/* Custom styling for select dropdowns */
select {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
}

/* Override browser focus styles */
select:focus {
    outline: none;
    box-shadow: none;
}

/* Style for option appearance */
option {
    padding: 8px;
    background-color: white;
    color: #333;
}
</style>
