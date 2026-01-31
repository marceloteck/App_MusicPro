<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const erro = ref(null);

const song = useForm({
  title: '',
  artist: '',
  singer: '',
  composer: '',
  description: '',
  video_url: '',
  audio_url: '',
  chords_text: '',
  chords_file: null,
});

const handleFileUpload = (event) => {
  const [file] = event.target.files;
  song.chords_file = file || null;
};

const submitForm = () => {
  erro.value = null;
  song.post(route('songs.store'), {
    preserveScroll: true,
    forceFormData: true,
    onSuccess: () => {
      song.reset('chords_text', 'chords_file');
    },
    onError: () => {
      erro.value = 'Erro ao processar a solicitação.';
    },
  });
};
</script>

<template>
  <form class="form-control" @submit.prevent="submitForm">
    <h4 class="mb-3">Nova música</h4>

    <input class="form-control" v-model="song.title" placeholder="Título" required />

    <input class="form-control" v-model="song.artist" placeholder="Artista (opcional)" />

    <input class="form-control" v-model="song.singer" placeholder="Cantor (opcional)" />

    <input class="form-control" v-model="song.composer" placeholder="Compositor (opcional)" />

    <input class="form-control" v-model="song.video_url" placeholder="URL do vídeo (opcional)" />

    <input class="form-control" v-model="song.audio_url" placeholder="URL do áudio (opcional)" />

    <textarea
      class="form-control"
      v-model="song.description"
      placeholder="Descrição extra (opcional)"
      rows="3"
    ></textarea>

    <label class="form-label mt-3">Enviar cifra</label>
    <textarea
      class="form-control"
      v-model="song.chords_text"
      placeholder="Cole a cifra aqui"
      rows="8"
    ></textarea>

    <div class="text-muted small mt-2">
      Ou envie um arquivo .txt
    </div>
    <input class="form-control" type="file" @change="handleFileUpload" accept=".txt" />

    <button class="btn btn-success mt-3" type="submit" :disabled="song.processing">
      Salvar Música
    </button>
    <p v-if="erro" class="text-danger mt-2">{{ erro }}</p>
  </form>
</template>

<style lang="css" scoped>
input,
textarea {
  margin-bottom: 15px;
}
</style>
