<template>
  <div>
    <round-dialog v-model="dialog" :round="selectedRound"/>

    <h1>{{ $route.params.member }}</h1>

    <template v-if="member">
      <div class="stat">
        <span><strong>{{ paid.length }}</strong> {{ $t("member.paid") }}</span>
        <span class="paid">+{{ (member.paid / 100).toFixed(2) }}</span>
      </div>
      <div class="stat">
        <span><strong>{{ consumed.length }}</strong> {{ $t("member.consumed") }}</span>
        <span class="consumed">&minus;{{ (member.consumed / 100).toFixed(2) }}</span>
      </div>
    </template>

    <h2>{{ $t("member.rounds") }}</h2>
    <v-list v-infinite-scroll="loadMore">
      <div v-for="(round, i) in visibleRounds" :key="i">

        <v-divider v-if="i === 0 || !sameDay(round.date, visibleRounds[i - 1].date)"/>
        <v-subheader v-if="i === 0 || !sameDay(round.date, visibleRounds[i - 1].date)">
          {{ round.date | moment("dddd, LL") }}
        </v-subheader>

        <v-list-item :class="{ deleted: round.deleted, large: round.description }">

          <v-list-item-content class="round">
            <v-list-item-title class="round-title">
              <template v-if="round.description">
                <span>{{ round.description }}</span>
              </template>
              <template v-else>
                <v-icon class="round-icon">mdi-account</v-icon>
                <span>{{ round.payer }}</span>
              </template>
            </v-list-item-title>

            <v-list-item-subtitle class="subtitle">
              <v-icon class="subtitle-icon">mdi-clock-outline</v-icon>
              <span>{{ round.date | moment("H:mm") }}</span>
            </v-list-item-subtitle>
          </v-list-item-content>

          <v-list-item-action class="round-actions">
            <span class="cost">
              &minus;{{ (round.price * round.consumers[$route.params.member] / 100).toFixed(2) }}
            </span>
            <v-btn icon @click="showConsumers(round)">
              <v-icon large color="primary">mdi-information</v-icon>
            </v-btn>
          </v-list-item-action>
        </v-list-item>
      </div>
      <v-divider></v-divider>
    </v-list>

    <h2 v-if="!loading && !consumed.length">{{ $t("member.none") }}</h2>

    <scroll-progress v-if="visibleRounds.length < consumed.length"/>
  </div>
</template>

<script>
import {mapActions, mapGetters, mapMutations, mapState} from "vuex";
import RoundDialog from "@/components/RoundDialog";
import ScrollProgress from "@/components/ScrollProgress";

export default {
    name: "Member",
    components: {ScrollProgress, RoundDialog},
    data: () => ({
        dialog: false,
        selectedRound: null,
        amountDisplayed: 0
    }),
    computed: {
        ...mapState(["group", "rounds", "refresh"]),
        ...mapGetters(["loading"]),

        member() {
            return this.group?.members.find(({name}) => name === this.$route.params.member);
        },
        consumed() {
            return this.rounds.filter(({consumers}) => consumers.hasOwnProperty(this.$route.params.member))
        },
        paid() {
            return this.rounds.filter(({payer}) => payer === this.$route.params.member)
        },
        visibleRounds() {
            return this.consumed.slice(0, this.amountDisplayed);
        }
    },
    methods: {
        ...mapActions(["fetchGroup", "fetchRounds"]),
        ...mapMutations(["onRefresh"]),

        sameDay(date1, date2) {
            return this.$moment(date1).isSame(date2, "day");
        },
        loadMore() {
            this.amountDisplayed += 10;
            if (this.consumed.length > 0 && this.consumed.length < this.amountDisplayed)
                this.amountDisplayed = this.consumed.length;
        },
        showConsumers(round) {
            this.selectedRound = round;
            this.dialog = true;
        },
    },
    created() {
        this.onRefresh(() => {
            this.fetchRounds(this.$route.params.id);
            this.fetchGroup(this.$route.params.id);
        });

        this.refresh();
    }
}
</script>

<style scoped>
h1 {
  font-weight: normal;
  font-size: 2em;
  margin: 20px;
}

.stat {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin: 10px 20px;
}

.paid, .consumed {
  text-align: right;
  font-size: 1.2em;
}

.paid {
  color: mediumseagreen;
}

.consumed {
  color: indianred;
}

h2 {
  font-weight: normal;
  margin: 20px;
}

.v-list {
  padding: 0;
  border-top: none;
}

.round {
  padding: 0;
}

.v-subheader {
  justify-content: center;
}

.round-title {
  display: flex;
  align-items: center;
}

.round-title > span:last-child {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.round-icon {
  font-size: 1.1em;
  margin-right: 6px;
}

.subtitle {
  display: flex;
  align-items: center;
  margin-top: 6px
}

.subtitle-icon {
  font-size: 0.9em;
  line-height: 1.5;
  margin-right: 4px;
}

.round-actions {
  display: flex;
  flex-direction: initial;
  align-items: center;
}

.cost {
  color: indianred;
  font-size: 1.4em;
  margin-right: 20px;
}
</style>
