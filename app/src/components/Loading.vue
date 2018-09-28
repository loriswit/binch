<template>
  <div id="loading">
    <v-progress-circular size="100" color="primary" indeterminate></v-progress-circular>
  </div>
</template>

<script>
export default {
    name: "Loading"
}
</script>

<style scoped>
#loading {
  background-color: rgba(255, 255, 255, 0.75);
  z-index: 1;
  display: flex;
  position: fixed;
  top: 0;
  left: 0;
  justify-content: center;
  align-items: center;
  width: 100%;
  height: 100%;
}
</style>
