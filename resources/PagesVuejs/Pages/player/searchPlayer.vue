<script setup>
import { ref } from 'vue';

const query = ref('');
const results = ref([]);
const loading = ref(false);

const buscar = async () => {
  loading.value = true;
  try {
    const response = await fetch(`/offline/songs?q=${encodeURIComponent(query.value)}`);
    const data = await response.json();
    results.value = data?.songs ?? [];
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <AppHead title="Minha Biblioteca" />
  <div class="searchPlayer">
    <LayoutIndexPlayer>
      <div class="container">
        <h1>Pesquisar no player</h1>
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

        <div class="list-group mt-4" v-if="results.length">
          <div v-for="song in results" :key="song.id" class="list-group-item">
            <strong>{{ song.title }}</strong> — {{ song.artist }}
            <a class="btn btn-sm btn-outline-secondary float-end" :href="`/songs/${song.id}`">
              Abrir
            </a>
          </div>
        </div>
        <p v-else class="text-muted mt-4">Faça uma busca para ver resultados.</p>
      </div>
    </LayoutIndexPlayer>
  </div>
</template>
