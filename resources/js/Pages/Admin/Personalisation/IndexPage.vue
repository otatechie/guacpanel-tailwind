<script setup>
import { Head, Link } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import { computed, ref } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import FilePondUploader from '@/Components/FilePondUploader.vue'
import FormInput from '@/Components/FormInput.vue'
import FormSelect from '@/Components/FormSelect.vue'
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
    },
    timezones: {
        type: Array,
        required: true
    },
})

const form = useForm({
    app_logo: personalisation.value.appLogo,
    app_name: props.personalisation?.app_name || null,
    favicon: personalisation.value.favicon,
    footer_text: props.personalisation?.footer_text || null,
    copyright_text: props.personalisation?.copyright_text || null,
    timezone: props.personalisation?.timezone || 'UTC',
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
        axios.post(route('admin.personalisation.removeFile'), { field: name })
            .then(() => {
                personalisation.value[name] = null
            })
    }
}

const submit = () => {
    form.post(route('admin.personalization.update'), {
        preserveScroll: true
    });
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
                    <div class="w-full md:w-2/3">
                        <div class="dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                            <fieldset class="space-y-6">
                                <FormInput v-model="form.app_name" label="Application Name" id="app_name"
                                    placeholder="Enter your application name" :error="form.errors.app_name" />
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <FormInput v-model="form.footer_text" label="Footer text" id="footer_text"
                                        placeholder="Enter footer text" :error="form.errors.footer_text" />
                                    <FormInput v-model="form.copyright_text" label="Copyright text" id="copyright_text"
                                        placeholder="Enter copyright text" :error="form.errors.copyright_text" />
                                </div>
                            </fieldset>
                        </div>
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
                        <!-- Application Logo Upload -->
                        <FilePondUploader name="app_logo" label="Application logo" label-idle="Drop logo here..."
                            id="app_logo" :accepted-file-types="['image/jpeg', 'image/png']" :server="uploadConfig"
                            :files="getInitialFiles('app_logo')"
                            @processfile="(error, file) => handleProcessedFile(error, file, 'app_logo')"
                            @removefile="(error, file) => handleFileRemoved(error, file, 'app_logo')" />

                        <!-- Favicon Upload -->
                        <FilePondUploader name="favicon" label="Favicon" label-idle="Drop favicon here..." id="favicon"
                            :accepted-file-types="['image/png']" :server="uploadConfig"
                            :files="getInitialFiles('favicon')"
                            @processfile="(error, file) => handleProcessedFile(error, file, 'favicon')"
                            @removefile="(error, file) => handleFileRemoved(error, file, 'favicon')" />
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

                    <FormSelect v-model="form.timezone" label="Timezone" :options="timezones" id="timezone"
                        class="dark:bg-gray-800 rounded-lg p-6 border border-gray-200 w-full md:w-2/3 dark:border-gray-700" />
                </section>
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800 flex justify-end">
                    <button type="submit" class="btn-primary inline-flex items-center gap-2" :disabled="form.processing"
                        :aria-busy="form.processing">
                        <svg v-if="form.processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                        </svg>
                        {{ form.processing ? 'Saving...' : 'Save changes' }}
                    </button>
                </div>
            </form>
        </article>
    </main>
</template>
