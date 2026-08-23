<script setup>
import vueFilePond from 'vue-filepond'
import 'filepond/dist/filepond.min.css'
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css'
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type'
import FilePondPluginImagePreview from 'filepond-plugin-image-preview'
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size'
import FilePondPluginPdfPreview from 'filepond-plugin-pdf-preview'
import 'filepond-plugin-pdf-preview/dist/filepond-plugin-pdf-preview.min.css'
import FilePondPluginImageExifOrientation from 'filepond-plugin-image-exif-orientation'

const FilePond = vueFilePond(
    FilePondPluginImageExifOrientation,
    FilePondPluginFileValidateType,
    FilePondPluginImagePreview,
    FilePondPluginFileValidateSize,
    FilePondPluginPdfPreview,
)

defineProps({
    name: {
        type: String,
        required: true,
    },
    label: {
        type: String,
        required: true,
    },
    labelIdle: {
        type: String,
        default: 'Drop files here...',
    },
    acceptedFileTypes: {
        type: Array,
        default: () => ['image/jpeg', 'image/png', 'application/pdf', 'image/x-icon'],
    },
    maxFileSize: {
        type: String,
        default: '5MB',
    },
    allowMultiple: {
        type: Boolean,
        default: false,
    },
    maxFiles: {
        type: Number,
        default: 1,
    },
    server: {
        type: Object,
        required: true,
    },
    required: {
        type: Boolean,
        default: false,
    },
    files: {
        type: Array,
        default: () => [],
    },
})

defineEmits(['processfile', 'removefile'])
</script>

<template>
    <div>
        <p class="mb-1.5 text-xs font-medium text-foreground">
            {{ label }}
            <span class="ml-1 font-normal text-muted-foreground">
                {{ acceptedFileTypes.map(t => t.split('/')[1].toUpperCase()).join(', ') }}
            </span>
        </p>

        <file-pond
            :name="name"
            :allow-multiple="allowMultiple"
            :max-files="maxFiles"
            :accepted-file-types="acceptedFileTypes"
            :max-file-size="maxFileSize"
            :server="server"
            :files="files"
            :credits="null"
            :allow-pdf-preview="true"
            :label-idle="`Drop file here or <span class='filepond--label-action'>Browse</span>`"
            :image-preview-height="120"
            :style-panel-layout="'compact'"
            :style-load-indicator-position="'center bottom'"
            :style-button-remove-item-position="'center bottom'"
            :pdf-component-extra-params="'toolbar=0'"
            @processfile="(error, file) => $emit('processfile', error, file)"
            @removefile="(error, file) => $emit('removefile', error, file)" />
    </div>
</template>

<style>
/* FilePond overrides to match design system */
.filepond--panel-root {
    background-color: transparent !important;
    border: none !important;
}
.filepond--root .filepond--drop-label {
    background-color: var(--card) !important;
    border: 1.5px dashed var(--border) !important;
    border-radius: 8px !important;
    color: var(--muted-foreground) !important;
    font-size: 0.8125rem !important;
}
.filepond--drop-label label {
    cursor: pointer !important;
}
.filepond--label-action {
    text-decoration: underline !important;
    color: var(--foreground) !important;
    font-weight: 500 !important;
}
.filepond--root {
    margin-bottom: 0 !important;
}
.filepond--root .filepond--credits {
    display: none !important;
}
</style>
