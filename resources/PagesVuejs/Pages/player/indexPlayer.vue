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
const queue = ref([]);
const isPlaying = ref(false);

const playlistSongs = computed(() => currentPlaylist.value?.songs ?? []);

const videoEmbedUrl = computed(() => {
  const url = currentSong.value?.video_url;
  if (!url) return '';
  if (url.includes('youtube.com') || url.includes('youtu.be')) {
    const base = typeof window === 'undefined' ? 'http://localhost' : window.location.origin;
    const parsed = new URL(url, base);
    const id = parsed.searchParams.get('v') || parsed.pathname.split('/').pop();
    return `https://www.youtube.com/embed/${id}`;
  }
  return '';
});

const playSong = (song) => {
  currentSong.value = song;
  isPlaying.value = true;
};

const enqueueSong = (song) => {
  if (!song) return;
  queue.value.push(song);
};

const playNext = () => {
  if (queue.value.length) {
    currentSong.value = queue.value.shift();
    isPlaying.value = true;
    return;
  }
  const songs = playlistSongs.value;
  if (!songs.length || !currentSong.value) return;
  const currentIndex = songs.findIndex((song) => song.id === currentSong.value.id);
  const nextIndex = currentIndex === -1 ? 0 : (currentIndex + 1) % songs.length;
  currentSong.value = songs[nextIndex];
  isPlaying.value = true;
};

const playPrev = () => {
  const songs = playlistSongs.value;
  if (!songs.length || !currentSong.value) return;
  const currentIndex = songs.findIndex((song) => song.id === currentSong.value.id);
  const prevIndex = currentIndex <= 0 ? songs.length - 1 : currentIndex - 1;
  currentSong.value = songs[prevIndex];
  isPlaying.value = true;
};

const togglePlay = () => {
  if (!currentSong.value && playlistSongs.value.length) {
    currentSong.value = playlistSongs.value[0];
  }
  isPlaying.value = !isPlaying.value;
};
</script>

<template>
  <AppHead title="Player" />
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

          <div class="queue-card mt-4">
            <h6>Fila de reprodução</h6>
            <div v-if="queue.length" class="list-group">
              <div v-for="song in queue" :key="song.id" class="list-group-item">
                {{ song.title }}
              </div>
            </div>
            <p v-else class="text-muted">Fila vazia.</p>
          </div>
        </aside>

        <section class="player-main">
          <div class="player-now">
            <h2>{{ currentSong?.title || 'Selecione uma música' }}</h2>
            <p class="text-muted">{{ currentSong?.artist }}</p>
            <div class="player-controls">
              <button class="btn btn-outline-secondary" @click="playPrev">⏮</button>
              <button class="btn btn-primary" @click="togglePlay">
                {{ isPlaying ? '⏸️' : '▶️' }}
              </button>
              <button class="btn btn-outline-secondary" @click="playNext">⏭</button>
            </div>
            <div v-if="currentSong?.video_url" class="mt-3">
              <a class="btn btn-link" :href="currentSong.video_url" target="_blank" rel="noreferrer">
                Abrir vídeo
              </a>
            </div>
            <div v-if="currentSong?.audio_url" class="mt-3">
              <audio :src="currentSong.audio_url" controls></audio>
            </div>
            <div v-if="videoEmbedUrl" class="video-embed mt-3">
              <iframe
                :src="videoEmbedUrl"
                title="Vídeo"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
              ></iframe>
            </div>
          </div>

          <div class="player-list mt-4">
            <h5>Músicas da playlist</h5>
            <div v-if="playlistSongs.length" class="list-group">
              <div v-for="song in playlistSongs" :key="song.id" class="list-group-item">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <strong>{{ song.title }}</strong>
                    <span class="text-muted"> — {{ song.artist }}</span>
                  </div>
                  <div class="song-actions">
                    <button class="btn btn-sm btn-outline-primary" @click="playSong(song)">
                      Tocar agora
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" @click="enqueueSong(song)">
                      Adicionar à fila
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <p v-else class="text-muted">Selecione uma playlist com músicas.</p>
          </div>
        </section>
      </div>

      <div class="mini-player" v-if="currentSong">
        <div>
          <strong>{{ currentSong.title }}</strong>
          <span class="text-muted"> — {{ currentSong.artist }}</span>
        </div>
        <div class="mini-actions">
          <button class="btn btn-sm btn-outline-secondary" @click="playPrev">⏮</button>
          <button class="btn btn-sm btn-primary" @click="togglePlay">
            {{ isPlaying ? '⏸️' : '▶️' }}
          </button>
          <button class="btn btn-sm btn-outline-secondary" @click="playNext">⏭</button>
        </div>
      </div>
    </LayoutIndexPlayer>
  </div>
</template>

<style lang="css" scoped>
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

.queue-card {
  background: #fff;
  border-radius: 12px;
  padding: 16px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
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

.song-actions {
  display: flex;
  gap: 8px;
}

.video-embed iframe {
  width: 100%;
  min-height: 280px;
  border-radius: 12px;
}

.mini-player {
  position: sticky;
  bottom: 0;
  margin-top: 24px;
  background: #1f2937;
  color: #fff;
  border-radius: 12px;
  padding: 12px 16px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
}

.mini-player .text-muted {
  color: #cbd5f5 !important;
}

.mini-actions {
  display: flex;
  gap: 8px;
}

@media (max-width: 992px) {
  .player-layout {
    grid-template-columns: 1fr;
  }
}
</style>
