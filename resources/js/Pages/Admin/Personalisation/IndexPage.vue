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
import PageHeader from '@/Components/PageHeader.vue'

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

    <main class="max-w-5xl mx-auto" aria-labelledby="personalization-settings">
        <h1 class="sr-only" id="personalization-settings">Personalization Settings</h1>

        <article class="container-border overflow-hidden">
            <PageHeader title="Personalization Settings"
                description="Configure your application's appearance and settings" :breadcrumbs="[
                    { label: 'Dashboard', href: '/' },
                    { label: 'Settings', href: route('admin.setting.index') },
                    { label: 'Personalization' }
                ]" />

            <form @submit.prevent="submit" class="divide-y divide-gray-200 dark:divide-gray-700">
                <!-- Basic Information Section -->
                <section class="p-6 space-y-6 dark:bg-gray-700" aria-labelledby="basic-info">
                    <header class="flex items-center gap-3 mb-4">
                        <span class="p-2 bg-indigo-50 dark:bg-indigo-900/50 rounded-lg" aria-hidden="true">
                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            </svg>
                        </span>
                        <h2 id="basic-info" class="text-lg font-medium text-gray-800 dark:text-gray-200">Basic
                            Information</h2>
                    </header>

                    <FormInput v-model="form.app_name" label="Application Name"
                        placeholder="Enter your application name" :error="form.errors.app_name"
                        class="dark:bg-gray-800 rounded-lg p-6 border border-gray-200 w-full md:w-2/3 dark:border-gray-700" />

                    <div
                        class="grid grid-cols-1 md:grid-cols-2 gap-6 dark:bg-gray-800 rounded-lg p-6 border border-gray-200 w-full md:w-2/3 dark:border-gray-700">
                        <FormInput v-model="form.footer_text" label="Footer text" placeholder="Enter footer text"
                            :error="form.errors.footer_text" />
                        <FormInput v-model="form.copyright_text" label="Copyright text"
                            placeholder="Enter copyright text" :error="form.errors.copyright_text" />
                    </div>
                </section>

                <!-- Media Section -->
                <section class="p-6 space-y-6 dark:bg-gray-700" aria-labelledby="media-section">
                    <header class="flex items-center gap-3 mb-4">
                        <span class="p-2 bg-emerald-50 dark:bg-emerald-900/50 rounded-lg" aria-hidden="true">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </span>
                        <h2 id="media-section" class="text-lg font-medium text-gray-800 dark:text-gray-200">Media</h2>
                    </header>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 dark:bg-gray-800 rounded-lg p-6 border border-gray-200 w-full md:w-2/3 dark:border-gray-700"
                        role="group" aria-label="Media uploads">
                        <div v-for="config in fileUploadConfigs" :key="config.name" class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
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
                </section>

                <!-- Localization Section -->
                <section class="p-6 space-y-6 dark:bg-gray-700" aria-labelledby="localization-section">
                    <header class="flex items-center gap-3 mb-4">
                        <span class="p-2 bg-purple-50 dark:bg-purple-900/50 rounded-lg" aria-hidden="true">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        <h2 id="localization-section" class="text-lg font-medium text-gray-800 dark:text-gray-200">
                            Localization</h2>
                    </header>

                    <FormSelect v-model="form.timezone" label="Timezone" :options="timezones"
                        class="dark:bg-gray-800 rounded-lg p-6 border border-gray-200 w-full md:w-2/3 dark:border-gray-700" />
                </section>

                <!-- Notifications Section -->
                <section class="p-6 space-y-6 dark:bg-gray-700" aria-labelledby="notifications-section">
                    <header class="flex items-center gap-3 mb-4">
                        <span class="p-2 bg-amber-50 dark:bg-amber-900/50 rounded-lg" aria-hidden="true">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </span>
                        <h2 id="notifications-section" class="text-lg font-medium text-gray-800 dark:text-gray-200">
                            Notifications</h2>
                    </header>

                    <div
                        class="bg-gray-50 rounded-lg border border-gray-200 divide-y divide-gray-200 md:w-2/3 dark:bg-gray-800 dark:border-gray-700 dark:divide-gray-700">
                        <div class="p-6 flex items-center justify-between" role="group"
                            aria-labelledby="email-notifications">
                            <div>
                                <h3 id="email-notifications"
                                    class="font-medium text-gray-800 dark:text-gray-200">Email Notifications
                                </h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Receive important updates via
                                    email</p>
                            </div>
                            <Switch v-model="form.email_notifications" aria-label="Toggle email notifications" />
                        </div>
                        <div class="p-6 flex items-center justify-between" role="group"
                            aria-labelledby="push-notifications">
                            <div>
                                <h3 id="push-notifications"
                                    class="font-medium text-gray-800 dark:text-gray-200">Push Notifications</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Receive updates via browser
                                    notifications</p>
                            </div>
                            <Switch v-model="form.push_notifications" aria-label="Toggle push notifications" />
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="btn-primary inline-flex items-center gap-2"
                            :disabled="form.processing" :aria-busy="form.processing">
                            <svg v-if="form.processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4" />
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                            </svg>
                            {{ form.processing ? 'Saving...' : 'Save changes' }}
                        </button>
                    </div>
                </section>
            </form>
        </article>
    </main>
</template>
