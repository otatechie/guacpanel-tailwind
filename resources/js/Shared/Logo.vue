<template>

    <div class="flex justify-center">
        <img class="h-12 w-auto mb-4" :src="logoUrl" alt="Afropulse logo" />
    </div>

</template>

<script>
export default {
    data() {
        return {
            logoUrl: window.logoUrl,
        };
    },
};
</script>
