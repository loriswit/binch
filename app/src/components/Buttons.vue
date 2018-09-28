<template>
  <div id="buttons">
    <v-btn @click="$emit('cancel')" flat>Cancel</v-btn>
    <v-btn :disabled="!valid" depressed type="submit" color="primary">{{ text }}</v-btn>
  </div>
</template>

<script>
export default {
    name: "Buttons",
    props: {
        text: String,
        valid: Boolean
    }
}
</script>

<style scoped>
.v-form #buttons {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 50px;
  background: #eee;
}

.v-form .v-btn {
  width: 50%;
  height: 100%;
  margin: 0;
  border-radius: 0;
}
</style>
