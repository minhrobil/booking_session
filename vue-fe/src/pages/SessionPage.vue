<template>
  <v-container class="py-8 px-4 bg-grey-lighten-5 fill-height" fluid>
    <v-overlay :value="loading" absolute>
      <v-progress-circular indeterminate size="64" color="primary" />
    </v-overlay>

    <div class="add-to-cart-container" v-if="!showCardDetail">
      <v-btn
        color="primary"
        size="large"
        class="px-6 py-4 text-body-1 font-weight-bold"
        :disabled="totalSlots === 0"
        @click="goToCardDetail"
      >
        Add to Cart ({{ totalSlots }} slots - ${{ totalPrice.toFixed(2) }})
      </v-btn>
    </div>

    <v-row no-gutters>
      <v-col cols="12" md="4" class="pr-6">
        <h1 class="text-h4 font-weight-bold text-primary mb-4">Session Selection</h1>
        <v-date-picker
          v-model="selectedDate"
          :min="minDate"
          show-adjacent-months
          color="primary"
        />
      </v-col>

      <v-col cols="12" md="8">
        <v-card
          v-for="session in filteredSessions"
          :key="session.id"
          class="mb-6 pa-4"
          color="white"
          elevation="3"
        >
          <v-card-title class="text-h6 font-weight-bold">{{ session.type }}</v-card-title>
          <v-card-text>
            <v-row dense>
              <v-col
                v-for="slot in session.slots"
                :key="slot.id"
                cols="6"
                sm="4"
                md="3"
              >
                <v-btn
                  block
                  class="text-body-2 py-3"
                  :color="getSlotColor(session.id, slot.time)"
                  :disabled="slot.isBooked"
                  variant="flat"
                  @click="openTrainerDialog(session.id, slot)"
                >
                  {{ slot.time }}
                  <template v-if="selectedTrainers[session.id]?.[slot.time]">
                    <div class="selected-trainer-wrapper">
                      👤 {{ selectedTrainers[session.id][slot.time] }}
                      <v-icon
                        size="16"
                        color="red lighten-2"
                        class="remove-icon"
                        @click.stop="clearTrainer(session.id, slot.time)"
                      >
                        mdi-close-circle
                      </v-icon>
                    </div>
                  </template>
                </v-btn>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <transition name="slide-in-right">
      <div v-if="showCardDetail" class="cart-detail-wrapper">
        <div class="background-overlay" @click="showCardDetail = false"></div>
        <div class="cart-detail-sidebar" @click.stop>
          <v-btn icon class="close-btn" @click="showCardDetail = false" aria-label="Close cart detail">
            <v-icon>mdi-close</v-icon>
          </v-btn>
          <h2 class="mb-4">Cart Detail</h2>

          <div v-if="totalSlots === 0" class="no-selection">
            No slots selected.
          </div>

          <v-list v-else two-line class="cart-list">
            <v-list-item
              v-for="(slots, sessionId) in selectedTrainers"
              :key="sessionId"
              class="session-group"
            >
              <v-list-item-content>
                <v-list-item-title class="font-weight-bold d-flex justify-space-between align-center">
                  <span>{{ getSessionType(sessionId) }}</span>
                  <span class="trainer-price-header">
                    Trainer & Price
                  </span>
                </v-list-item-title>
                <v-list-item-subtitle>
                  <div
                    v-for="(trainer, slotTime) in slots"
                    :key="slotTime"
                    class="slot-item"
                  >
                    <div class="slot-time">{{ slotTime }}</div>
                    <div class="slot-trainer">
                      Trainer: {{ getTrainerName(trainer) }}
                    </div>
                    <div class="slot-price">
                      ${{ getSlotPrice(sessionId, slotTime).toFixed(2) }}
                    </div>
                  </div>
                </v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
          </v-list>

          <div class="total-price">
            Total Price: ${{ totalPrice.toFixed(2) }}
          </div>

          <v-btn
            color="primary"
            class="booking-btn"
            :disabled="totalSlots === 0"
            @click="goToBooking"
          >
            Booking
          </v-btn>
        </div>
      </div>
    </transition>

    <v-dialog v-model="trainerDialog.visible" max-width="500">
      <v-card class="pa-4" color="grey-lighten-4">
        <v-card-title class="text-h6 font-weight-bold">
          Select Trainer for {{ trainerDialog.slot?.time }}
        </v-card-title>
        <v-card-text>
          <v-row dense>
            <v-col
              v-for="trainer in trainers"
              :key="trainer"
              cols="12"
              sm="6"
            >
            <v-btn
              block
              class="text-body-1 py-5 font-weight-medium"
              elevation="2"
              :color="isTrainerSelected(trainer.id) ? 'primary' : 'grey-lighten-3'"
              variant="flat"
              @click="selectTrainer(trainerDialog.sessionId, trainerDialog.slot?.time, trainer.id)"
            >
              {{ trainer.name }}
            </v-btn>
            </v-col>
          </v-row>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn variant="text" @click="trainerDialog.visible = false">Cancel</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
