<template>

    <Head title="Personalisation" />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 md:p-8">
            <div>
                <h2 class="sub-heading">Personalisation</h2>
                <p class="mt-1 leading-6 font-medium text-gray-600">
                    These settings affect the entire platform. Only accessible to super administrators.
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-6 md:space-y-8 mt-6 md:mt-8">
                <div class="space-y-6">
                    <div class="flex items-center gap-2">
                        <h3 class="text-base md:text-lg font-semibold text-gray-900">Basic Information</h3>
                        <div class="h-px bg-gray-200 flex-1"></div>
                    </div>

                    <FormInput v-model="form.app_name" label="Application Name" type="text"
                        placeholder="Enter your application name" />

                    <FormInput v-model="form.footer_text" label="Footer Text" type="text"
                        placeholder="Enter footer text" />

                    <FormInput v-model="form.copyright_text" label="Copyright Text" type="text"
                        placeholder="Enter copyright text" />
                </div>

                <div class="space-y-6">
                    <div class="flex items-center gap-2">
                        <h3 class="text-base md:text-lg font-semibold text-gray-900">Media</h3>
                        <div class="h-px bg-gray-200 flex-1"></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                        <div
                            class="border-2 border-dashed border-gray-200 rounded-xl p-6 transition-all duration-200 hover:border-indigo-200 hover:bg-indigo-50/30">
                            <div class="text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                    viewBox="0 0 48 48">
                                    <path
                                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="mt-4 flex text-sm leading-6 text-gray-600 justify-center">
                                    <label
                                        class="relative cursor-pointer rounded-md font-semibold text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 hover:text-indigo-500">
                                        <span>Upload Logo</span>
                                        <FormInput v-model="form.app_logo" type="file" class="sr-only"
                                            @change="handleLogoPreview" />
                                    </label>
                                </div>
                                <p class="text-xs leading-5 text-gray-600 mt-2">PNG, JPG, GIF up to 10MB</p>
                            </div>
                            <img v-if="logoPreview" :src="logoPreview"
                                class="mt-4 h-20 object-contain rounded-lg mx-auto" alt="Logo preview">
                        </div>

                        <div
                            class="border-2 border-dashed border-gray-200 rounded-xl p-6 transition-all duration-200 hover:border-indigo-200 hover:bg-indigo-50/30">
                            <div class="text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                    viewBox="0 0 48 48">
                                    <path
                                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="mt-4 flex text-sm leading-6 text-gray-600 justify-center">
                                    <label
                                        class="relative cursor-pointer rounded-md font-semibold text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 hover:text-indigo-500">
                                        <span>Upload Favicon</span>
                                        <FormInput v-model="form.favicon" type="file" class="sr-only"
                                            @change="handleFaviconPreview" />
                                    </label>
                                </div>
                                <p class="text-xs leading-5 text-gray-600 mt-2">Recommended: 32x32px</p>
                            </div>
                            <img v-if="faviconPreview" :src="faviconPreview" class="mt-4 h-8 w-8 object-contain mx-auto"
                                alt="Favicon preview">
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="flex items-center gap-2">
                        <h3 class="text-base md:text-lg font-semibold text-gray-900">Notifications</h3>
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
                                        <h4 class="font-medium text-gray-800">Email Notifications</h4>
                                        <p class="text-sm text-gray-500 mt-0.5">Receive important updates via email</p>
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
                                        <h4 class="font-semibold text-gray-800">Push Notifications</h4>
                                        <p class="text-sm text-gray-500 mt-0.5">Receive important updates via push
                                            notifications</p>
                                    </div>
                                </div>
                                <Switch v-model="form.push_notifications"
                                    class="relative inline-flex h-6 w-11 rounded-full" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="flex items-center gap-2">
                        <h3 class="text-base md:text-lg font-semibold text-gray-900">Localization</h3>
                        <div class="h-px bg-gray-200 flex-1"></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                        <FormSelect v-model="form.timezone" label="Timezone" :options="timezones" />

                        <FormSelect v-model="form.currency" label="Currency" :options="currencies" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                        <FormSelect v-model="form.date_format" label="Date Format" :options="dateFormats" />

                        <FormSelect v-model="form.time_format" label="Time Format" :options="timeFormats" />
                    </div>
                </div>

                <div class="mt-6 md:mt-10 flex justify-start md:justify-end border-t border-gray-200 pt-4 md:pt-6">
                    <button type="submit"
                        class="w-full md:w-auto btn-primary inline-flex items-center gap-2 justify-center"
                        :disabled="form.processing">
                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        {{ form.processing ? 'Saving changes...' : 'Save changes' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import { useForm } from '@inertiajs/vue3'
import Switch from '@/Components/Switch.vue'
import FormInput from '@/Components/FormInput.vue'
import FormSelect from '@/Components/FormSelect.vue'
import { ref } from 'vue'

defineOptions({
    layout: Default,
})

const props = defineProps({
    settings: {
        type: Object,
        required: false,
        default: () => ({})
    }
})

const timezones = [
    { label: 'UTC', value: 'UTC' },
    // Add more timezones
]

const currencies = [
    { label: 'USD ($)', value: 'USD' },
    // Add more currencies
]

const dateFormats = [
    { label: 'YYYY-MM-DD', value: 'Y-m-d' },
    // Add more formats
]

const timeFormats = [
    { label: '24 Hour', value: 'H:i' },
    // Add more formats
]

const form = useForm({
    app_name: props.settings?.app_name ?? '',
    app_logo: null,
    favicon: null,
    footer_text: props.settings?.footer_text ?? '',
    copyright_text: props.settings?.copyright_text ?? '',
    email_notifications: props.settings?.email_notifications ?? true,
    push_notifications: props.settings?.push_notifications ?? true,
    timezone: props.settings?.timezone ?? 'UTC',
    date_format: props.settings?.date_format ?? 'Y-m-d',
    time_format: props.settings?.time_format ?? 'H:i',
    currency: props.settings?.currency ?? 'USD',
})

const logoPreview = ref(null)
const faviconPreview = ref(null)

const handleLogoPreview = (event) => {
    const file = event.target.files[0]
    if (file) logoPreview.value = URL.createObjectURL(file)
}

const handleFaviconPreview = (event) => {
    const file = event.target.files[0]
    if (file) faviconPreview.value = URL.createObjectURL(file)
}

const submit = () => {
    form.post(route('admin.setting.update'), {
        preserveScroll: true
    })
}
</script>
