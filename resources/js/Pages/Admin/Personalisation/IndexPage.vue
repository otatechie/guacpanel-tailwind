<template>

    <Head title="Personalisation" />

    <div class="max-w-5xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 md:p-8">
            <div>
                <h2 class="sub-heading">Personalisation</h2>
                <p class="mt-1 leading-6 font-medium text-gray-600">
                    Customize the platform to suit your preferences.
                </p>
            </div>

            <div class="w-full md:w-2xl">
                <form @submit.prevent="submit" class="space-y-6 md:space-y-8 mt-6 md:mt-8">
                    <div class="space-y-6">
                        <div class="flex items-center gap-2">
                            <h3 class="text-base md:text-lg font-medium text-gray-800">Basic Information</h3>
                            <div class="h-px bg-gray-200 flex-1"></div>
                        </div>

                        <FormInput v-model="form.app_name" label="Application name" id="app_name"
                            :error="form.errors.app_name" type="text" />

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 pt-2">
                            <FormInput v-model="form.footer_text" label="Footer text" id="footer_text"
                                :error="form.errors.footer_text" type="text" />
                            <FormInput v-model="form.copyright_text" label="Copyright text" id="copyright_text"
                                :error="form.errors.copyright_text" type="text" />
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div class="flex items-center gap-2">
                            <h3 class="text-base md:text-lg font-medium text-gray-800">Localization</h3>
                            <div class="h-px bg-gray-200 flex-1"></div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                            <FormSelect v-model="form.timezone" label="Timezone" id="timezone" :options="timezones" />
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div class="flex items-center gap-2">
                            <h3 class="text-base md:text-lg font-medium text-gray-800">Media</h3>
                            <div class="h-px bg-gray-200 flex-1"></div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                            <div v-for="(config, index) in fileUploadConfigs" :key="index" class="space-y-2">
                                <label :for="config.name" class="block text-sm text-gray-600">
                                    {{ config.name === 'app_logo' ? 'Application logo' : 'Favicon' }}
                                </label>
                                <file-pond :id="config.name" :name="config.name" :ref="config.ref"
                                    :label-idle="config.labelIdle" :allow-multiple="false"
                                    accepted-file-types="image/jpeg, image/png" :server="uploadConfig"
                                    @processfile="(error, file) => handleProcessedFile(error, file, config.name)"
                                    @removefile="(error, file) => handleFileRemoved(error, file, config.name)"
                                    :files="config.initialFiles" :credits="false" />
                            </div>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div class="flex items-center gap-2">
                            <h3 class="text-base md:text-lg font-medium text-gray-800">Notifications</h3>
                            <div class="h-px bg-gray-200 flex-1"></div>
                        </div>

                        <div class="space-y-4">
                            <div
                                class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-4 border border-gray-200 hover:border-indigo-200 transition-all duration-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="p-2 bg-indigo-50 rounded-lg">
                                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-700">Email Notifications</h4>
                                            <p class="text-sm text-gray-500 mt-0.5">
                                                Receive important updates via email
                                            </p>
                                        </div>
                                    </div>
                                    <Switch v-model="form.email_notifications"
                                        class="relative inline-flex h-6 w-11 rounded-full" />
                                </div>
                            </div>
                            <div
                                class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-4 border border-gray-200 hover:border-indigo-200 transition-all duration-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="p-2 bg-indigo-50 rounded-lg">
                                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-700">Push Notifications</h4>
                                            <p class="text-sm text-gray-500 mt-0.5">
                                                Receive important updates via push notifications
                                            </p>
                                        </div>
                                    </div>
                                    <Switch v-model="form.push_notifications"
                                        class="relative inline-flex h-6 w-11 rounded-full" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end border-t border-gray-200 pt-6">
                        <button type="submit" class="btn-primary inline-flex items-center gap-2"
                            :disabled="form.processing">
                            <loading-spinner v-if="form.processing" />
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
import { computed } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import vueFilePond from 'vue-filepond'
import 'filepond/dist/filepond.min.css'
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css'
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type'
import FilePondPluginImagePreview from 'filepond-plugin-image-preview'
import Switch from '@/Components/Switch.vue'
import FormInput from '@/Components/FormInput.vue'
import FormSelect from '@/Components/FormSelect.vue'

const page = usePage()
const csrfToken = page.props.csrf_token

const FilePond = vueFilePond(FilePondPluginFileValidateType, FilePondPluginImagePreview)

defineOptions({ layout: Default })

const props = defineProps({
    personalisation: {
        type: Object,
        required: false,
        default: () => ({})
    },
    timezones: {
        type: Array,
        required: true
    },
})

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
            .then(load)
    }
}

const fileUploadConfigs = computed(() => [
    {
        name: 'app_logo',
        ref: 'logoPond',
        labelIdle: 'Drop logo here...',
        initialFiles: getInitialFiles('app_logo')
    },
    {
        name: 'favicon',
        ref: 'faviconPond',
        labelIdle: 'Drop favicon here...',
        initialFiles: getInitialFiles('favicon')
    }
])

const getInitialFiles = (field) => {
    if (!props.personalisation?.[field]) return []
    return [{
        source: `/storage/${props.personalisation[field]}`,
        options: { type: 'local' }
    }]
}

const form = useForm({
    app_logo: props.personalisation?.app_logo || null,
    app_name: props.personalisation?.app_name || null,
    favicon: props.personalisation?.favicon || null,
    footer_text: props.personalisation?.footer_text || null,
    copyright_text: props.personalisation?.copyright_text || null,
    timezone: props.personalisation?.timezone || 'UTC',
    email_notifications: props.personalisation?.email_notifications || false,
    push_notifications: props.personalisation?.push_notifications || false,
})


const handleFileRemoved = (error, file, name) => {
    if (error) return
    form[name] = null
}

const handleProcessedFile = (error, file, name) => {
    if (error) return
    if (file.serverId) {
        const response = JSON.parse(file.serverId)
        form[name] = response.path
    }
}

const submit = () => {
    form.post(route('admin.personalisation.update'), {
        preserveScroll: true
    })
}
</script>
