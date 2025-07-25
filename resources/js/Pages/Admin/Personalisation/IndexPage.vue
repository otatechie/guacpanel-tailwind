<script setup>
import { Head, Link } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import { computed, ref } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import FilePondUploader from '@/Components/FilePondUploader.vue'
import FormInput from '@/Components/FormInput.vue'
import PageHeader from '@/Components/PageHeader.vue'
import axios from 'axios'

defineOptions({
    layout: Default
})

const page = usePage()
const csrfToken = page.props.csrf_token
const personalisation = ref(page.props.personalisation)

const props = defineProps({
    personalisation: {
        type: Object,
        required: false,
        default: () => ({})
    }
})

const form = useForm({
    app_logo: personalisation.value?.app_logo || null,
    app_name: props.personalisation?.app_name || null,
    app_logo_dark: personalisation.value?.app_logo_dark || null,
    favicon: personalisation.value?.favicon || null,
    copyright_text: props.personalisation?.copyright_text || null,
})

const uploadConfig = {
    process: {
        url: route('admin.personalization.upload'),
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        withCredentials: true,
        onload: (response) => {
            return typeof response === 'string' ? JSON.parse(response) : response;
        }
    },
    load: (source, load) => {
        fetch(source)
            .then(res => res.blob())
            .then(load);
    }
}

const getInitialFiles = (field) => {
    if (!props.personalisation?.[field]) {
        return [];
    }

    return [{
        source: `/storage/${props.personalisation[field]}`,
        options: { type: 'local' }
    }];
}

const handleProcessedFile = (error, file, name) => {
    if (error || !file) return;

    const response = file.serverId ?
        (typeof file.serverId === 'string' ? JSON.parse(file.serverId) : file.serverId) :
        (typeof file === 'string' ? JSON.parse(file) : file);

    if (response?.path) {
        form[name] = response.path;
    }
}

const handleFileRemoved = (error, file, name) => {
    if (!error) {
        form[name] = null
        axios.post(route('admin.personalization.delete.file'), { field: name })
            .then(() => {
                personalisation.value[name] = null
            })
    }
}

const submit = () => {
    form.post(route('admin.personalization.update.info'), {
        preserveScroll: true
    });
}
</script>


<template>

    <Head title="Personalization Settings" />

    <main class="max-w-6xl mx-auto" role="main">
        <div class="container-border overflow-hidden">
            <PageHeader title="Personalization Settings"
                description="Configure your application's appearance and settings" :breadcrumbs="[
                    { label: 'Dashboard', href: '/' },
                    { label: 'Settings', href: route('admin.setting.index') },
                    { label: 'Personalization' }
                ]" />

            <form @submit.prevent="submit" class="divide-y divide-gray-200 dark:divide-gray-700">
                <section class="p-6 space-y-6 dark:bg-gray-700" aria-labelledby="basic-info">
                    <header class="flex items-center gap-3 mb-4">
                        <span class="p-2 bg-indigo-50 dark:bg-indigo-900/50 rounded-lg" aria-hidden="true">
                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            </svg>
                        </span>
                        <h2 id="basic-info" class="text-lg font-medium text-gray-800 dark:text-gray-200">Application
                            Details</h2>
                    </header>
                    <div class="w-full md:w-2/3">
                        <div class="dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <FormInput v-model="form.app_name" label="Application name" id="app_name"
                                    placeholder="Enter your application name" :error="form.errors.app_name" />
                                <FormInput v-model="form.copyright_text" label="Copyright text" id="copyright_text"
                                    placeholder="Enter copyright text" :error="form.errors.copyright_text" />
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="btn btn-sm btn-primary gap-2"
                            :disabled="form.processing" :aria-busy="form.processing">
                            <svg v-if="form.processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" aria-hidden="true"></svg>
                            {{ form.processing ? 'Saving...' : 'Save changes' }}
                        </button>
                    </div>
                </section>

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

                    <div class="grid grid-cols-1 md:grid-cols-1 gap-8 dark:bg-gray-800 rounded-lg p-6 border border-gray-200 w-full md:w-2/3 dark:border-gray-700"
                        role="group" aria-label="Media uploads">
                        <div class="w-full md:w-2/3 grid grid-cols-1 md:grid-cols-1 gap-2">
                            <FilePondUploader name="app_logo" label="Application logo" label-idle="Drop logo here..."
                                id="app_logo" :accepted-file-types="['image/jpeg', 'image/png']" :server="uploadConfig"
                                :files="getInitialFiles('app_logo')"
                                @processfile="(error, file) => handleProcessedFile(error, file, 'app_logo')"
                                @removefile="(error, file) => handleFileRemoved(error, file, 'app_logo')" />
                            <div
                                class="mt-2 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                <div class="flex items-start gap-1">
                                    <svg class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div class="text-xs text-blue-800 dark:text-blue-200">
                                        <p>Optional. For logos that work better on dark backgrounds.</p>
                                    </div>
                                </div>
                            </div>

                            <FilePondUploader name="app_logo_dark" label="Application logo (dark mode)"
                                label-idle="Drop logo here..." id="app_logo_dark"
                                :accepted-file-types="['image/jpeg', 'image/png']" :server="uploadConfig"
                                :files="getInitialFiles('app_logo_dark')"
                                @processfile="(error, file) => handleProcessedFile(error, file, 'app_logo_dark')"
                                @removefile="(error, file) => handleFileRemoved(error, file, 'app_logo_dark')" />

                            <hr class="my-4 border-gray-200 dark:border-gray-700">

                            <FilePondUploader name="favicon" label="Favicon" label-idle="Drop favicon here..."
                                id="favicon" :accepted-file-types="['image/png', 'image/x-icon']" :server="uploadConfig"
                                :files="getInitialFiles('favicon')"
                                @processfile="(error, file) => handleProcessedFile(error, file, 'favicon')"
                                @removefile="(error, file) => handleFileRemoved(error, file, 'favicon')" />
                        </div>
                    </div>
                </section>
            </form>
        </div>
    </main>
</template>