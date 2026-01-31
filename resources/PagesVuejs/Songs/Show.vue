<script setup>
import { computed } from 'vue';

const props = defineProps({
  song: {
    type: Object,
    required: true,
  },
  data: {
    type: Object,
    default: null,
  },
  versions: {
    type: Array,
    default: () => [],
  },
});

const musica = computed(() => {
  if (props.data) {
    return {
      ...props.song,
      ...props.data,
      lyrics: props.data.lyrics ?? props.song?.lyrics,
    };
  }

  return props.song;
});
</script>

<template>
  <AppHead :title="musica?.title || 'Música'" />
  <LayoutIndexPages>
    <div class="container-fluid">
      <Musica :musica="musica" :versions="versions" />
    </div>
  </LayoutIndexPages>
</template>
