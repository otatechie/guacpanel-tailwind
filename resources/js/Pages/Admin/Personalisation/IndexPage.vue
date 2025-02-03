<template>

    <Head title="Personalisation" />
    <div class="max-w-7xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 md:p-8">
            <div>
                <h2 class="sub-heading">Personalisation</h2>
                <p class="mt-1 leading-6 font-medium text-gray-600">
                    Customise the platform to your liking.
                </p>
            </div>

            <div class="w-full md:w-2xl">
                <form @submit.prevent="submit" class="space-y-6 md:space-y-8 mt-6 md:mt-8">
                    <!-- Media -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-2">
                            <h3 class="text-base md:text-lg font-semibold text-gray-900">Media</h3>
                            <div class="h-px bg-gray-200 flex-1"></div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">

                            <file-pond name="app_logo" ref="pond" label-idle="Drop files here..."
                                :allow-multiple="false" accepted-file-types="image/jpeg, image/png"
                                :server="uploadConfig" @init="handleFilePondInit" @processfile="handleProcessedFile"
                                @error="handleError"       :files="initialFiles"
                                />
                        </div>
                    </div>


                    <!-- Submit Button -->
                    <div class="mt-6 flex justify-end border-t pt-6">
                        <button type="submit" class="btn-primary inline-flex items-center gap-2"
                            :disabled="form.processing">
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4" />
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                            </svg>
                            {{ form.processing ? 'Saving changes...' : 'Save changes' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import { ref, computed } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import vueFilePond from 'vue-filepond'
import 'filepond/dist/filepond.min.css'
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css'
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type'
import FilePondPluginImagePreview from 'filepond-plugin-image-preview'

const page = usePage()
const csrfToken = page.props.csrf_token

const FilePond = vueFilePond(
    FilePondPluginFileValidateType,
    FilePondPluginImagePreview
)

defineOptions({
    layout: Default,
})

const props = defineProps({
    personalisation: {
        type: Object,
        required: false,
        default: () => ({})
    }
})

// In your script setup
const uploadConfig = {
    process: {
        url: route('admin.upload.store'),
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        withCredentials: true
    },
    load: (source, load) => {
        fetch(source)
            .then(res => res.blob())
            .then(load);
    }
}

const initialFiles = computed(() => {
    if (!props.personalisation?.app_logo) return []

    return [{
        source: `/storage/${props.personalisation.app_logo}`,
        options: {
            type: 'local'
        }
    }]
})

const form = useForm({
    app_logo: props.personalisation?.app_logo || null
})

const handleError = (error) => {
    console.error('Upload failed:', error)
}

const handleFilePondInit = () => {
    console.log('FilePond has initialized')
}

const handleProcessedFile = (error, file) => {
    if (error) {
        handleError(error)
        return
    }
    // Only update form if it's a new file
    if (file.serverId) {
        try {
            const response = JSON.parse(file.serverId)
            form.app_logo = response.path
        } catch (e) {
            handleError('Invalid server response')
        }
    }
}
const submit = () => {
    form.post(route('admin.personalisation.update'), {
        preserveScroll: true,
        onSuccess: () => {
            // Optional: Show success message
        },
        onError: (errors) => {
            console.error('Form submission errors:', errors);
        }
    })
}
</script>
