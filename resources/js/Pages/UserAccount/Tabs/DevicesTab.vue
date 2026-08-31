<script setup>
import Button from '@/Components/Button.vue'
import { useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import Modal from '@js/Components/Notifications/Modal.vue'
import FormInput from '@js/Components/Forms/FormInput.vue'
import Alert from '@js/Components/Notifications/Alert.vue'
import Badge from '@js/Components/Badge.vue'

const props = defineProps({
    user: { type: Object, required: true },
    sessions: { type: Object },
    showHeading: { type: Boolean, default: true },
})

const formattedSessions = computed(() => {
    if (!Array.isArray(props.sessions)) return []
    return props.sessions.map(s => ({
        id: s.id,
        device: s.agent?.device || 'Unknown',
        browser: s.agent?.browser || 'Unknown',
        platform: s.agent?.platform || '',
        ip: s.ip || '',
        lastActive: s.lastActive || '',
        isCurrent: s.isCurrent || false,
    }))
})

/* A row is a browser session, not a device: two browsers on one laptop are two
   rows. The count also answers the question people come here with, which is
   whether anyone else is signed in. */
const sessionSummary = computed(() =>
    formattedSessions.value.length === 1
        ? 'This is your only active session.'
        : `You have ${formattedSessions.value.length} active sessions.`
)

/* Two windows of the same browser produce two rows reading "Edge . macOS", so
   the address is what tells you whether the other one is you. The current row
   shows it too, to compare against. Last active is omitted there: it is now. */
const sessionMeta = session =>
    [session.ip, session.isCurrent ? null : session.lastActive].filter(Boolean).join(' \u00b7 ')

/* "Sign out all" reads as including the one you are reading it in, which is the
   fear the dialog exists to settle. */
const otherSessionCount = computed(() => Math.max(0, formattedSessions.value.length - 1))

const logoutAllConsequence = computed(() =>
    otherSessionCount.value === 1
        ? 'Your other session is signed out immediately. This browser stays signed in.'
        : `Your ${otherSessionCount.value} other sessions are signed out immediately. This browser stays signed in.`
)

const logoutModal = ref(false)
const logoutAllModal = ref(false)
const selectedSession = ref(null)
const passwordForm = useForm({ password: '' })
const logoutForm = useForm({})

const confirmLogout = session => {
    selectedSession.value = session
    logoutModal.value = true
}
const confirmLogoutAll = () => {
    passwordForm.reset()
    logoutAllModal.value = true
}

const logoutSession = () => {
    logoutForm.delete(route('user.session.destroy', { sessionId: selectedSession.value.id }), {
        preserveScroll: true,
        onSuccess: () => {
            logoutModal.value = false
            selectedSession.value = null
        },
    })
}

const logoutAllSessions = () => {
    passwordForm.post(route('user.session.logout'), {
        preserveScroll: true,
        onSuccess: () => {
            logoutAllModal.value = false
            passwordForm.reset()
        },
    })
}
</script>

<template>
    <!-- Short rows with a right-aligned action: across the full column the
         browser and its Sign out end up a screen apart, so the list keeps a
         measure even though the page does not. -->
    <section class="max-w-2xl">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 v-if="showHeading" class="text-foreground text-base font-medium">
                    Active sessions
                </h2>
                <p class="text-muted-foreground text-sm" :class="showHeading ? 'mt-1' : ''">
                    {{ sessionSummary }}
                </p>
            </div>
            <button
                v-if="formattedSessions.length > 1"
                type="button"
                class="text-muted-foreground shrink-0 text-sm transition-colors hover:text-red-600 dark:hover:text-red-400"
                @click="confirmLogoutAll">
                Sign out others
            </button>
        </div>

        <!-- Rules between four short rows read as a table the content does not
             fill; the gap does the same work without the ink. -->
        <div v-if="formattedSessions.length" class="mt-5 space-y-4">
            <div
                v-for="s in formattedSessions"
                :key="s.id"
                class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-foreground flex items-center gap-2 text-sm font-medium">
                        {{ s.browser }} · {{ s.platform }}
                        <Badge v-if="s.isCurrent" dot variant="success">This browser</Badge>
                    </p>
                    <p v-if="sessionMeta(s)" class="text-muted-foreground mt-0.5 text-xs">
                        {{ sessionMeta(s) }}
                    </p>
                </div>
                <button
                    v-if="!s.isCurrent"
                    type="button"
                    class="text-muted-foreground shrink-0 text-sm transition-colors hover:text-red-600 dark:hover:text-red-400"
                    @click="confirmLogout(s)">
                    Sign out
                </button>
            </div>
        </div>

        <p v-else class="text-muted-foreground mt-5 text-sm">No active sessions.</p>
    </section>

    <Modal :show="logoutModal" @close="logoutModal = false" size="sm">
        <template #title>Sign out session</template>
        <template #default>
            <p class="text-muted-foreground text-sm">
                <span class="text-foreground font-medium">
                    {{ selectedSession?.browser }} · {{ selectedSession?.platform }}
                </span>
                <template v-if="selectedSession?.ip">at {{ selectedSession.ip }}</template>
                is signed out immediately. Whoever is using it has to sign in again.
            </p>
        </template>
        <template #footer>
            <div class="flex justify-end gap-3">
                <Button variant="secondary" size="sm" @click="logoutModal = false">Cancel</Button>
                <Button
                    variant="danger"
                    size="sm"
                    :disabled="logoutForm.processing"
                    @click="logoutSession">
                    {{ logoutForm.processing ? 'Signing out...' : 'Sign out session' }}
                </Button>
            </div>
        </template>
    </Modal>

    <Modal :show="logoutAllModal" @close="logoutAllModal = false" size="sm">
        <template #title>Sign out other sessions</template>
        <template #default>
            <div class="space-y-4">
                <p class="text-muted-foreground text-sm">{{ logoutAllConsequence }}</p>
                <FormInput
                    v-model="passwordForm.password"
                    label="Confirm your password"
                    type="password"
                    :error="passwordForm.errors.password"
                    required
                    autocomplete="current-password" />
            </div>
        </template>
        <template #footer>
            <div class="flex justify-end gap-3">
                <Button variant="secondary" size="sm" @click="logoutAllModal = false">
                    Cancel
                </Button>
                <Button
                    variant="danger"
                    size="sm"
                    :disabled="passwordForm.processing"
                    @click="logoutAllSessions">
                    {{ passwordForm.processing ? 'Signing out...' : 'Sign out other sessions' }}
                </Button>
            </div>
        </template>
    </Modal>
</template>
