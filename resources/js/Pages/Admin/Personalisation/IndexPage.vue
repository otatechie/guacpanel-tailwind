<script setup>
import { Head, Link } from '@inertiajs/vue3'
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

defineOptions({
    layout: Default
})

const page = usePage()
const csrfToken = page.props.csrf_token
const FilePond = vueFilePond(FilePondPluginFileValidateType, FilePondPluginImagePreview)

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

<template>

    <Head title="Personalization Settings" />

    <main class="max-w-5xl mx-auto">
        <section class="container-border overflow-hidden">
            <div class="bg-gray-50 px-6 py-3 border-b border-gray-200">
                <div class="flex items-center space-x-2 text-sm">
                    <Link href="/admin" class="text-gray-500 hover:text-gray-700">Dashboard</Link>
                    <span class="text-gray-400">/</span>
                    <span class="text-gray-800">Personalization</span>
                </div>
            </div>

            <header class="px-6 py-5 border-b border-gray-200">
                <h1 class="sub-heading">Personalization Settings</h1>
                <p class="mt-1 text-gray-500">
                    Configure your application's appearance and settings
                </p>
            </header>

            <form @submit.prevent="submit" class="divide-y divide-gray-200">
                <div class="p-6 space-y-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 bg-blue-50 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            </svg>
                        </div>
                        <h2 class="text-lg font-medium text-gray-800">Basic Information</h2>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-200 space-y-6 w-full md:w-2/3">
                        <FormInput v-model="form.app_name" label="Application Name"
                            placeholder="Enter your application name" :error="form.errors.app_name" />
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <FormInput v-model="form.footer_text" label="Footer text" placeholder="Enter footer text"
                                :error="form.errors.footer_text" />
                            <FormInput v-model="form.copyright_text" label="Copyright text"
                                placeholder="Enter copyright text" :error="form.errors.copyright_text" />
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 bg-blue-50 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h2 class="text-lg font-medium text-gray-800">Media</h2>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-200 w-full md:w-2/3">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div v-for="config in fileUploadConfigs" :key="config.name" class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ config.name === 'app_logo' ? 'Application logo' : 'Favicon' }}
                                </label>
                                <file-pond :name="config.name" :ref="config.ref" :label-idle="config.labelIdle"
                                    :allow-multiple="false" accepted-file-types="image/jpeg, image/png"
                                    :server="uploadConfig"
                                    @processfile="(error, file) => handleProcessedFile(error, file, config.name)"
                                    @removefile="(error, file) => handleFileRemoved(error, file, config.name)"
                                    :files="config.initialFiles" :credits="false" class="bg-white rounded-lg" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 bg-blue-50 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h2 class="text-lg font-medium text-gray-800">Localization</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 rounded-lg p-6 border border-gray-200 w-full md:w-2/3">
                        <FormSelect v-model="form.timezone" label="Timezone" :options="timezones" class="max-w-md" />
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 bg-blue-50 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                        <h2 class="text-lg font-medium text-gray-800">Notifications</h2>
                    </div>

                    <div class="bg-gray-50 rounded-lg border border-gray-200 divide-y divide-gray-200 md:w-2/3">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-800">Email Notifications</h3>
                                    <p class="mt-1 text-sm text-gray-500">Receive important updates via email</p>
                                </div>
                                <Switch v-model="form.email_notifications" />
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-800">Push Notifications</h3>
                                    <p class="mt-1 text-sm text-gray-500">Receive updates via browser notifications
                                    </p>
                                </div>
                                <Switch v-model="form.push_notifications" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 flex items-center justify-end gap-3">
                    <button type="submit" class="btn-primary inline-flex items-center gap-2"
                        :disabled="form.processing">
                        <svg v-if="form.processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                        </svg>
                        {{ form.processing ? 'Saving...' : 'Save Changes' }}
                    </button>
                </div>
            </form>
        </section>
    </main>
</template>
