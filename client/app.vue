<template>
  <v-app>
    <!-- Header -->
    <v-app-bar
      color="surface"
      class="app-header"
      elevation="0"
    >
      <v-container class="d-flex align-center">
        <v-app-bar-title class="text-h6 font-weight-medium">
          Scandiweb Test Assignment
        </v-app-bar-title>
      </v-container>
    </v-app-bar>

    <!-- Main Content -->
    <v-main>
      <NuxtPage />
    </v-main>

    <!-- Footer -->
    <v-footer
      color="surface"
      class="app-footer"
      app
    >
      <v-container class="text-center text-caption text-grey">
        Scandiweb Test assignment
      </v-container>
    </v-footer>
  </v-app>
</template>

<script setup lang="ts">
// App-level setup
useHead({
  titleTemplate: (title) => {
    return title ? `${title} | Scandiweb Test Assignment` : 'Scandiweb Test Assignment'
  },
})
</script>