const API_BASE_URL = import.meta.env.VITE_API_BASE_URL

const router = useRouter()

const selectedDate = ref(new Date())
const minDate = new Date()
const sessions = ref([])
const loading = ref(false)
const showCardDetail = ref(false)

const trainers = [
  { id: 1, name: 'John' },
  { id: 2, name: 'Alex' },
  { id: 3, name: 'Max' }
]

const trainerDialog = ref({
  visible: false,
  sessionId: null,
  slot: null
})

const selectedTrainers = ref({})

const fetchSessions = async (date) => {
  loading.value = true
  try {
    const formattedDate = date.toISOString().slice(0, 10)
    console.log("API_BASE_URL", API_BASE_URL);
    
    const res = await axios.get(`${API_BASE_URL}/sessions`, {
      params: { date: formattedDate }
    })
    sessions.value = res.data.map((s, idx) => ({
      id: idx + 1,
      type: s.sessionType,
      date: s.date,
      slots: s.timeSlots.map(slot => ({
        id: slot.id,
        time: `${slot.start} - ${slot.end}`,
        trainer: slot.trainer,
        isBooked: slot.isBooked,
        price: parseFloat(slot.price)
      }))
    }))
  } catch (err) {
    console.error('Failed to fetch sessions:', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchSessions(selectedDate.value)
})

watch(selectedDate, (newDate) => {
  selectedTrainers.value = {} 
  fetchSessions(newDate)
})

const openTrainerDialog = (sessionId, slot) => {
  if (slot.isBooked) return
  trainerDialog.value = {
    visible: true,
    sessionId,
    slot
  }
}

const selectTrainer = (sessionId, slotTime, trainerId) => {
  if (!selectedTrainers.value[sessionId]) {
    selectedTrainers.value[sessionId] = {}
  }
  selectedTrainers.value[sessionId][slotTime] = trainerId
  trainerDialog.value.visible = false
}

const clearTrainer = (sessionId, slotTime) => {
  if (selectedTrainers.value[sessionId]) {
    delete selectedTrainers.value[sessionId][slotTime]
    if (Object.keys(selectedTrainers.value[sessionId]).length === 0) {
      delete selectedTrainers.value[sessionId]
    }
  }
}

const isTrainerSelected = (trainerId) => {
  const sessionId = trainerDialog.value.sessionId
  const slotTime = trainerDialog.value.slot?.time
  return selectedTrainers.value[sessionId]?.[slotTime] === trainerId
}

const getTrainerName = (trainerId) => {
  return trainers.find(t => t.id === trainerId)?.name || 'Unknown'
}

const getSlotColor = (sessionId, slotTime) => {
  return selectedTrainers.value[sessionId]?.[slotTime] ? 'primary' : 'grey-lighten-3'
}

const filteredSessions = computed(() => sessions.value)

const totalSlots = computed(() =>
  Object.values(selectedTrainers.value).reduce(
    (sum, slots) => sum + Object.keys(slots).length,
    0
  )
)

const totalPrice = computed(() => {
  let total = 0
  for (const session of sessions.value) {
    const selected = selectedTrainers.value[session.id]
    if (selected) {
      for (const slot of session.slots) {
        if (selected[slot.time]) {
          total += slot.price
        }
      }
    }
  }
  return total
})

const goToCardDetail = () => {
  showCardDetail.value = true
}

const getSessionType = (sessionId) => {
  const session = sessions.value.find(s => s.id == sessionId)
  return session ? session.type : 'Unknown Session'
}

const getSlotPrice = (sessionId, slotTime) => {
  const session = sessions.value.find(s => s.id == sessionId)
  if (!session) return 0
  const slot = session.slots.find(s => s.time === slotTime)
  return slot ? slot.price : 0
}

const getSession = (sessionId) => {
  return sessions.value.find(s => s.id == sessionId)
}

const getSlot = (sessionId, slotTime) => {
  const session = sessions.value.find(s => s.id == sessionId)
  if (!session) return null
  const slot = session.slots.find(s => s.time === slotTime)
  return slot
}

const getTrainer = (trainerId) => {
  return trainers.find(s => s.id == trainerId)
}

const goToBooking = () => {
  const bookings = []
  console.log(selectedTrainers.value);
  
  for (const sessionId in selectedTrainers.value) {
    for (const slotTime in selectedTrainers.value[sessionId]) {
      const trainerId = selectedTrainers.value[sessionId][slotTime]
      const session = getSession(Number(sessionId))
      const slot = getSlot(Number(sessionId), slotTime)
      const trainer = getTrainer(trainerId)
      if (slot && trainerId) {
        bookings.push({ session, slot, trainer })
      }
    }
  }
  console.log(bookings);

  router.push({
    name: 'Booking',
    query: {
      bookings: JSON.stringify(bookings)
    }
  })
}
</script>


<style scoped>
.add-to-cart-container {
  position: fixed;
  top: 24px;
  right: 24px;
  z-index: 1200;
}

.v-btn {
  white-space: normal;
}

.selected-trainer-wrapper {
  position: relative;
  font-size: 0.875rem;
  margin-top: 4px;
  padding-right: 20px;
}

.remove-icon {
  position: absolute;
  top: -4px;
  right: -4px;
  z-index: 1;
  background-color: white;
  border-radius: 50%;
}

.v-col.pr-6 {
  padding-right: 24px !important;
}

/* Modal: Card Detail */
.cart-detail-wrapper {
  position: fixed;
  top: 0;
  right: 0;
  height: 100vh;
  width: 100vw;
  z-index: 1300;
  display: flex;
  justify-content: flex-end;
  align-items: stretch;
  pointer-events: none;
}

.background-overlay {
  flex-grow: 1;
  background: rgba(0, 0, 0, 0.1);
  backdrop-filter: blur(1px);
  pointer-events: auto;
}

.cart-detail-sidebar {
  position: relative;
  width: 360px;
  max-width: 90vw;
  background: white;
  box-shadow: -4px 0 12px rgba(0, 0, 0, 0.12);
  padding: 24px 24px 100px;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  pointer-events: auto;
  z-index: 1400;
}

.close-btn {
  position: absolute;
  top: 12px;
  right: 12px;
  color: #666;
}

.cart-list {
  flex-grow: 1;
  overflow-y: auto;
  max-height: calc(100vh - 160px);
}

.session-group {
  margin-bottom: 16px;
  border-bottom: 1px solid #eee;
  padding-bottom: 8px;
}

.trainer-price-header {
  font-size: 0.85rem;
  font-weight: 600;
  color: #555;
}

.slot-item {
  display: flex;
  justify-content: space-between;
  font-size: 0.9rem;
  margin-top: 4px;
  gap: 6px;
}

.slot-time {
  flex: 1 1 40%;
  font-weight: 500;
}

.slot-trainer {
  flex: 1 1 40%;
  color: #555;
}

.slot-price {
  flex: 1 1 20%;
  text-align: right;
  font-weight: 600;
  color: #000;
}

.total-price {
  font-size: 1.2rem;
  text-align: right;
  margin-top: 12px;
  font-weight: 700;
}

.booking-btn {
  position: fixed;
  bottom: 24px;
  right: 24px;
  width: 312px;
  max-width: 90vw;
  height: 48px;
  font-weight: 700;
  z-index: 1500;
}

/* Animation */
.slide-in-right-enter-active,
.slide-in-right-leave-active {
  transition: transform 0.3s ease;
}

.slide-in-right-enter-from,
.slide-in-right-leave-to {
  transform: translateX(100%);
}

.slide-in-right-enter-to,
.slide-in-right-leave-from {
  transform: translateX(0);
}
</style>
