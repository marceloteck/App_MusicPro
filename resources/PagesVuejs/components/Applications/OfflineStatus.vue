<script setup>
import { onMounted, onUnmounted, ref } from 'vue';

const isOnline = ref(typeof navigator !== 'undefined' ? navigator.onLine : true);
const syncing = ref(false);
const lastSync = ref(null);

const updateStatus = () => {
  isOnline.value = navigator.onLine;
};

const syncOffline = async () => {
  if (!isOnline.value || syncing.value) return;
  syncing.value = true;
  try {
    const response = await fetch('/offline/songs');
    if (!response.ok) {
      throw new Error('Falha ao buscar músicas.');
    }
    const data = await response.json();
    const songs = data?.songs ?? [];
    for (const song of songs) {
      await fetch(`/offline/songs/${song.id}`);
    }
    lastSync.value = new Date();
  } catch (error) {
    console.warn('Falha ao sincronizar músicas.', error);
  } finally {
    syncing.value = false;
  }
};

onMounted(() => {
  window.addEventListener('online', updateStatus);
  window.addEventListener('offline', updateStatus);
});

onUnmounted(() => {
  window.removeEventListener('online', updateStatus);
  window.removeEventListener('offline', updateStatus);
});
</script>

<template>
  <div class="offline-status" :class="{ online: isOnline, offline: !isOnline }">
    <span class="indicator"></span>
    <span>
      {{ isOnline ? 'Online' : 'Offline' }}
      <small v-if="lastSync" class="ms-2">Última sincronização: {{ lastSync.toLocaleString('pt-BR') }}</small>
    </span>
    <button class="btn btn-sm btn-light" @click="syncOffline" :disabled="!isOnline || syncing">
      {{ syncing ? 'Sincronizando...' : 'Sincronizar' }}
    </button>
  </div>
</template>

<style scoped>
.offline-status {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 8px 12px;
  border-radius: 12px;
  background: #f1f3f5;
  font-size: 13px;
}

.offline-status.online {
  border: 1px solid #b6e3b6;
}

.offline-status.offline {
  border: 1px solid #f3b2b2;
  background: #fff5f5;
}

.indicator {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: #dc3545;
}

.offline-status.online .indicator {
  background: #198754;
}

button {
  margin-left: auto;
}
</style>
