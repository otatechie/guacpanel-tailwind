<script setup>
import { ref, watch, onBeforeUnmount } from 'vue'
const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },
    delay: {
        type: Number,
        default: 500,
    },
    className: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: '',
    },
})
const emit = defineEmits(['update:modelValue'])
const inputValue = ref(props.modelValue)
let timeoutId = null
watch(
    () => props.modelValue,
    newValue => {
        inputValue.value = newValue
    }
)
const handleInput = event => {
    inputValue.value = event.target.value
    if (timeoutId) {
        clearTimeout(timeoutId)
    }
    timeoutId = setTimeout(() => {
        emit('update:modelValue', inputValue.value)
    }, props.delay)
}
const handleKeydown = event => {
    if (event.key === 'Enter') {
        if (timeoutId) {
            clearTimeout(timeoutId)
        }
        emit('update:modelValue', inputValue.value)
    }
}

// Cleanup timeout on component unmount
onBeforeUnmount(() => {
    if (timeoutId) {
        clearTimeout(timeoutId)
        timeoutId = null
    }
})
</script>
<template>
    <input :value="inputValue" @input="handleInput" @keydown="handleKeydown" :class="className"
        :placeholder="placeholder" type="text" />
</template>
