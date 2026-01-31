<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
  playlists: {
    type: Array,
    default: () => [],
  },
  songs: {
    type: Array,
    default: () => [],
  },
});

const currentSong = ref(props.songs[0] || null);
const currentPlaylist = ref(props.playlists[0] || null);

const playlistSongs = computed(() => currentPlaylist.value?.songs ?? []);

const playSong = (song) => {
  currentSong.value = song;
};
</script>

<template>
  <AppHead title="Inicio" />
  <div class="indexpage">
    <LayoutIndexPlayer>
      <div class="player-layout">
        <aside class="player-sidebar">
          <h4>Playlists</h4>
          <ul class="list-group">
            <li
              v-for="playlist in playlists"
              :key="playlist.id"
              class="list-group-item"
              :class="{ active: playlist.id === currentPlaylist?.id }"
              @click="currentPlaylist = playlist"
            >
              {{ playlist.name }}
            </li>
            <li v-if="!playlists.length" class="list-group-item text-muted">
              Nenhuma playlist disponível.
            </li>
          </ul>
        </aside>

        <section class="player-main">
          <div class="player-now">
            <h2>{{ currentSong?.title || 'Selecione uma música' }}</h2>
            <p class="text-muted">{{ currentSong?.artist }}</p>
            <div class="player-controls">
              <button class="btn btn-outline-secondary">⏮</button>
              <button class="btn btn-primary">▶️</button>
              <button class="btn btn-outline-secondary">⏭</button>
            </div>
            <div v-if="currentSong?.video_url" class="mt-3">
              <a class="btn btn-link" :href="currentSong.video_url" target="_blank" rel="noreferrer">
                Abrir vídeo
              </a>
            </div>
          </div>

          <div class="player-list mt-4">
            <h5>Músicas da playlist</h5>
            <div v-if="playlistSongs.length" class="list-group">
              <button
                v-for="song in playlistSongs"
                :key="song.id"
                class="list-group-item list-group-item-action"
                @click="playSong(song)"
              >
                {{ song.title }} — {{ song.artist }}
              </button>
            </div>
            <p v-else class="text-muted">Selecione uma playlist com músicas.</p>
          </div>
        </section>
      </div>
    </LayoutIndexPlayer>
  </div>
</template>

<style lang="css" scope>
.player-layout {
  display: grid;
  grid-template-columns: 280px 1fr;
  gap: 24px;
}

.player-sidebar {
  background: #f8f9fa;
  border-radius: 12px;
  padding: 16px;
}

.player-main {
  background: #fff;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.player-now {
  border-bottom: 1px solid #eaeaea;
  padding-bottom: 16px;
}

.player-controls {
  display: flex;
  gap: 12px;
  margin-top: 12px;
}
</style>
