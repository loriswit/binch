<template>
  <div class="progress">
    <v-progress-circular color="primary" indeterminate/>
  </div>
</template>

<script>
export default {
    name: "ScrollProgress"
}
</script>

<style scoped>
.progress {
  display: block;
  margin: 20px;
  text-align: center;
}
</style>
