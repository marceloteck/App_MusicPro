<script setup>
import { computed, ref } from 'vue';
import { user, avatarUrl } from "@resources/plugins/DadosUser.js";

const users = ref({
  name: user.value.name || '',
  email: user.value.email || '',
  photo: avatarUrl || ''
});

const Title = "Configurações";
const isAdmin = computed(() => user.value?.roles?.includes('admin'));

const isDownloading = ref(false);
const downloadProgress = ref(0);
const downloadMessage = ref('');

const baixarTudoOffline = async () => {
  if (isDownloading.value) return;
  isDownloading.value = true;
  downloadProgress.value = 0;
  downloadMessage.value = 'Preparando lista de músicas...';

  try {
    const response = await fetch('/offline/songs');
    if (!response.ok) {
      downloadMessage.value = 'Falha ao buscar músicas para offline.';
      return;
    }

    const data = await response.json();
    const songs = data?.songs ?? [];
    const total = songs.length || 1;

    for (let index = 0; index < songs.length; index += 1) {
      const song = songs[index];
      downloadMessage.value = `Baixando ${index + 1} de ${songs.length}...`;
      await fetch(`/offline/songs/${song.id}`);
      downloadProgress.value = Math.round(((index + 1) / total) * 100);
    }

    downloadMessage.value = 'Músicas prontas para uso offline!';
  } catch (error) {
    console.error(error);
    downloadMessage.value = 'Erro ao baixar músicas para offline.';
  } finally {
    isDownloading.value = false;
  }
};
</script>
<template>
<AppHead :title="Title"/>
    <div class="profilePage">
        <LayoutSubPages :title="Title">
           <div class="container-fluid">
                <h2>{{Title}}</h2>

                <div class="card mt-3">
                  <div class="card-body">
                    <h5 class="card-title">Offline</h5>
                    <p class="text-muted">Baixe todas as músicas para usar o app sem internet.</p>
                    <button
                      class="btn btn-primary"
                      type="button"
                      :disabled="isDownloading"
                      @click="baixarTudoOffline"
                    >
                      {{ isDownloading ? 'Baixando...' : 'Baixar tudo para offline' }}
                    </button>
                    <div v-if="downloadMessage" class="mt-3">
                      <small class="text-muted">{{ downloadMessage }}</small>
                      <div class="progress mt-2">
                        <div
                          class="progress-bar"
                          role="progressbar"
                          :style="{ width: `${downloadProgress}%` }"
                          :aria-valuenow="downloadProgress"
                          aria-valuemin="0"
                          aria-valuemax="100"
                        >
                          {{ downloadProgress }}%
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div v-if="isAdmin" class="list-group mt-4">
                  <a :href="route('admin.imports.index')" class="list-group-item list-group-item-action">
                    Importar músicas (Admin)
                  </a>
                </div>
           </div>
        </LayoutSubPages>

    </div>
</template>
<style lang="css" scope>

</style>
