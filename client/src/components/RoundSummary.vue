<template>
  <v-list>
    <v-list-item v-for="(amount, name) in round.consumers" :key="name">
      <v-list-item-content>
        <v-list-item-title>
          {{ name }}
        </v-list-item-title>
      </v-list-item-content>
      <v-list-item-action class="amount">
        {{ amount }} &times; {{ (parseFloat(round.price) / 100).toFixed(2) }}
      </v-list-item-action>
    </v-list-item>

    <v-divider></v-divider>

    <v-list-item>
      <v-list-item-content>
        <v-list-item-title class="total">
          {{ $t("rounds.dialog.total") }}
        </v-list-item-title>
      </v-list-item-content>
      <v-list-item-action class="amount">
        {{ drinksCount }}
        &times; {{ (parseFloat(round.price) / 100).toFixed(2) }}
        = {{ (parseFloat(totalPrice) / 100).toFixed(2) }}
      </v-list-item-action>
    </v-list-item>
  </v-list>
</template>

<script>
export default {
    name: "RoundSummary",
    props: {
        round: Object
    },
    computed: {
        drinksCount() {
            return Object.values(this.round.consumers).reduce((a, b) => a + b);
        },
        totalPrice() {
            return this.drinksCount * this.round.price
        }
    }
}
</script>

<style scoped>
.total {
  font-weight: bold;
}

.amount {
  text-align: right;
  font-weight: bold;
}
</style>
