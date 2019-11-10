<template>
  <div id="home">
    <h1>{{ $t("home.title") }}</h1>
    <div id="buttons">
      <label>{{ $t("home.goto.label") }}</label>
      <v-btn to="join" color="primary" depressed>{{ $t("home.goto.button") }}</v-btn>
      <label>{{ $t("home.new.label") }}</label>
      <v-btn to="new" color="primary" depressed>{{ $t("home.new.button") }}</v-btn>
    </div>
  </div>
</template>

<script>
export default {
    name: "Home"
}
</script>

<style scoped>
#home {
  text-align: center;
}

h1 {
  margin-top: 20px;
  font-weight: normal;
}

#buttons {
  display: flex;
  flex-direction: column;
  align-items: center;
}

#buttons label {
  font-size: 1.25em;
  margin-top: 30px;
}

#buttons .v-btn {
  font-size: 1.75em;
  width: 290px;
  height: 70px;
  border-radius: 0;
  margin: 10px;
}
</style>
