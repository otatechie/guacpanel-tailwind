<script setup>
defineProps({
  title: {
    type: String,
    required: true
  },
  duration: {
    type: String,
    required: true
  },
  price: {
    type: Number,
    required: true
  },
  currency: {
    type: String,
    default: 'USD'
  },
  rating: {
    type: Number,
    default: 4.5
  },
  maxRating: {
    type: Number,
    default: 5
  },
  image: {
    type: String,
    required: true
  },
  perPerson: {
    type: Boolean,
    default: true
  },
  basis: {
    type: String,
    default: 'Double Sharing Basis'
  },
  amenities: {
    type: Array,
    default: () => ['Return Airfare', 'Breakfast', 'Airport Transfers', 'Sightseeing']
  }
});

const emits = defineEmits(['enquiry']);

const handleEnquiry = () => {
  emits('enquiry');
};
</script>

<template>
  <div class="travel-package rounded-lg overflow-hidden shadow-lg bg-white dark:bg-gray-800">
    <div class="relative">
      <img :src="image" :alt="title" class="w-full h-64 object-cover">
      <div class="absolute bottom-0 right-0 m-4">
        <button class="bg-white text-blue-600 px-3 py-1 rounded text-sm font-medium hover:bg-blue-50 transition">
          View All Photos
        </button>
      </div>
    </div>
    
    <div class="p-5">
      <div class="border-b pb-5 mb-5">
        <div class="flex justify-between items-start">
          <div>
            <div class="flex items-center mb-2">
              <div class="flex">
                <template v-for="i in Math.floor(rating)" :key="`full-star-${i}`">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                </template>
                <template v-if="rating % 1 !== 0">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                </template>
                <template v-for="i in (maxRating - Math.ceil(rating))" :key="`empty-star-${i}`">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                </template>
              </div>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ title }}</h2>
            <p class="text-gray-600 dark:text-gray-300">{{ duration }}</p>
          </div>
        </div>
      </div>

      <div class="border-b pb-5 mb-5">
        <div class="flex flex-wrap gap-4">
          <div v-for="(amenity, index) in amenities" :key="index" class="flex items-center gap-2">
            <span v-if="amenity === 'Return Airfare'">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
              </svg>
            </span>
            <span v-else-if="amenity === 'Breakfast'">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18v18H3V3z" />
              </svg>
            </span>
            <span v-else-if="amenity === 'Airport Transfers'">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
              </svg>
            </span>
            <span v-else-if="amenity === 'Sightseeing'">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </span>
            <span v-else>
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
            </span>
            <span class="text-gray-600 dark:text-gray-300 text-sm">{{ amenity }}</span>
          </div>
          <div v-if="amenities.length > 4" class="flex items-center">
            <button class="text-sm text-gray-600 dark:text-gray-300 font-medium hover:text-blue-600 transition">More</button>
          </div>
        </div>
      </div>

      <div class="flex justify-between items-center">
        <div>
          <p class="text-gray-500 text-sm">Starting From</p>
          <p class="text-red-600 font-bold text-2xl">{{ currency }} {{ price }}</p>
          <p class="text-green-600 text-sm">Per Person On {{ basis }}</p>
        </div>
        <button @click="handleEnquiry" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-md font-medium transition">
          Send Enquiry
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.travel-package {
  transition: transform 0.3s ease;
}

.travel-package:hover {
  transform: translateY(-5px);
}
</style> 