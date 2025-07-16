<script setup>
import { computed } from 'vue'

const props = defineProps({
    modelValue: {
        type: [String, Number, Boolean],
        required: true
    },
    options: {
        type: Array,
        required: true,
        validator: (value) => value.every(option =>
            typeof option === 'object' &&
            'label' in option &&
            'value' in option
        )
    },
    label: {
        type: String,
        default: ''
    },
    name: {
        type: String,
        default: ''
    },
    error: {
        type: String,
        default: ''
    },
    required: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['update:modelValue'])

const groupName = computed(() => props.name || `radio-group-${Math.random().toString(36).substring(7)}`)
</script>

<template>
    <div class="space-y-3">
        <label v-if="label" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>

        <div class="flex flex-wrap gap-4">
            <div v-for="option in options" :key="option.value" class="relative flex items-start">
                <div class="flex items-center h-5">
                    <input type="radio" :id="`${groupName}-${option.value}`" :name="groupName" :value="option.value"
                        :checked="modelValue === option.value" @change="$emit('update:modelValue', option.value)"
                        class="h-4 w-4 text-blue-600 dark:text-blue-400 border-gray-300 dark:border-gray-600 focus:ring-blue-500 dark:focus:ring-blue-400"
                        :class="{ 'border-red-500': error }">
                </div>
                <div class="ml-3 text-sm">
                    <label :for="`${groupName}-${option.value}`"
                        class="font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                        {{ option.label }}
                    </label>
                    <p v-if="option.description" class="text-gray-500 dark:text-gray-400">{{ option.description }}</p>
                </div>
            </div>
        </div>

        <div v-if="error" class="text-red-500 text-xs mt-1">{{ error }}</div>
    </div>
</template>