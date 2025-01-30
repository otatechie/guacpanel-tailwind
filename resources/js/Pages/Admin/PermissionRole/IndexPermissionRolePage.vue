<template>

    <Head title="Permission & Roles" />

    <slot>
        <div class="container-border mx-5">
           <div class="mx-0.5">
            <Tabs value="0" class="compact-tabs">
                <TabList>
                    <Tab value="0" class="font-bold text-lg">Users</Tab>
                    <Tab value="1" class="font-bold text-lg">Roles</Tab>
                    <Tab value="2" class="font-bold text-lg">Permissions</Tab>
                </TabList>
                <TabPanels>
                    <TabPanel value="0">
                        <IndexUserTab :users="props.users"></IndexUserTab>
                    </TabPanel>

                    <TabPanel value="1">
                        <Role :roles="props.roles"></Role>
                    </TabPanel>

                    <TabPanel value="2">
                    </TabPanel>
                </TabPanels>
            </Tabs>
           </div>
        </div>
    </slot>
</template>

<script setup>
import { ref, watch } from 'vue';
import { Head } from '@inertiajs/vue3';
import Default from '../../../Layouts/Default.vue';

import Role from './IndexRoleTab.vue';
import IndexUserTab from './IndexUserTab.vue';

defineOptions({
    layout: Default,
});

const props = defineProps({
    roles: {
        type: Object,
        required: true
    },
    permissions: {
        type: Object,
        required: true
    },
    users: {
        type: Object,
        required: true
    }
});

</script>
