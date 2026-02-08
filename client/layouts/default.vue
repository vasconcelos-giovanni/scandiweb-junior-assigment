<template>
    <v-app>
        <v-app-bar flat color="surface" density="comfortable">
            <v-container class="d-flex align-center">
                <v-toolbar-title class="text-h6 font-weight-medium">
                    Scandiweb Test Assignment
                </v-toolbar-title>
            </v-container>
        </v-app-bar>

        <v-main>
            <slot/>
        </v-main>

        <v-footer color="surface" app>
            <v-container class="text-center text-caption text-grey">
                Scandiweb Test assignment
            </v-container>
        </v-footer>
    </v-app>
</template>
