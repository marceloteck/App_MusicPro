<script setup>
import { ref } from 'vue';

const query = ref('');
const results = ref([]);
const loading = ref(false);
const error = ref('');

const externalQuery = ref('');
const externalResults = ref([]);
const externalLoading = ref(false);
const externalError = ref('');
const importMessage = ref('');

const activeTab = ref('local');

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

const buscarExterno = async () => {
  externalError.value = '';
  externalLoading.value = true;
  try {
    const response = await fetch(`/search/external/results?q=${encodeURIComponent(externalQuery.value)}`);
    if (!response.ok) {
      throw new Error('Falha ao buscar em sites externos.');
    }
    const data = await response.json();
    externalResults.value = data?.results ?? [];
  } catch (err) {
    externalError.value = err.message ?? 'Erro ao buscar fontes externas.';
  } finally {
    externalLoading.value = false;
  }
};

const importarMusica = async (url) => {
  importMessage.value = '';
  try {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    const response = await fetch('/search/external/import', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token || '',
      },
      body: JSON.stringify({ url }),
    });
    const data = await response.json();
    if (!response.ok) {
      throw new Error(data?.message || 'Falha ao importar.');
    }
    importMessage.value = data?.message || 'Música importada com sucesso.';
  } catch (err) {
    importMessage.value = err.message || 'Erro ao importar música.';
  }
};
</script>
<template>
  <AppHead title="Pesquisar" />
  <div class="indexSearch">
    <LayoutIndexPages>
      <div class="container">
        <h1>Pesquisar músicas</h1>

        <div class="tabs">
          <button :class="['tab', { active: activeTab === 'local' }]" @click="activeTab = 'local'">
            No app
          </button>
          <button :class="['tab', { active: activeTab === 'external' }]" @click="activeTab = 'external'">
            Internet
          </button>
        </div>

        <div v-if="activeTab === 'local'">
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

        <div v-else>
          <div class="input-group mt-3">
            <input
              v-model="externalQuery"
              class="form-control"
              placeholder="Buscar em sites externos"
              @keyup.enter="buscarExterno"
            />
            <button class="btn btn-primary" :disabled="externalLoading" @click="buscarExterno">
              {{ externalLoading ? 'Buscando...' : 'Buscar' }}
            </button>
          </div>

          <p v-if="externalError" class="text-danger mt-3">{{ externalError }}</p>
          <p v-if="importMessage" class="text-success mt-3">{{ importMessage }}</p>

          <div v-if="externalResults.length" class="list-group mt-4">
            <div v-for="result in externalResults" :key="result.url" class="list-group-item">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h5 class="mb-1">{{ result.title || 'Título não identificado' }}</h5>
                  <small class="text-muted">
                    {{ result.artist || 'Artista não identificado' }} · {{ result.source }}
                  </small>
                </div>
                <button class="btn btn-outline-primary" @click="importarMusica(result.url)">
                  Importar
                </button>
              </div>
            </div>
          </div>
          <p v-else class="text-muted mt-4">Busque para ver fontes externas disponíveis.</p>
        </div>
      </div>
    </LayoutIndexPages>
  </div>
</template>
<style lang="css" scoped>
.tabs {
  display: flex;
  gap: 8px;
  margin-top: 16px;
}

.tab {
  border: none;
  background: #f1f3f5;
  padding: 8px 16px;
  border-radius: 999px;
  font-weight: 600;
  color: #6c757d;
}

.tab.active {
  background: #0d6efd;
  color: #fff;
}
</style>
