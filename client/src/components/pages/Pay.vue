<template>
  <v-form v-if="group" v-model="valid" @submit.prevent="submit()">
    <h1>{{ $t("pay.title") }} {{ payer }}</h1>
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
    <v-list>
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
             :valid="valid && enoughConsumers"
             @cancel="$emit('page', 'Group')"/>
  </v-form>
</template>

<script>
import "@/assets/css/form.css"
import Buttons from "@/components/Buttons";

export default {
    name: "Pay",
    components: {Buttons},
    data() {
        const consumers = {};
        if (this.group)
            for (const member of this.group.members)
                consumers[member.name] = 0;

        return {
            valid: false,
            price: null,
            consumers: consumers,

            required: value => !!value || this.$t("validation.required"),
            positive: value => value >= 0.01 || this.$t("validation.positive"),
        }
    },
    computed: {
        enoughConsumers() {
            return Object.values(this.consumers).some(amount => amount > 0);
        },
        totalPrice() {
            return this.price * Object.values(this.consumers).reduce((a, b) => a + b);
        }
    },
    props: {
        group: Object,
        payer: String
    },
    methods: {
        submit() {
            const data = {
                payer: this.payer,
                price: parseInt(this.price * 100),
                consumers: this.consumers
            };

            localStorage.setItem("price", data.price);

            this.$emit("pay", data);
        }
    },
    created() {
        const price = localStorage.getItem("price");
        if (price)
            this.price = (parseFloat(price) / 100).toFixed(2);
    },
    beforeCreate() {
        scrollTo(0, 0);
    }
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
