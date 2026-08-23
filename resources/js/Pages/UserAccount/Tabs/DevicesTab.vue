<script setup>
import Button from '@/Components/Button.vue'
import { useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import Modal from '@js/Components/Notifications/Modal.vue'
import FormInput from '@js/Components/Forms/FormInput.vue'
import Alert from '@js/Components/Notifications/Alert.vue'

const props = defineProps({
    user: { type: Object, required: true },
    sessions: { type: Object },
})

const formattedSessions = computed(() => {
    if (!Array.isArray(props.sessions)) return []
    return props.sessions.map(s => ({
        id: s.id,
        device: s.agent?.device || 'Unknown',
        browser: s.agent?.browser || 'Unknown',
        platform: s.agent?.platform || '',
        lastActive: s.lastActive || '',
        isCurrent: s.isCurrent || false,
    }))
})

const logoutModal = ref(false)
const logoutAllModal = ref(false)
const selectedSession = ref(null)
const passwordForm = useForm({ password: '' })
const logoutForm = useForm({})

const confirmLogout = session => { selectedSession.value = session; logoutModal.value = true }
const confirmLogoutAll = () => { passwordForm.reset(); logoutAllModal.value = true }

const logoutSession = () => {
    logoutForm.delete(route('user.session.destroy', { sessionId: selectedSession.value.id }), {
        preserveScroll: true,
        onSuccess: () => { logoutModal.value = false; selectedSession.value = null },
    })
}

const logoutAllSessions = () => {
    passwordForm.delete(route('user.sessions.destroy'), {
        preserveScroll: true,
        onSuccess: () => { logoutAllModal.value = false; passwordForm.reset() },
    })
}
</script>

<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base font-medium text-foreground">Active sessions</h2>
                <p class="mt-1 text-sm text-muted-foreground">{{ formattedSessions.length }} {{ formattedSessions.length === 1 ? 'session' : 'sessions' }} across your devices</p>
            </div>
            <button v-if="formattedSessions.length > 1" type="button" class="shrink-0 text-sm text-red-600 hover:text-red-700 dark:text-red-400" @click="confirmLogoutAll">
                Sign out others
            </button>
        </div>

        <div v-if="formattedSessions.length" class="divide-y divide-border rounded-lg border border-border">
            <div v-for="s in formattedSessions" :key="s.id" class="flex items-center justify-between gap-4 px-4 py-3">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-foreground">{{ s.browser }} · {{ s.platform }}</span>
                        <span v-if="s.isCurrent" class="flex items-center gap-1 text-[10px] text-green-600 dark:text-green-400">
                            <span class="h-1 w-1 rounded-full bg-green-500"></span>You
                        </span>
                    </div>
                    <p class="mt-0.5 text-xs text-muted-foreground">{{ s.lastActive }}</p>
                </div>
                <button
                    v-if="!s.isCurrent"
                    type="button"
                    class="shrink-0 text-sm text-red-600 hover:text-red-700 dark:text-red-400"
                    @click="confirmLogout(s)">
                    Sign out
                </button>
            </div>
        </div>

        <p v-else class="py-6 text-center text-sm text-muted-foreground">No active sessions</p>
    </div>

    <Modal :show="logoutModal" @close="logoutModal = false" size="sm">
        <template #title>Sign out session</template>
        <template #default>
            <p class="text-sm text-muted-foreground">
                Sign out the session on <span class="font-medium text-foreground">{{ selectedSession?.browser }} · {{ selectedSession?.platform }}</span>?
            </p>
        </template>
        <template #footer>
            <div class="flex justify-end gap-3">
                <Button variant="secondary" size="sm" @click="logoutModal = false">Cancel</Button>
                <Button variant="danger" size="sm" :disabled="logoutForm.processing" @click="logoutSession">
                    {{ logoutForm.processing ? 'Signing out...' : 'Sign out' }}
                </Button>
            </div>
        </template>
    </Modal>

    <Modal :show="logoutAllModal" @close="logoutAllModal = false" size="sm">
        <template #title>Sign out all other sessions</template>
        <template #default>
            <div class="space-y-4">
                <p class="text-sm text-muted-foreground">Enter your password to sign out all other browser sessions.</p>
                <FormInput v-model="passwordForm.password" label="Password" type="password" :error="passwordForm.errors.password" required autocomplete="current-password" />
            </div>
        </template>
        <template #footer>
            <div class="flex justify-end gap-3">
                <Button variant="secondary" size="sm" @click="logoutAllModal = false">Cancel</Button>
                <Button variant="danger" size="sm" :disabled="passwordForm.processing" @click="logoutAllSessions">
                    {{ passwordForm.processing ? 'Signing out...' : 'Sign out all' }}
                </Button>
            </div>
        </template>
    </Modal>
</template>
