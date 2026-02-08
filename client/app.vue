<template>
    <NuxtLayout>
        <NuxtPage/>
    </NuxtLayout>
</template>

<script setup lang="ts">
useHead({
    titleTemplate: (title) =>
    {
        return title ? `${title} | Scandiweb Test Assignment` : 'Scandiweb Test Assignment'
    },
})
</script>
