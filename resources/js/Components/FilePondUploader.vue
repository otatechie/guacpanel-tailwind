<script setup>
import { computed } from 'vue'
import vueFilePond from 'vue-filepond'
import 'filepond/dist/filepond.min.css'
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css'
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type'
import FilePondPluginImagePreview from 'filepond-plugin-image-preview'
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size'
import FilePondPluginPdfPreview from 'filepond-plugin-pdf-preview'

// Initialize FilePond with all plugins
const FilePond = vueFilePond(
    FilePondPluginFileValidateType,
    FilePondPluginImagePreview,
    FilePondPluginFileValidateSize,
    FilePondPluginPdfPreview
)

const props = defineProps({
    name: {
        type: String,
        required: true
    },
    label: {
        type: String,
        required: true
    },
    labelIdle: {
        type: String,
        default: 'Drop files here...'
    },
    acceptedFileTypes: {
        type: Array,
        default: () => ['image/jpeg', 'image/png', 'application/pdf']
    },
    maxFileSize: {
        type: String,
        default: '5MB'
    },
    maxFiles: {
        type: Number,
        default: 1
    },
    allowMultiple: {
        type: Boolean,
        default: false
    },
    server: {
        type: Object,
        required: true
    },
    required: {
        type: Boolean,
        default: false
    },
    files: {
        type: Array,
        default: () => []
    }
})

const emit = defineEmits(['processfile', 'removefile'])
</script>

<template>
    <div class="space-y-2">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ label }}
        </label>
        <file-pond :name="name" :label-idle="labelIdle" :allow-multiple="false" :accepted-file-types="acceptedFileTypes"
            :max-file-size="maxFileSize" :server="server" @processfile="$emit('processfile', $event)"
            @removefile="$emit('removefile', $event)" :files="files" :credits="false" class="bg-white rounded-lg" />
    </div>
</template>