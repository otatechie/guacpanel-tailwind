<template>

    <div class="flex justify-center">
        <img class="h-24 w-auto" :src="logoUrl" alt="Afropulse logo" />
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
