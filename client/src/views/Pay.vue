<template>
  <v-form v-model="valid" @submit.prevent="submit">
    <h1>{{ $t("pay.title", [$route.params.payer]) }}</h1>
    <v-text-field class="price"
                  v-model="price"
                  :rules="[required, positive]"
                  type="number"
                  :autofocus="!price"
                  outlined
                  single-line
                  reverse
                  :suffix="$t('pay.price.label')"
    ></v-text-field>

    <h2>{{ $t("pay.subtitle") }}</h2>
    <v-list v-if="group">
      <v-list-item v-for="(member, i) in group.members" :key="i">
        <v-list-item-content>
          <v-list-item-title>{{ member.name }}</v-list-item-title>
        </v-list-item-content>

        <v-list-item-action class="amount-buttons">
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
</template>

<script>
import "@/assets/css/form.css"
import Buttons from "@/components/Buttons";
import {mapActions, mapState, mapMutations} from "vuex";

export default {
    name: "Pay",
    components: {Buttons},
    data() {
        return {
            valid: false,
            price: null,
            consumers: {},

            required: value => !!value || this.$t("validation.required"),
            positive: value => value >= 0.01 || this.$t("validation.positive"),
        }
    },
    computed: {
        ...mapState(["group", "refresh"]),

        enoughConsumers() {
            return Object.values(this.consumers).some(amount => amount > 0);
        },
        totalPrice() {
            return this.price * Object.values(this.consumers).reduce((a, b) => a + b, 0);
        }
    },
    methods: {
        ...mapActions(["fetchGroup", "postRound"]),
        ...mapMutations(["onRefresh", "setTitle"]),

        async submit() {
            const data = {
                payer: this.$route.params.payer,
                price: this.price * 100,
                consumers: this.consumers
            };

            localStorage.setItem("price", data.price);
            await this.postRound({id: this.$route.params.id, rounds: data});
            this.$router.go(-1);
        }
    },
    created() {
        this.setTitle(this.$i18n.t("pay.title", [this.$route.params.payer]));

        const price = localStorage.getItem("price");
        if (price)
            this.price = (parseFloat(price) / 100).toFixed(2);

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
.v-text-field.price {
  width: 140px;
  margin: auto;
}

.amount-buttons {
  flex-direction: row;
  align-items: center;
  margin-top: 6px;
}

.amount {
  font-size: 1.5em;
  font-weight: bold;
  margin-left: 15px;
  margin-right: 15px;
}
</style>
