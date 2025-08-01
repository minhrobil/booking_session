<template>
  <v-container
    class="d-flex align-center justify-center fill-height bg-grey-lighten-5"
    fluid
  >
    <div style="max-width: 500px; width: 100%">
      <div class="mb-8 text-center">
        <h1 class="text-h3 font-weight-bold text-primary">
          Sport Booking
        </h1>
        <div class="text-subtitle-2 text-grey-darken-1">
          Reserve your favorite session today
        </div>
      </div>

      <v-card
        class="py-6 px-4"
        color="grey-lighten-4"
        elevation="3"
        rounded="xl"
        variant="elevated"
      >
        <template #subtitle>
          <div class="text-subtitle-1 text-center text-grey-darken-2 mb-4">
            Booking now!
          </div>
        </template>

        <v-card-actions class="justify-center">
          <v-btn
            color="primary"
            variant="flat"
            size="large"
            rounded
            elevation="2"
            @click="goToContact"
          >
            Get Started
          </v-btn>
        </v-card-actions>
      </v-card>
    </div>
  </v-container>
</template>

<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

const goToContact = () => {
  router.push('/session')
}
</script>
