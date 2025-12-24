<script setup>
import { Head, useForm, usePage, router, Link } from '@inertiajs/vue3'
import DataTable from '@js/Components/Common/Datatable.vue'
import Default from '@js/Layouts/Default.vue'
import Modal from '@js/Components/Notifications/Modal.vue'
import { createColumnHelper } from '@tanstack/vue-table'
import { h, ref, watch } from 'vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'
import FormInput from '@js/Components/Forms/FormInput.vue'
import FormSelect from '@js/Components/Forms/FormSelect.vue'
import FormCheckbox from '@js/Components/Forms/FormCheckbox.vue'
import RolesBadges from '@js/Components/Common/RolesBadges.vue'

defineOptions({
  layout: Default,
})

const props = defineProps({
  users: {
    type: Object,
    required: true,
  },
  roles: {
    type: Object,
    required: true,
  },
})

const page = usePage()

const columnHelper = createColumnHelper()
const loading = ref(false)
const pagination = ref({
  current_page: props.users.current_page,
  per_page: Number(props.users.per_page),
  total: props.users.total,
})

const showDeleteModal = ref(false)
const userToDelete = ref(null)
const showDestroyAllUsersModal = ref(false)

const form = useForm({
  confirm_destroy_all: false,
})

const closeModal = () => {
  showDeleteModal.value = false
  userToDelete.value = null
  showDestroyAllUsersModal.value = false
  form.errors = {}
  form.reset()
}

const isSuperUser = user => {
  if (!user?.roles?.length) return false
  const role = user.roles[0]
  return role?.name?.toLowerCase() === 'superuser' || role?.slug?.toLowerCase() === 'superuser'
}

const canDeleteUser = user => {
  if (!user) return false
  if (isSuperUser(user)) return false
  return true
}

const handleRestore = user => {
  if (!user?.id) return
  router.post(route('admin.user.deleted.restore', { id: user.id }))
}

const confirmDeleteUser = user => {
  if (!canDeleteUser(user)) return
  userToDelete.value = user
  showDeleteModal.value = true
}

const checkAutoDeleteStatus = user => {
  const autoDestroy = user.auto_destroy
  const autoDestroyDate = user.auto_destroy_date_full
  let val = '-'
  if (!autoDestroy) {
    val = 'Disabled'
  } else if (autoDestroy && !autoDestroyDate) {
    val = 'Date Not Set'
  } else if (autoDestroy && autoDestroyDate) {
    val = autoDestroyDate
  }
  return val
}

const destroyUser = () => {
  if (!userToDelete.value?.id) return
  if (!canDeleteUser(userToDelete.value)) return

  router.delete(route('admin.user.deleted.destroy', { id: userToDelete.value.id }), {
    preserveScroll: true,
    onSuccess: () => {
      showDeleteModal.value = false
      userToDelete.value = null
    },
    onError: () => {
      showDeleteModal.value = false
      userToDelete.value = null
    },
  })
}

const openDestroyAllUsersModal = () => {
  form.errors = {}
  form.reset()
  showDestroyAllUsersModal.value = true
}

const destroyAllUsers = () => {
  if (!form.confirm_destroy_all) {
    form.errors.confirm_destroy_all = 'The confirm destroy all field must be accepted.'
    return
  }

  form.post(route('admin.user.deleted.destroy-all'), {
    preserveScroll: true,
    onSuccess: () => {
      showDestroyAllUsersModal.value = false
      form.reset()
    },
    onError: errors => {
      //
    },
  })
}

