<script setup>
import Button from '@/Components/Button.vue'
import { Head } from '@inertiajs/vue3'
import Default from '@js/Layouts/Default.vue'
import { useForm, usePage, router } from '@inertiajs/vue3'
import FilePondUploader from '@js/Components/Forms/FilePondUploader.vue'
import FormInput from '@js/Components/Forms/FormInput.vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'
import axios from 'axios'

defineOptions({
    layout: Default,
})

const page = usePage()
const csrfToken = page.props.csrf_token

const props = defineProps({
    personalisation: {
        type: Object,
        required: false,
        default: () => ({}),
    },
    /* Three separately-gated actions on this screen — updateInfo, upload and
       delete each check their own permission server-side. */
    canUpdate: { type: Boolean, default: false },
    canUpload: { type: Boolean, default: false },
    canDeleteFiles: { type: Boolean, default: false },
})

const form = useForm({
    app_logo: props.personalisation?.app_logo || null,
    app_name: props.personalisation?.app_name || null,
    app_logo_dark: props.personalisation?.app_logo_dark || null,
    favicon: props.personalisation?.favicon || null,
    copyright_text: props.personalisation?.copyright_text || null,
})

const uploadConfig = {
    process: {
        url: route('admin.personalization.upload'),
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        withCredentials: true,
        onload: response => {
            return typeof response === 'string' ? JSON.parse(response) : response
        },
    },
    load: (source, load) => {
        fetch(source)
            .then(res => res.blob())
            .then(load)
    },
    revert: null,
    remove: null,
}

const getInitialFiles = field => {
    if (!props.personalisation?.[field]) return []
    return [{ source: `/storage/${props.personalisation[field]}`, options: { type: 'local' } }]
}

const handleProcessedFile = (error, file, name) => {
    if (error || !file) return
    const response = file.serverId
        ? typeof file.serverId === 'string'
            ? JSON.parse(file.serverId)
            : file.serverId
        : typeof file === 'string'
          ? JSON.parse(file)
          : file
    if (response?.path) form[name] = response.path
}

const handleFileRemoved = async (error, file, name) => {
    if (!props.canDeleteFiles) return
    if (error) return
    form[name] = null
    try {
        await axios.delete(route('admin.personalization.delete.file'), { data: { field: name } })
        refreshPersonalisation()
    } catch (e) {
        // silent
    }
}

const submit = () => {
    if (!props.canUpdate) return
    form.post(route('admin.personalization.update.info'), { preserveScroll: true })
}

const refreshPersonalisation = () => {
    router.reload({ only: ['personalisation'] })
}
</script>

<template>
    <Head title="Personalization" />

    <main class="mx-auto max-w-4xl" aria-labelledby="personalization-settings">
        <PageHeader
            title="Personalization"
            :breadcrumbs="[
                { label: 'Dashboard', href: route('dashboard') },
                { label: 'System settings', href: route('admin.setting.index') },
                { label: 'Personalization' },
            ]" />

        <div class="space-y-8">
            <!-- App details -->
            <section>
                <h2 class="text-foreground text-sm font-medium">Application details</h2>
                <form id="app-details-form" class="mt-4 max-w-lg" @submit.prevent="submit">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <FormInput
                            id="app_name"
                            v-model="form.app_name"
                            label="Application name"
                            placeholder=""
                            help="Shown in the browser tab and sidebar."
                            :error="form.errors.app_name" />
                        <FormInput
                            id="copyright_text"
                            v-model="form.copyright_text"
                            label="Copyright text"
                            placeholder=""
                            help="Shown in the footer."
                            :error="form.errors.copyright_text" />
                    </div>
                    <div class="mt-4">
                        <Button
                            v-if="canUpdate"
                            variant="primary"
                            size="sm"
                            type="submit"
                            :disabled="form.processing || !form.isDirty"
                            :aria-busy="form.processing">
                            {{ form.processing ? 'Saving...' : 'Save' }}
                        </Button>
                    </div>
                </form>
            </section>

            <!-- Branding -->
            <section class="border-border border-t pt-8">
                <h2 class="text-foreground text-sm font-medium">Branding</h2>
                <p class="text-muted-foreground mt-0.5 text-xs">
                    {{
                        canUpload
                            ? 'Uploads apply immediately — no save needed.'
                            : 'You do not have permission to change these files.'
                    }}
                </p>

                <div class="mt-5 grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div>
                        <FilePondUploader
                            id="app_logo"
                            name="app_logo"
                            label="Logo"
                            :accepted-file-types="['image/jpeg', 'image/png']"
                            :server="uploadConfig"
                            :disabled="!canUpload"
                            :allow-remove="canDeleteFiles"
                            :files="getInitialFiles('app_logo')"
                            @processfile="
                                (error, file) => handleProcessedFile(error, file, 'app_logo')
                            "
                            @removefile="
                                (error, file) => handleFileRemoved(error, file, 'app_logo')
                            " />
                    </div>
                    <div>
                        <FilePondUploader
                            id="app_logo_dark"
                            name="app_logo_dark"
                            label="Logo (dark mode)"
                            :accepted-file-types="['image/jpeg', 'image/png']"
                            :server="uploadConfig"
                            :disabled="!canUpload"
                            :allow-remove="canDeleteFiles"
                            :files="getInitialFiles('app_logo_dark')"
                            @processfile="
                                (error, file) => handleProcessedFile(error, file, 'app_logo_dark')
                            "
                            @removefile="
                                (error, file) => handleFileRemoved(error, file, 'app_logo_dark')
                            " />
                    </div>
                    <div>
                        <FilePondUploader
                            id="favicon"
                            name="favicon"
                            label="Favicon"
                            :accepted-file-types="['image/png', 'image/x-icon']"
                            :server="uploadConfig"
                            :disabled="!canUpload"
                            :allow-remove="canDeleteFiles"
                            :files="getInitialFiles('favicon')"
                            @processfile="
                                (error, file) => handleProcessedFile(error, file, 'favicon')
                            "
                            @removefile="
                                (error, file) => handleFileRemoved(error, file, 'favicon')
                            " />
                    </div>
                </div>
            </section>
        </div>
    </main>
</template>
