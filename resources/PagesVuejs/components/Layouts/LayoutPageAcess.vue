<template>
<CapaAbertura type="time"/>
<Loading />
<NavBarPageAcess :title="props.title" :band="props.band" />
    <div class="layoutPage">
        <slot />
    </div>
</template>

<style lang="css" scoped>
.layoutPage{
    margin-top: 60px;
}
</style>
<script setup>
const props = defineProps({
  title: {
    type: String,
    required: true
  },
  band: {
    type: Object,
    required: true
  }
});
</script>