const columns = [
  columnHelper.accessor('name', {
    header: 'Name',
    cell: info => h('span', info.getValue() || '-'),
  }),
  columnHelper.accessor('email', {
    header: 'Email',
    cell: info => h('span', info.getValue() || '-'),
  }),
  columnHelper.accessor('role', {
    header: 'Role',
    cell: info => {
      const roleName = info.row.original.roles?.[0]?.name || 'No Role'
      return h(
        'span',
        {
          class:
            'px-1 py-1 text-xs capitalize rounded-md inline-flex items-center justify-center bg-blue-50 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300',
        },
        roleName
      )
    },
  }),
  columnHelper.accessor('email_verified_at', {
    header: 'Verified',
    cell: info => h('span', info.getValue() ? 'Yes' : 'No'),
  }),
  columnHelper.accessor('disable_account', {
    header: 'Disabled',
    cell: info => h('span', info.getValue() ? 'Yes' : 'No'),
  }),
  columnHelper.accessor('created_at_formatted', {
    header: 'Created At',
    cell: info => h('span', info.getValue() || '-'),
  }),
  columnHelper.accessor('deleted_at_formatted', {
    header: 'Deleted At',
    cell: info => h('span', info.getValue() || '-'),
  }),
  columnHelper.accessor('auto_destroy', {
    header: 'Auto Destroy On',
    cell: info => {
      const val = checkAutoDeleteStatus(info.row.original)
      return h(
        'span',
        {
          class: 'text-xs',
        },
        val
      )
    },
  }),
  columnHelper.display({
    id: 'actions',
    header: 'Actions',
    cell: info => {
      const user = info.row.original
      if (!user?.id) return null

      const editButton = h(
        'button',
        {
          class:
            'p-2 text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-lg cursor-pointer hover:scale-105 transition-all duration-200',
          onClick: () => handleRestore(user),
          type: 'button',
          title: 'Restore User',
        },
        [
          h('span', { class: 'sr-only' }, 'Restore User'),
          h(
            'svg',
            {
              class: 'h-4 w-4',
              xmlns: 'http://www.w3.org/2000/svg',
              fill: 'none',
              viewBox: '0 0 24 24',
              stroke: 'currentColor',
              'stroke-width': '1.5',
              'aria-hidden': 'true',
            },
            [
              h('path', {
                'stroke-linecap': 'round',
                'stroke-linejoin': 'round',
                d: 'M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99',
              }),
            ]
          ),
        ]
      )

      const deleteButton = h(
        'button',
        {
          class:
            'p-2 text-red-500 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg cursor-pointer hover:scale-105 transition-all duration-200',
          onClick: () => confirmDeleteUser(user),
          type: 'button',
          title: 'Destroy User',
        },
        [
          h('span', { class: 'sr-only' }, 'Destroy User'),
          h(
            'svg',
            {
              class: 'h-4 w-4',
              fill: 'none',
              stroke: 'currentColor',
              viewBox: '0 0 24 24',
              'aria-hidden': 'true',
            },
            [
              h('path', {
                'stroke-linecap': 'round',
                'stroke-linejoin': 'round',
                'stroke-width': '2',
                d: 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
              }),
            ]
          ),
        ]
      )

      return h(
        'div',
        {
          class: 'flex items-center gap-2 justify-end',
        },
        [editButton, canDeleteUser(user) && deleteButton].filter(Boolean)
      )
    },
  }),
]

watch(
  pagination,
  newPagination => {
    loading.value = true
    router.get(
      route('admin.user.index'),
      {
        page: newPagination.current_page,
        per_page: Number(newPagination.per_page),
      },
      {
        preserveState: true,
        preserveScroll: true,
        onFinish: () => (loading.value = false),
      }
    )
  },
  { deep: true }
)
</script>

