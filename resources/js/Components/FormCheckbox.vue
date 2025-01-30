<template>
    <div class="flex items-center">
        <input :id="id" :name="name" type="checkbox" :value="modelValue"
            @input="$emit('update:modelValue', $event.target.checked)" class="h-4 w-4 rounded border-gray-300 text-blue-600
        transition-shadow duration-150 ease-in-out
        focus:outline-none focus:ring-2 focus:ring-blue-200 focus:ring-offset-0 focus:border-blue-400">
        <label :for="id" class="ml-1.5 block text-sm leading-6 text-gray-800 select-none">
            {{ label }}
        </label>
    </div>
</template>

<script>
export default {
    name: 'CheckboxInput',
    props: {
        modelValue: {
            type: Boolean,
            default: false
        },
        label: {
            type: String,
            required: true
        },
        id: {
            type: String,
            required: true
        },
        name: {
            type: String,
            required: true
        }
    },
    emits: ['update:modelValue']
}
</script>
