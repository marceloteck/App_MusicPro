
<style lang="css" scope>
input{
  margin-bottom: 15px;
}
  
</style>


<template>
  <form class="form-control" @submit.prevent="submitForm">
    <input class="form-control" v-model="song.title" placeholder="Título" required />


    <input class="form-control" v-model="song.artist" placeholder="Artista"  required/>

    <input class="form-control" v-model="song.video_url" placeholder="URL do vídeo" />

    <!-- Upload de arquivo TXT -->
    <input class="form-control" type="file" @change="handleFileUpload" accept=".txt" />

    <!-- Inserção via Textarea -->
    <textarea  class="form-control" v-model="song.chords_text" placeholder="Cole a cifra aqui"></textarea>


    <button class="btn btn-success" type="submit">Salvar Música</button>
    {{erro}}
  </form>
</template>

<script setup>
import { ref, watch } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';

    const song = useForm({ 
      title: "", 
      artist: "", 
      video_url: "", 
      chords_text: "", 
      chords_file: null 
      });

const resultado = ref([]); // Armazena os links dos documentos gerados
const erro = ref(null); // Armazena mensagens de erro      

const submitForm = async () => {
    try {
        erro.value = null;
        resultado.value = [];

        // Enviando os dados para o backend via Inertia.js
        song.post(route('songs.store'), {
            preserveScroll: true,
            onSuccess: (response) => {
                if (song.success) {  
                    //Inertia.visit(route('salvar.documentos'));
                    alert("Enviado com sucesso")
                } else {
                    erro.value = "Erro ao gerar documentos.";
                }
            },
            onError: () => {
                erro.value = "Erro ao processar a solicitação.";
            }
        });
    } catch (e) {
        erro.value = "Ocorreu um erro inesperado.";
    }
};      

/*
export default {
  setup() {


    const handleFileUpload = (event) => {
      song.value.chords_file = event.target.files[0];
    };

    const submitForm = async () => {
      const formData = new FormData();
      formData.append("title", song.value.title);
      formData.append("artist", song.value.artist);
      formData.append("video_url", song.value.video_url);
      if (song.value.chords_file) {
        formData.append("chords_file", song.value.chords_file);
      } else {
        formData.append("chords_text", song.value.chords_text);
      }

      await fetch("/songs", {
        method: "POST",
        body: formData,
      });
    };

    return { song, handleFileUpload, submitForm };
  },
};*/
</script>