<template>

  <Head title="Deleted Users Management" />
  <main class="main-container mx-auto max-w-7xl" aria-labelledby="users-management">
    <div class="container-border">
      <PageHeader title="Deleted Users Management" description="Manage system deleted users" :breadcrumbs="[
        { label: 'Dashboard', href: route('dashboard') },
        { label: 'System Settings', href: route('admin.setting.index') },
        { label: 'Users Management', href: route('admin.user.index') },
        { label: 'Deleted Users' },
      ]">
        <template #actions>
          <button @click="openDestroyAllUsersModal" class="btn btn-danger btn-sm">
            Destroy All
          </button>
        </template>
      </PageHeader>

      <section class="p-6 dark:bg-gray-900">
        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
          <DataTable :data="users.data" :columns="columns" :loading="loading" :pagination="pagination"
            empty-message="No deleted users found" empty-description="Users will appear here once deleted"
            export-file-name="deleted_users" @update:pagination="pagination = $event" />
        </div>
      </section>
    </div>
  </main>

  <Modal :show="showDeleteModal" @close="closeModal" size="md">
    <template #title>
      <div class="text-red-600 dark:text-red-400">Permanently Destroy User</div>
    </template>

    <template #default>
      <div class="space-y-4">
        <p class="text-sm text-gray-500 dark:text-gray-400">
          Are you sure you want to permanently destroy this user?
        </p>
        <p class="text-sm text-red-400">This action cannot be undone.</p>
        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 dark:border-amber-800 dark:bg-amber-900/20">
          <div class="flex items-center gap-2">
            <svg class="h-10 w-10 flex-shrink-0 text-amber-600 dark:text-amber-400" fill="currentColor"
              viewBox="0 0 20 20">
              <path fill-rule="evenodd"
                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                clip-rule="evenodd" />
            </svg>
            <p class="text-sm text-amber-700 dark:text-amber-300">
              This will permanently destroy the user's account and all associated data. This will
              erase the user from the app and is not recoverable.
            </p>
          </div>
        </div>
        <div v-if="userToDelete"
          class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
          <h4 class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">User details:</h4>
          <dl class="space-y-1">
            <div class="flex gap-2">
              <dt class="text-sm text-gray-500 dark:text-gray-400">Name:</dt>
              <dd class="text-sm text-gray-900 dark:text-gray-100">
                {{ userToDelete.name }}
              </dd>
            </div>
            <div class="flex gap-2">
              <dt class="text-sm text-gray-500 dark:text-gray-400">Email:</dt>
              <dd class="text-sm text-gray-900 dark:text-gray-100">
                {{ userToDelete.email }}
              </dd>
            </div>

            <div class="flex gap-2">
              <dt class="text-sm text-gray-500 dark:text-gray-400">Verified:</dt>
              <dd class="text-sm text-gray-900 dark:text-gray-100">
                {{ userToDelete.email_verified_at ? 'Yes' : 'No' }}
              </dd>
            </div>

            <div class="flex gap-2">
              <dt class="text-sm text-gray-500 dark:text-gray-400">Disabled:</dt>
              <dd class="text-sm text-gray-900 dark:text-gray-100">
                {{ userToDelete.disable_account ? 'Yes' : 'No' }}
              </dd>
            </div>

            <div class="flex gap-2">
              <dt class="text-sm text-gray-500 dark:text-gray-400">Role:</dt>
              <dd class="text-sm text-gray-900 dark:text-gray-100">
                <RolesBadges :roles="userToDelete.roles" />
              </dd>
            </div>

            <div class="flex gap-2">
              <dt class="text-sm text-gray-500 dark:text-gray-400">Created At:</dt>
              <dd class="text-sm text-gray-900 dark:text-gray-100">
                {{ userToDelete.created_at_full }}
              </dd>
            </div>

            <div class="flex gap-2">
              <dt class="text-sm text-gray-500 dark:text-gray-400">Deleted At:</dt>
              <dd class="text-sm text-gray-900 dark:text-gray-100">
                {{ userToDelete.deleted_at_full }}
              </dd>
            </div>

            <div class="flex gap-2">
              <dt class="text-sm text-gray-500 dark:text-gray-400">Auto Delete On:</dt>
              <dd class="text-sm text-gray-900 dark:text-gray-100">
                {{ checkAutoDeleteStatus(userToDelete) }}
              </dd>
            </div>
          </dl>
        </div>
      </div>
    </template>

    <template #footer>
      <div class="flex justify-end gap-8">
        <button @click="closeModal" type="button"
          class="cursor-pointer px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-500 dark:text-gray-200 dark:hover:text-gray-400">
          Cancel
        </button>
        <button @click="destroyUser" type="button" class="btn btn-danger btn-sm" :disabled="false">
          Confirm
        </button>
      </div>
    </template>
  </Modal>

  <Modal :show="showDestroyAllUsersModal" @close="closeModal" size="lg">
    <template #title>Destroy All Deleted Users</template>

    <template #default>
      <div class="w-full space-y-8">
        <div class="mb-4 flex flex-col text-center">
          <div class="my-4 flex justify-center text-red-600">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="size-20">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
          </div>
          <h1 class="mb-4 text-red-200">Warning: This will destroy all deleted</h1>
          <h2 class="text-red-200">This action cannot be undone</h2>
        </div>
        <div class="mb-3 flex justify-center space-y-4">
          <FormCheckbox v-model="form.confirm_destroy_all" label="I understand this action cannot be undone"
            description="This will destroy all deleted users and this action cannot be undone."
            :error="form.errors.confirm_destroy_all" />
        </div>
      </div>
    </template>

    <template #footer>
      <div class="flex justify-end gap-8">
        <button @click="closeModal" type="button"
          class="cursor-pointer text-sm font-medium text-gray-700 hover:text-gray-500 dark:text-gray-200 dark:hover:text-gray-400">
          Cancel
        </button>
        <button @click="destroyAllUsers" type="button" class="btn btn-danger btn-sm" :disabled="form.processing">
          Confirm
        </button>
      </div>
    </template>
  </Modal>
</template>
