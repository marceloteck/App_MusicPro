<template>
  <div class="CreateBandModalClass">
    <LayoutModalMobile
      ref="RefCreateBandModal"
      :height="modalHeight"
    >
      <div class="modal-header">
        <h2>Criar Nova Banda</h2>
      </div>

      <form @submit.prevent="submitForm" class="band-form">
        <div v-if="error" class="error-message">
          {{ error }}
        </div>
        <div class="form-group">
          <label for="name">Nome da Banda</label>
          <input 
            type="text" 
            id="name" 
            v-model="form.name" 
            required
            placeholder="Digite o nome da sua banda"
          >
        </div>

        <div class="form-group">
          <label for="description">Descrição</label>
          <textarea 
            id="description" 
            v-model="form.description" 
            rows="3"
            placeholder="Descreva sua banda"
          ></textarea>
        </div>

        <div class="form-group">
          <label for="genre">Gênero Musical</label>
          <input 
            type="text" 
            id="genre" 
            v-model="form.genre" 
            required
            placeholder="Ex: Rock, Pop, Jazz..."
          >
        </div>

        <div class="form-group">
          <label for="image">Imagem da Banda</label>
          <input 
            type="file" 
            id="image" 
            @change="handleImageUpload" 
            accept="image/*"
          >
          <div v-if="imagePreview" class="image-preview">
            <img :src="imagePreview" alt="Preview da imagem">
          </div>
        </div>

        <div class="form-actions">
          <button type="button" class="btn-cancel" @click="closeModal">Cancelar</button>
          <button type="submit" class="btn-submit" :disabled="loading">
            {{ loading ? 'Criando...' : 'Criar Banda' }}
          </button>
        </div>
      </form>
    </LayoutModalMobile>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const RefCreateBandModal = ref(null);
const loading = ref(false);
const imagePreview = ref(null);
const modalHeight = ref('80vh');
const error = ref(null);

const form = ref({
  name: '',
  description: '',
  genre: '',
  image: null
});

const ExecuteCreateBandModal = () => {
  if (RefCreateBandModal.value) {
    RefCreateBandModal.value.openModal();
  }
};

defineExpose({ ExecuteCreateBandModal });

const closeModal = () => {
  if (RefCreateBandModal.value) {
    RefCreateBandModal.value.closeModal();
  }
  resetForm();
};

const resetForm = () => {
  form.value = {
    name: '',
    description: '',
    genre: '',
    image: null
  };
  imagePreview.value = null;
  error.value = null;
};

const handleImageUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    form.value.image = file;
    const reader = new FileReader();
    reader.onload = (e) => {
      imagePreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const submitForm = async () => {
  loading.value = true;
  error.value = null;
  
  try {
    const formData = new FormData();
    formData.append('name', form.value.name);
    formData.append('description', form.value.description);
    formData.append('genre', form.value.genre);
    if (form.value.image) {
      formData.append('image', form.value.image);
    }

    await router.post('/bands', formData, {
      onSuccess: () => {
        resetForm();
        if (RefCreateBandModal.value) {
          RefCreateBandModal.value.closeModal();
        }
        router.reload();
      },
      onError: (errors) => {
        error.value = Object.values(errors).flat().join('\n');
        console.error('Erro ao criar banda:', errors);
      }
    });
  } catch (e) {
    error.value = 'Erro ao criar banda. Por favor, tente novamente.';
    console.error('Erro:', e);
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.CreateBandModalClass {
  width: 100%;
}

.modal-header {
  padding: 1rem;
  border-bottom: 1px solid #eee;
}

.modal-header h2 {
  margin: 0;
  font-size: 1.5rem;
  color: #333;
}

.band-form {
  padding: 1rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  color: #666;
  font-weight: 500;
}

.form-group input[type="text"],
.form-group textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 1rem;
}

.form-group textarea {
  resize: vertical;
  min-height: 100px;
}

.form-group input[type="file"] {
  width: 100%;
  padding: 0.5rem;
  border: 2px dashed #ddd;
  border-radius: 8px;
  background: #f9f9f9;
}

.image-preview {
  margin-top: 1rem;
  text-align: center;
}

.image-preview img {
  max-width: 200px;
  max-height: 200px;
  border-radius: 8px;
  object-fit: cover;
}

.form-actions {
  display: flex;
  gap: 1rem;
  margin-top: 2rem;
}

.btn-cancel,
.btn-submit {
  flex: 1;
  padding: 0.75rem;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-cancel {
  background: #f0f0f0;
  color: #666;
}

.btn-submit {
  background: #007bff;
  color: white;
}

.btn-submit:disabled {
  background: #ccc;
  cursor: not-allowed;
}

.btn-cancel:hover {
  background: #e0e0e0;
}

.btn-submit:hover:not(:disabled) {
  background: #0056b3;
}

.error-message {
  background-color: #fee2e2;
  color: #dc2626;
  padding: 0.75rem;
  border-radius: 8px;
  margin-bottom: 1rem;
  font-size: 0.875rem;
  white-space: pre-line;
}
</style> 