<script>
export default {
    name: 'FloatingLabelInput',
    props: {
        modelValue: {
            type: [String, Number],
            default: ''
        },
        label: {
            type: String,
            required: true
        },
        id: {
            type: String,
            required: true
        },
        type: {
            type: String,
            default: 'text'
        },
        required: {
            type: Boolean,
            default: false
        },
        error: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            showPassword: false
        }
    },
    emits: ['update:modelValue']
}
</script>

<template>
    <fieldset class="space-y-1">
        <label :for="id" class="relative block">
            <input :type="showPassword ? 'text' : type" :id="id" :value="modelValue" :required="required"
                @input="$emit('update:modelValue', $event.target.value)"
                class="w-full peer border rounded-md bg-white placeholder-transparent px-3 py-2 transition-shadow duration-150 ease-in-out focus:outline-none dark:bg-gray-800 dark:text-white"
                :class="[
                    error
                        ? 'border-red-500 focus:border-red-500 focus:ring-1 focus:ring-red-500 focus:shadow focus:shadow-red-300/50 dark:focus:ring-red-900'
                        : 'border-gray-300 dark:border-gray-600 focus:border-purple-400 focus:ring-1 focus:ring-purple-500 focus:shadow focus:shadow-purple-300/50 dark:focus:ring-purple-500'
                ]" :placeholder="label" :aria-invalid="!!error"
                :aria-describedby="error ? `${id}-error` : undefined" />
            <span
                class="pointer-events-none absolute start-2.5 top-0 -translate-y-1/2 bg-white dark:bg-gray-800 px-1 text-xs text-gray-700 dark:text-gray-400 transition-all peer-placeholder-shown:top-1/2 peer-placeholder-shown:text-sm peer-focus:top-0 peer-focus:text-xs">
                {{ label }}{{ required ? ' *' : '' }}
            </span>

            <button v-if="type === 'password'" type="button"
                class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-700 dark:text-white cursor-pointer"
                @click="showPassword = !showPassword" :aria-label="showPassword ? 'Hide password' : 'Show password'"
                :aria-pressed="showPassword">
                <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                </svg>
            </button>
        </label>
        <p v-if="error" :id="`${id}-error`" role="alert" class="text-red-500 dark:text-red-400 text-xs">
            {{ error }}
        </p>
    </fieldset>
</template>
