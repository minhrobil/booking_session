<template>
  <v-menu
    v-model="menu"
    :close-on-content-click="false"
    offset-y
    transition="scale-transition"
  >
    <template v-slot:activator="{ on, attrs }">
      <v-card
        v-bind="attrs"
        v-on="on"
        class="pa-3 text-center"
        :color="getColor()"
        :class="{ 'elevation-3': !slot.isBooked }"
        style="cursor: pointer; width: 150px;"
        @click.stop="handleClick"
      >
        <div>{{ slot.time }}</div>
        <div v-if="slot.chosenTrainer" class="text-caption grey--text">
          {{ slot.chosenTrainer }}
        </div>
      </v-card>
    </template>

    <v-list v-if="!slot.isBooked">
      <v-list-item
        v-for="trainer in trainers"
        :key="trainer"
        @click="selectTrainer(trainer)"
      >
        <v-list-item-title>{{ trainer }}</v-list-item-title>
      </v-list-item>
    </v-list>
  </v-menu>
</template>

<script>
export default {
  props: {
    slot: Object,
    trainers: Array,
  },
  data() {
    return {
      menu: false,
    };
  },
  methods: {
    getColor() {
      if (this.slot.isBooked) return "error lighten-4";
      if (this.slot.chosenTrainer) return "success lighten-4";
      return "grey lighten-3";
    },
    handleClick() {
      if (!this.slot.isBooked) {
        this.menu = true;
      }
    },
    selectTrainer(trainer) {
      this.slot.chosenTrainer = trainer;
      this.menu = false;
    },
  },
};
</script>

<style scoped>
.text-caption {
  font-size: 12px;
}
</style>
