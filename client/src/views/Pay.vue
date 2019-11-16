<template>
  <div>
    <v-dialog v-model="dialog" width="500">
      <v-card>
        <v-form @submit.prevent="submit">
          <v-card-title class="headline" primary-title>
            {{ $t("pay.dialog.title") }}
          </v-card-title>
          <v-card-text>
            <RoundSummary :round="round"/>
            <v-text-field v-model="description"
                          outlined
                          rounded
                          hide-details
                          dense
                          :placeholder="$t('pay.description.label')"
            ></v-text-field>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn text @click="dialog = false">
              {{ $t("button.cancel") }}
            </v-btn>
            <v-btn depressed color="primary" type="submit">
              {{ $t("button.ok") }}
            </v-btn>
          </v-card-actions>
        </v-form>
      </v-card>
    </v-dialog>

    <v-form v-model="valid" @submit.prevent="confirm">
      <h1>{{ $t("pay.title", [$route.params.payer]) }}</h1>
      <div class="price-row">
        <div>
          <v-text-field class="price"
                        v-model="price"
                        :rules="[required, positive]"
                        type="number"
                        :autofocus="!price"
                        outlined
                        rounded
                        single-line
                        reverse
                        :suffix="$t('pay.price.label')"
          ></v-text-field>
        </div>
        <v-switch v-model="total" class="total" color="primary" inset :label="$t('pay.price.total')"/>
      </div>

      <h2>{{ $t("pay.subtitle") }}</h2>
      <v-list v-if="group">
        <v-list-item v-for="(member, i) in group.members" :key="i">
          <v-list-item-content>
            <v-list-item-title>{{ member.name }}</v-list-item-title>
          </v-list-item-content>

          <v-list-item-action class="amount-buttons">
            <span v-if="consumers[member.name]" class="cost">{{ memberCost(member.name).toFixed(2) }}</span>
            <v-btn icon ripple
                   @click="--consumers[member.name]"
                   :disabled="consumers[member.name] < 1">
              <v-icon large color="primary">mdi-minus-circle</v-icon>
            </v-btn>
            <span class="amount">{{ consumers[member.name] }}</span>
            <v-btn icon ripple
                   @click="++consumers[member.name]">
              <v-icon large color="primary">mdi-plus-circle</v-icon>
            </v-btn>
          </v-list-item-action>
        </v-list-item>
      </v-list>
      <Buttons :text="$t('button.pay') + ' ' + totalPrice.toFixed(2)"
               :valid="valid && enoughConsumers"/>
    </v-form>
  </div>
</template>

<script>
import "@/assets/css/form.css"
import Buttons from "@/components/Buttons";
import {mapActions, mapState, mapMutations} from "vuex";
import RoundSummary from "@/components/RoundSummary";

export default {
    name: "Pay",
    components: {RoundSummary, Buttons},
    data() {
        return {
            valid: false,
            price: null,
            total: false,
            consumers: {},
            description: null,

            dialog: false,
            round: {},

            required: value => !!value || this.$t("validation.required"),
            positive: value => value >= 0.01 || this.$t("validation.positive"),
        }
    },
    computed: {
        ...mapState(["group", "refresh"]),

        enoughConsumers() {
            return Object.values(this.consumers).some(amount => amount > 0);
        },
        totalAmount() {
            return Object.values(this.consumers).reduce((a, b) => a + b, 0);
        },
        totalPrice() {
            return this.total ? Number(this.price) : this.price * this.totalAmount;
        }
    },
    methods: {
        ...mapActions(["fetchGroup", "postRound"]),
        ...mapMutations(["onRefresh", "setTitle"]),

        async submit() {
            this.dialog = false;

            localStorage.setItem("price", (this.price * 100).toFixed(0));
            if (this.total)
                localStorage.setItem("total", "true");
            else
                localStorage.removeItem("total");

            if (this.description)
                this.round.description = this.description;

            await this.postRound({id: this.$route.params.id, rounds: this.round});
            this.$router.go(-1);
        },
        confirm() {
            const price = Math.round(this.price * 100 / (this.total ? this.totalAmount : 1));
            this.round = {
                payer: this.$route.params.payer,
                price: price,
                consumers:
                    Object.fromEntries(Object.entries(this.consumers)
                        .filter(([, amount]) => amount > 0))
            };

            this.dialog = true;
        },

        memberCost(name) {
            return this.price * this.consumers[name] / (this.total ? this.totalAmount : 1);
        }
    },
    created() {
        this.setTitle(this.$i18n.t("pay.title", [this.$route.params.payer]));

        const price = localStorage.getItem("price");
        if (price)
            this.price = (parseFloat(price) / 100).toFixed(2);

        this.total = localStorage.getItem("total") || false;

        this.onRefresh(async () => {
            await this.fetchGroup(this.$route.params.id);
            this.consumers = this.group.members
                .reduce((obj, x) => (obj[x.name] = this.consumers[x.name] || 0, obj), {});
        });

        this.refresh();
    },
    beforeCreate() {
        scrollTo(0, 0);
    },
}
</script>

<style scoped>
.headline {
  word-break: normal;
}

.price-row {
  display: flex;
  justify-content: space-evenly;
  align-items: baseline;
}

.v-text-field.price {
  width: 140px;
  margin: auto;
}

.total {
  margin: 0;
}

.amount-buttons {
  flex-direction: row;
  align-items: center;
  margin-top: 6px;
}

.amount {
  font-size: 1.6em;
  font-weight: bold;
  margin-left: 15px;
  margin-right: 15px;
}

.cost {
  color: darkgrey;
  margin-right: 20px;
}
</style>
