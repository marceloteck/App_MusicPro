<template>
  <AppHead :title="event.title"/>
  <div class="event-show">
    <LayoutIndexPages>
      <div class="container">
        <div v-if="error" class="alert alert-danger">
          {{ error }}
        </div>
        
        <div v-else>
          <div class="event-header">
            <h1>{{ event.title }}</h1>
            <p class="event-date">Data: {{ formatDate(event.date) }}</p>
            <p v-if="event.description" class="event-description">{{ event.description }}</p>
          </div>

          <div class="event-songs">
            <h2>Músicas do Evento</h2>
            <div v-if="event.songs && event.songs.length > 0" class="songs-list">
              <div v-for="song in event.songs" :key="song.id" class="song-item">
                <h3>{{ song.title }}</h3>
                <p>Artista: {{ song.artist }}</p>
                <div class="song-actions">
                  <button @click="viewSong(song.id)" class="btn-view">Ver Detalhes</button>
                </div>
              </div>
            </div>
            <div v-else class="no-songs">
              <p>Nenhuma música cadastrada para este evento.</p>
            </div>
          </div>
        </div>
      </div>
    </LayoutIndexPages>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { usePage, router } from '@inertiajs/vue3';

const props = defineProps({
  event: {
    type: Object,
    required: true,
  },
});

const error = ref(null);

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('pt-BR');
};

const viewSong = (songId) => {
  router.visit(`/songs/${songId}`);
};
</script>

<style scoped>
.event-show {
  padding: 2rem;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
}

.event-header {
  margin-bottom: 2rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #ddd;
}

.event-date {
  color: #666;
  font-size: 1.1rem;
  margin: 0.5rem 0;
}

.event-description {
  margin-top: 1rem;
  line-height: 1.6;
}

.event-songs {
  margin-top: 2rem;
}

.songs-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1rem;
  margin-top: 1rem;
}

.song-item {
  padding: 1rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  background-color: #fff;
  transition: all 0.3s ease;
}

.song-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.song-item h3 {
  margin: 0;
  color: #333;
}

.song-item p {
  margin: 0.5rem 0;
  color: #666;
}

.song-actions {
  margin-top: 1rem;
  display: flex;
  gap: 0.5rem;
}

.btn-view {
  padding: 0.5rem 1rem;
  background-color: #28a745;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.btn-view:hover {
  background-color: #218838;
}

.no-songs {
  text-align: center;
  padding: 2rem;
  background-color: #f8f9fa;
  border-radius: 4px;
  color: #666;
}

.alert {
  padding: 1rem;
  margin-bottom: 1rem;
  border-radius: 4px;
}

.alert-danger {
  background-color: #f8d7da;
  border: 1px solid #f5c6cb;
  color: #721c24;
}
</style> 