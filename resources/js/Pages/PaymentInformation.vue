<template>
    <div class="bg-gradient-to-br from-blue-50 via-white to-indigo-50 min-h-screen p-4 md:p-8">
        <!-- Header -->
        <div class="max-w-6xl mx-auto mb-8">
            <h1 class="text-4xl font-bold text-slate-800 mb-4">Complete Your Booking</h1>
            
            <!-- Progress Steps -->
            <div class="flex items-center space-x-4 mb-8">
                <div class="flex items-center">
                    <span class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-medium">1</span>
                    <span class="ml-2 text-sm font-medium text-gray-900">Flight Selection</span>
                </div>
                <div class="flex-1 h-px bg-blue-600"></div>
                <div class="flex items-center">
                    <span class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-medium">2</span>
                    <span class="ml-2 text-sm font-medium text-gray-900">Passenger Details</span>
                </div>
                <div class="flex-1 h-px bg-gray-300"></div>
                <div class="flex items-center">
                    <span class="w-8 h-8 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center text-sm font-medium">3</span>
                    <span class="ml-2 text-sm font-medium text-gray-600">Payment</span>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column - Passenger Details and Payment Form -->
                <div class="lg:col-span-2">
                    <!-- Passenger Information Section -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 mb-8">
                        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4 rounded-t-xl">
                            <h2 class="text-xl font-semibold text-gray-800">Passenger Information</h2>
                        </div>
                        <div class="p-6">
                            <!-- Contact Information -->
                            <div class="mb-8">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Contact Details</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                        <input type="email" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" 
                                            placeholder="Enter email address">
                                        <p class="mt-1 text-sm text-gray-500">Booking confirmation will be sent here</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                        <input type="tel" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" 
                                            placeholder="Enter phone number">
                                        <p class="mt-1 text-sm text-gray-500">For important travel updates</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Passenger Details -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Passenger Details</h3>
                                <div class="space-y-8">
                                    <!-- Adult 1 -->
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-4">
                                            <h4 class="text-base font-medium text-gray-900">Adult 1 (Primary)</h4>
                                            <span class="text-sm text-blue-600">Required</span>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">First & Middle Names</label>
                                                <input type="text" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" 
                                                    placeholder="As shown on ID/passport">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                                                <input type="text" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" 
                                                    placeholder="As shown on ID/passport">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                                                <input type="date" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Nationality</label>
                                                <select class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                                    <option value="">Select nationality</option>
                                                    <option value="us">United States</option>
                                                    <option value="uk">United Kingdom</option>
                                                    <option value="ca">Canada</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Passport Number</label>
                                                <input type="text" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" 
                                                    placeholder="Enter passport number">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Passport Expiry</label>
                                                <input type="date" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Add Passenger Button -->
                                    <button class="w-full border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-gray-400 transition-colors">
                                        <span class="text-sm font-medium text-gray-600">+ Add Another Passenger</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</template>

<script setup>
// Add any necessary script logic here
</script>

<style scoped>
/* Add any component-specific styles here */
</style> 