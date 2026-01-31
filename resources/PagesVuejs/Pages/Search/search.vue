<script setup>
import { ref } from 'vue';

const query = ref('');
const results = ref([]);
const loading = ref(false);
const error = ref('');

const buscar = async () => {
  error.value = '';
  loading.value = true;
  try {
    const response = await fetch(`/offline/songs?q=${encodeURIComponent(query.value)}`);
    if (!response.ok) {
      throw new Error('Falha ao buscar músicas.');
    }
    const data = await response.json();
    results.value = data?.songs ?? [];
  } catch (err) {
    error.value = err.message ?? 'Erro ao buscar músicas.';
  } finally {
    loading.value = false;
  }
};
</script>
<template>
  <AppHead title="Pesquisar"/>
    <div class="indexSearch">
        <LayoutIndexPages>
          <div class="container">
            <h1>Pesquisar músicas</h1>
            <div class="input-group mt-3">
              <input
                v-model="query"
                class="form-control"
                placeholder="Digite o título ou trecho da letra"
                @keyup.enter="buscar"
              />
              <button class="btn btn-primary" :disabled="loading" @click="buscar">
                {{ loading ? 'Buscando...' : 'Buscar' }}
              </button>
            </div>

            <p v-if="error" class="text-danger mt-3">{{ error }}</p>

            <div v-if="results.length" class="list-group mt-4">
              <div v-for="song in results" :key="song.id" class="list-group-item">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h5 class="mb-1">{{ song.title }}</h5>
                    <small class="text-muted">{{ song.artist }}</small>
                    <p v-if="song.lyrics" class="mb-0 mt-2 text-muted">
                      {{ song.lyrics.slice(0, 120) }}...
                    </p>
                  </div>
                  <a class="btn btn-outline-secondary" :href="`/songs/${song.id}`">
                    Ver detalhes
                  </a>
                </div>
              </div>
            </div>
            <p v-else class="text-muted mt-4">Faça uma busca para ver resultados.</p>
          </div>
                    
        </LayoutIndexPages>
    </div>
</template>
<style lang="css" scope>

</style>
