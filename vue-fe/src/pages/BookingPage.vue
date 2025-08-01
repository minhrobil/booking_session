<template>
  <v-container class="py-8 px-4" fluid>
    <v-row justify="center">
      <v-col cols="12" md="8">
        <v-card class="pa-6 elevation-3" style="position: relative">
          <v-overlay
            :model-value="loading"
            absolute
            class="d-flex align-center justify-center"
            style="z-index: 9999; background-color: rgba(255, 255, 255, 0.8)"
          >
            <v-progress-circular indeterminate color="primary" size="64" width="6" />
          </v-overlay>

          <v-card-title class="text-h5 font-weight-bold">
            Booking Confirmation
          </v-card-title>

          <div
            v-for="(item, index) in selectedTrainers"
            :key="index"
            class="my-2 pa-3 rounded bg-grey-lighten-4 d-flex justify-space-between align-center"
          >
            <div class="text-subtitle-2">
              <strong>{{ item.timeSlot.startTime }} - {{ item.timeSlot.endTime }}</strong>
            </div>
            <div class="text-subtitle-2">
              <strong>{{ item.trainerName }}</strong>
            </div>
            <div class="text-subtitle-2 text-primary font-weight-bold">
              {{ item.timeSlot.price }}$
            </div>
          </div>

          <v-divider class="my-4" />
          <div class="text-right font-weight-bold text-h6">
            Total: {{ totalPrice }}$
          </div>

          <v-form ref="formRef" v-model="valid" class="mt-6">
            <v-text-field
              v-model="form.name"
              label="Name"
              :rules="[rules.required]"
              required
              class="mb-4"
            />
            <v-text-field
              v-model="form.email"
              label="Email"
              :rules="[rules.required, rules.email]"
              required
              class="mb-4"
            />
            <v-text-field
              v-model="form.phone"
              label="Phone Number"
              :rules="[rules.required]"
              required
              class="mb-4"
            />
          </v-form>

          <v-checkbox
            v-model="form.terms"
            label="I agree to the terms and conditions"
            class="mb-4"
            :error="!form.terms && submitted"
            :error-messages="!form.terms && submitted ? [rules.mustAgree(form.terms)] : []"
          />

          <v-btn color="primary" class="text-none px-6" @click="submit">
            Confirm Booking
          </v-btn>
        </v-card>
      </v-col>
    </v-row>

    <v-dialog v-model="successDialog" width="400" persistent>
      <v-card>
        <v-card-title class="text-h6 font-weight-bold">Booking Successful</v-card-title>
        <v-card-text>Thank you! Your booking has been confirmed.</v-card-text>
        <v-card-actions>
          <v-btn color="primary" text @click="goHome">OK</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="errorDialog" width="400">
      <v-card>
        <v-card-title class="text-h6 font-weight-bold">Error</v-card-title>
        <v-card-text>{{ errorMessage }}</v-card-text>
        <v-card-actions>
          <v-btn color="error" text @click="errorDialog = false">Close</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const router = useRouter()

let rawBookings = []
try {
  rawBookings = JSON.parse(route.query.bookings || '[]')
} catch (e) {
  console.error('Failed to parse bookings from query:', e)
}

const selectedTrainers = ref(
  rawBookings.map(item => ({
    timeSlot: {
      id: item.slot.id,
      startTime: item.slot.time.split('-')[0],
      endTime: item.slot.time.split('-')[1],
      price: item.slot.price || 0
    },
    trainerId: item.trainer.id,
    trainerName: item.trainer.name
  }))
)

const form = ref({
  name: '',
  email: '',
  phone: '',
  terms: false
})

const submitted = ref(false)
const formRef = ref(null)
const valid = ref(false)
const successDialog = ref(false)
const errorDialog = ref(false)
const errorMessage = ref('Something went wrong. Please try again later.')
const loading = ref(false)

const rules = {
  required: v => !!v || 'Required.',
  email: v => /.+@.+\..+/.test(v) || 'Invalid email.',
  mustAgree: v => v || 'You must agree to continue.'
}

const totalPrice = computed(() =>
  selectedTrainers.value.reduce((sum, item) => sum + (item.timeSlot?.price || 0), 0)
)

const submit = async () => {
  submitted.value = true
  const formIsValid = await formRef.value.validate()

  if (formIsValid.valid && form.value.terms) {
    const payload = {
      fullName: form.value.name,
      email: form.value.email,
      phoneNumber: form.value.phone,
      acceptedTerms: true,
      bookings: selectedTrainers.value.map(item => ({
        sessionTimeSlotId: item.timeSlot.id,
        trainerId: item.trainerId
      }))
    }

    try {
      loading.value = true
      const API_BASE_URL = import.meta.env.VITE_API_BASE_URL
      const response = await axios.post(`${API_BASE_URL}/bookings`, payload, {
        headers: { 'Content-Type': 'application/json' }
      })

      if (response.status === 200) {
        successDialog.value = true
      } else {
        errorMessage.value = 'Unexpected response from server.'
        errorDialog.value = true
      }
    } catch (err) {
      console.error('Booking error:', err)
      errorMessage.value =
        err?.response?.data?.details || 'Something went wrong. Please try again later.'
      errorDialog.value = true
    } finally {
      loading.value = false
    }
  }
}

const goHome = () => {
  successDialog.value = false
  router.push({ name: 'Home' })
}
</script>

<style scoped>
.text-subtitle-2 {
  font-size: 14px;
}
</style>
