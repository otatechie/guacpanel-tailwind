<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: [String, Number, Boolean],
    required: true,
  },
  options: {
    type: Array,
    required: true,
    validator: value =>
      value.every(option => typeof option === 'object' && 'label' in option && 'value' in option),
  },
  label: {
    type: String,
    default: '',
  },
  name: {
    type: String,
    default: '',
  },
  error: {
    type: String,
    default: '',
  },
  required: {
    type: Boolean,
    default: false,
  },
})

defineEmits(['update:modelValue'])

const groupName = computed(
  () => props.name || `radio-group-${Math.random().toString(36).substring(7)}`
)
</script>

<template>
  <div class="space-y-3">
    <label v-if="label" class="mb-2 block text-sm font-medium text-[var(--color-text)]">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>

    <div class="flex flex-wrap gap-4">
      <div v-for="option in options" :key="option.value" class="relative flex items-start">
        <div class="flex h-5 items-center">
          <input
            :id="`${groupName}-${option.value}`"
            type="radio"
            :name="groupName"
            :value="option.value"
            :checked="modelValue === option.value"
            class="h-4 w-4 border-[var(--color-border-strong)] text-blue-600 focus:ring-blue-500"
            :class="{ 'border-red-500': error }"
            @change="$emit('update:modelValue', option.value)" />
        </div>
        <div class="ml-3 text-sm">
          <label
            :for="`${groupName}-${option.value}`"
            class="cursor-pointer font-medium text-[var(--color-text)]">
            {{ option.label }}
          </label>
          <p v-if="option.description" class="text-[var(--color-text-muted)]">
            {{ option.description }}
          </p>
        </div>
      </div>
    </div>

    <div v-if="error" class="mt-1 text-xs text-red-500">{{ error }}</div>
  </div>
</template>
