<script setup>
import { computed } from 'vue'
import GoogleIcon from '@js/Components/Icons/GoogleIcon.vue'
import FacebookIcon from '@js/Components/Icons/FacebookIcon.vue'
import GitHubIcon from '@js/Components/Icons/GitHubIcon.vue'
import LinkedInIcon from '@js/Components/Icons/LinkedInIcon.vue'

const props = defineProps({
    providersConfig: {
        type: Object,
        required: false,
        default: () => ({
            button_text: '',
            providers: [],
        }),
    },
    iconsOnly: {
        type: Boolean,
        default: false,
    },
})

const redirect = provider => {
    window.location.href = route('social.redirect', { provider })
}

const providerCount = computed(() => Object.keys(props.providersConfig.providers).length)

const gridClass = computed(() => {
    const count = providerCount.value
    if (count === 1) return 'grid-cols-1'
    if (props.iconsOnly) {
        if (count % 4 === 0) return 'grid-cols-4'
        if (count % 2 === 0) return 'grid-cols-2'
        return 'grid-cols-3'
    }
    return 'grid-cols-1'
})

const providerIcon = provider => {
    const icons = {
        google: GoogleIcon,
        facebook: FacebookIcon,
        github: GitHubIcon,
        linkedin: LinkedInIcon,
    }
    return icons[provider] || null
}

const providerLabel = provider => {
    const labels = {
        google: 'Google',
        facebook: 'Facebook',
        github: 'GitHub',
        linkedin: 'LinkedIn',
    }
    return labels[provider] || provider
}
</script>

<template>
    <div class="grid gap-2" :class="gridClass">
        <template v-for="(provider, index) in providersConfig.providers" :key="index">
            <button
                type="button"
                @click="redirect(index)"
                :aria-label="iconsOnly ? `Continue with ${providerLabel(index)}` : undefined"
                class="flex w-full items-center justify-center gap-2 rounded-lg border border-(--color-border-strong) px-3 py-2 text-sm font-medium text-(--color-text) transition-colors hover:bg-(--color-surface-muted)">
                <component :is="providerIcon(index)" class="size-5" />
                <span v-if="!iconsOnly">{{ providerLabel(index) }}</span>
            </button>
        </template>
    </div>
</template>
