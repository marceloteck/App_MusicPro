<script setup>
import { computed, onMounted, ref } from 'vue';

const highlights = [
  {
    title: 'Pesquisar músicas',
    description: 'Busque por título ou trechos da letra.',
    href: '/search',
  },
  {
    title: 'Playlists',
    description: 'Monte repertórios por evento ou banda.',
    href: '/player',
  },
  {
    title: 'Offline',
    description: 'Baixe tudo e use o app sem internet.',
    href: '/settings',
  },
];

const latestSong = ref(null);
const lastUpdated = ref('');
const offlineReady = ref(false);

const loadLatest = async () => {
  try {
    const response = await fetch('/offline/songs');
    if (!response.ok) {
      offlineReady.value = false;
      return;
    }
    const data = await response.json();
    const songs = data?.songs ?? [];
    offlineReady.value = songs.length > 0;
    const latest = songs[0] || null;
    latestSong.value = latest;
    if (latest?.imported_at) {
      lastUpdated.value = new Date(latest.imported_at).toLocaleDateString('pt-BR');
    }
  } catch (error) {
    offlineReady.value = false;
  }
};

onMounted(loadLatest);

const offlineStatus = computed(() => (offlineReady.value ? 'Offline pronto' : 'Offline pendente'));
</script>

<template>
  <AppHead title="Inicio" />
  <div class="indexpage">
    <LayoutIndexPages>
      <div class="container">
        <div class="hero card shadow-sm">
          <div class="card-body">
            <h1 class="card-title">Bem-vindo ao Tocare</h1>
            <p class="card-text text-muted">
              Organize cifras, letras e vídeos em um só lugar. Prepare repertórios e mantenha tudo offline.
            </p>
            <div class="hero-status">
              <span class="badge" :class="offlineReady ? 'bg-success' : 'bg-warning text-dark'">
                {{ offlineStatus }}
              </span>
              <small v-if="lastUpdated" class="text-muted">Últimas cifras atualizadas em {{ lastUpdated }}</small>
            </div>
            <a href="/search" class="btn btn-primary">Começar pesquisa</a>
            <a
              v-if="latestSong"
              :href="`/songs/${latestSong.id}`"
              class="btn btn-outline-secondary ms-2"
            >
              Abrir minha última cifra
            </a>
          </div>
        </div>

        <div class="row mt-4 g-3">
          <div v-for="item in highlights" :key="item.title" class="col-md-4">
            <div class="card h-100">
              <div class="card-body">
                <h5 class="card-title">{{ item.title }}</h5>
                <p class="card-text text-muted">{{ item.description }}</p>
                <a :href="item.href" class="btn btn-outline-secondary">Abrir</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </LayoutIndexPages>
  </div>
</template>

<style lang="css" scope>
.hero {
  border-radius: 16px;
}

.hero-status {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-bottom: 12px;
}
</style>
