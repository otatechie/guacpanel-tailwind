<script setup>
import { computed } from 'vue'
import Button from '@/Components/Button.vue'
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/Components/ui/tooltip'
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
    <TooltipProvider :delay-duration="200">
        <div class="grid gap-2" :class="gridClass">
            <template v-for="(provider, index) in providersConfig.providers" :key="index">
                <Tooltip v-if="iconsOnly">
                    <TooltipTrigger as-child>
                        <Button
                            variant="secondary"
                            class="w-full"
                            :aria-label="`Continue with ${providerLabel(index)}`"
                            @click="redirect(index)">
                            <component :is="providerIcon(index)" class="size-5" />
                        </Button>
                    </TooltipTrigger>
                    <TooltipContent side="bottom">
                        Continue with {{ providerLabel(index) }}
                    </TooltipContent>
                </Tooltip>

                <Button v-else variant="secondary" class="w-full" @click="redirect(index)">
                    <component :is="providerIcon(index)" class="size-5" />
                    <span>{{ providerLabel(index) }}</span>
                </Button>
            </template>
        </div>
    </TooltipProvider>
</template>
