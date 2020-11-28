<template>
  <v-dialog v-model="dialog" width="500">
    <v-card v-if="round">
      <v-card-title class="headline" primary-title>
        {{ $t("rounds.dialog.title", [round.payer]) }}
      </v-card-title>

      <v-card-subtitle class="dialog-subtitle">
        <div class="subtitle-row">
          <v-icon small>mdi-calendar</v-icon>
          <span>{{ round.date | moment("LL, H:mm") }}</span>
        </div>
        <div class="subtitle-row" v-if="round.description">
          <v-icon small>mdi-card-text-outline</v-icon>
          <span>{{ round.description }}</span>
        </div>
      </v-card-subtitle>

      <v-card-text class="dialog-content">
        <round-summary :round="round"/>
      </v-card-text>

      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn text @click="dialog = false">
          {{ $t("button.close") }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import RoundSummary from "@/components/RoundSummary";

export default {
    name: "RoundDialog",
    components: {RoundSummary},
    props: {
        value: {type: Boolean, required: true},
        round: Object
    },
    data() {
        return {dialog: this.value}
    },
    watch: {
        value(val) {
            this.dialog = val
        },
        dialog(val) {
            this.$emit("input", val)
        }
    }
}
</script>

<style scoped>
.headline {
  word-break: normal;
}

.v-card > .dialog-subtitle {
  background-color: whitesmoke;
  border-radius: 4px;
  margin: 0 20px;
  padding: 0 0 10px;
  font-size: 1.1em;
}

.subtitle-row {
  display: flex;
  flex-wrap: nowrap;
  align-items: flex-start;
  padding: 10px 10px 0;
}

.dialog-subtitle .v-icon {
  margin-right: 4px;
  padding: 3px 6px;
}

.v-card .dialog-content {
  padding-bottom: 0;
}
</style>
