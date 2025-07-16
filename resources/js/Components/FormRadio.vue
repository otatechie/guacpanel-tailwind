
<script setup>
import { computed } from 'vue'

const props = defineProps({
    modelValue: {
        type: [String, Number, Boolean],
        required: true
    },
    value: {
        type: [String, Number, Boolean],
        required: true
    },
    label: {
        type: String,
        required: true
    },
    description: {
        type: String,
        default: ''
    },
    name: {
        type: String,
        default: ''
    }
})

const emit = defineEmits(['update:modelValue'])

const id = computed(() => `radio-${props.value}-${Math.random().toString(36).substring(7)}`)
</script> 

<template>
    <div class="relative flex items-start">
        <div class="flex items-center h-5">
            <input
                type="radio"
                :id="id"
                :name="name"
                :value="value"
                :checked="modelValue === value"
                @change="$emit('update:modelValue', value)"
                class="h-4 w-4 text-blue-600 dark:text-blue-400 border-gray-300 dark:border-gray-600 focus:ring-blue-500 dark:focus:ring-blue-400"
            >
        </div>
        <div class="ml-3 text-sm">
            <label :for="id" class="font-medium text-gray-700 dark:text-gray-300">
                {{ label }}
            </label>
            <p v-if="description" class="text-gray-500 dark:text-gray-400">{{ description }}</p>
        </div>
    </div>
</template>
