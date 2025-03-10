<script setup>
import vueFilePond from 'vue-filepond'
import 'filepond/dist/filepond.min.css'
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css'
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type'
import FilePondPluginImagePreview from 'filepond-plugin-image-preview'
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size'
import FilePondPluginPdfPreview from 'filepond-plugin-pdf-preview'

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
        default: () => ['image/jpeg', 'image/png']
    },
    maxFileSize: {
        type: String,
        default: '5MB'
    },
    allowMultiple: {
        type: Boolean,
        default: false
    },
    maxFiles: {
        type: Number,
        default: 1
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
            <span class="text-xs text-gray-500 ml-2">
                ({{ acceptedFileTypes.map(type => type.split('/')[1].toUpperCase()).join(', ') }}
                <template v-if="allowMultiple"> - Max files: {{ maxFiles }}</template>)
            </span>
        </label>
        
        <file-pond 
            :name="name" 
            :label-idle="labelIdle" 
            :allow-multiple="allowMultiple"
            :max-files="maxFiles"
            :accepted-file-types="acceptedFileTypes"
            :max-file-size="maxFileSize"
            :server="server"
            :files="files" 
            :credits="false" 
            class="bg-white dark:bg-gray-800 rounded-lg" 
            @processfile="$emit('processfile', $event)"
            @removefile="$emit('removefile', $event)"
        />
    </div>
</template>