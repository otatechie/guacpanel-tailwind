<template>
    <div class="flex items-start">
        <div class="flex items-center h-5">
            <input
                type="checkbox"
                :id="id"
                :name="name"
                :value="value"
                :checked="Array.isArray(modelValue) ? modelValue.includes(value) : modelValue"
                @change="updateValue"
                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
            />
        </div>
        <div class="ml-3 text-sm">
            <label :for="id" class="font-medium text-gray-700 dark:text-gray-300 cursor-pointer">{{ label }}</label>
            <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
    modelValue: {
        type: [Boolean, Array],
        required: true
    },
    value: {
        type: [String, Number],
        default: null
    },
    label: {
        type: String,
        required: true
    },
    name: {
        type: String,
        required: true
    },
    id: {
        type: String,
        required: true
    },
    error: {
        type: String,
        default: ''
    }
})

const emit = defineEmits(['update:modelValue'])

const updateValue = (e) => {
    if (Array.isArray(props.modelValue)) {
        const newValue = [...props.modelValue]
        if (e.target.checked) {
            newValue.push(props.value)
        } else {
            const index = newValue.indexOf(props.value)
            if (index > -1) {
                newValue.splice(index, 1)
            }
        }
        emit('update:modelValue', newValue)
    } else {
        emit('update:modelValue', e.target.checked)
    }
}
</script>
