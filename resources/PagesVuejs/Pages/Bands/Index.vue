<script setup>
import { ref, onMounted, nextTick, computed } from 'vue'
import { useForm } from '@inertiajs/vue3';
import { useAuthStore } from '@resources/plugins/stores/auth.js'
import { user, avatarUrl } from "@resources/plugins/DadosUser.js";
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
  bands: {
    type: Array,
    default: () => []
  },
  userLists: {
    type: Array,
    default: () => []
  }
});

const page = usePage();
const flashMessage = computed(() => {
  return page.props.flash?.error || null;
});

const activeTab = ref('bands')
const authStore = useAuthStore()
const createBandModalRef = ref(null);
const manageBandModalRef = ref(null);
const selectedBand = ref(null);
const showListForm = ref(false);
const editingList = ref(null);

const listForm = useForm({
  name: '',
  description: '',
  is_public: false,
});

onMounted(() => {
  // Inicializa o store com os dados do usuário
  if (user.value) {
    authStore.setUser(user.value)
  }
})

const userRole = (band) => {
  return authStore.checkUserRole(band)
}

const canManageList = (list) => {
  return list?.user_id === user.value?.id;
};

const shareBand = (band) => {
  // Implementar lógica de compartilhamento
  const link = `${window.location.origin}/bands/${band.id}`;
  navigator.clipboard.writeText(link).catch(() => {
    window.prompt('Copie o link da banda:', link);
  });
}

const deleteList = (listId) => {
  if (!confirm('Deseja excluir esta lista?')) return;
  listForm.delete(route('playlists.destroy', listId), {
    preserveScroll: true,
  });
}

const editList = (list) => {
  editingList.value = list;
  listForm.name = list.name;
  listForm.description = list.description || '';
  listForm.is_public = !!list.is_public;
  showListForm.value = true;
};

const shareList = (listId) => {
  const link = `${window.location.origin}/player?playlist=${listId}`;
  navigator.clipboard.writeText(link).catch(() => {
    window.prompt('Copie o link da lista:', link);
  });
}

// Função para lidar com eventos de toque
const handleTouch = (event) => {
  // Não precisamos mais prevenir o comportamento padrão
  // Apenas executamos a ação desejada
}

const openCreateBandModal = () => {
  if (createBandModalRef.value) {
    createBandModalRef.value.ExecuteCreateBandModal();
  }
};

const openManageBandModal = (band) => {
  if (!band || !band.id) {
    console.error('Dados da banda inválidos');
    return;
  }

  // Limpa o valor anterior antes de definir o novo
  selectedBand.value = null;
  
  // Força uma atualização do DOM
  nextTick(() => {
    // Define o novo valor com uma cópia segura dos dados
    selectedBand.value = {
      ...band,
      members: band.members?.map(member => ({
        ...member,
        user: member.user || {},
        user_id: member.user_id || member.user?.id
      })) || []
    };

    // Aguarda outro ciclo para garantir que o modal foi atualizado
    nextTick(() => {
      if (manageBandModalRef.value) {
        manageBandModalRef.value.ExecuteManageBandModal();
      } else {
        console.error('Modal não encontrado');
      }
    });
  });
};

const handleBandDeleted = (bandId) => {
  // Remove a banda da lista
  const index = props.bands.findIndex(band => band.id === bandId);
  if (index !== -1) {
    props.bands.splice(index, 1);
  }
  
  // Limpa a banda selecionada
  selectedBand.value = null;
};

const saveList = () => {
  if (editingList.value) {
    listForm.put(route('playlists.update', editingList.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        showListForm.value = false;
        editingList.value = null;
        listForm.reset();
      },
    });
    return;
  }

  listForm.post(route('playlists.store'), {
    preserveScroll: true,
    onSuccess: () => {
      showListForm.value = false;
      listForm.reset();
    },
  });
};

const startNewList = () => {
  editingList.value = null;
  listForm.reset();
  showListForm.value = true;
};

</script>

<template>
  <AppHead title="Bandas e Listas de Músicas"/>
  <div class="IndexBands">
    <LayoutIndexPages>
      <!-- Alerta de erro -->
      <div v-if="flashMessage" class="alert alert-danger">
        {{ flashMessage }}
      </div>

      <div class="tabs">
        <button 
          :class="{ active: activeTab === 'bands' }" 
          @click="activeTab = 'bands'"
          @touchstart="activeTab = 'bands'"
        >
          <span class="material-symbols-outlined">groups</span>
          <span>Minhas Bandas</span>
        </button>
        <button 
          :class="{ active: activeTab === 'lists' }" 
          @click="activeTab = 'lists'"
          @touchstart="activeTab = 'lists'"
        >
          <span class="material-symbols-outlined">playlist_play</span>
          <span>Minhas Listas</span>
        </button>
      </div>

      <!-- Aba de Bandas -->
      <div v-if="activeTab === 'bands'" class="tab-content">
        <div class="header-actions">
          <h1>Minhas Bandas</h1>
          <button class="btn-create" @click="openCreateBandModal">
            <span class="material-symbols-outlined">add</span>
            Nova Banda
          </button>
        </div>
        <div class="bands-grid">
          <div v-for="band in bands" :key="band.id" class="band-card">
            <h3>{{ band.name }}</h3>
            <p>Seu papel: {{ userRole(band) }}</p>
            <div class="band-actions">
              <a :href="'/bands/' + band.id" class="btn-view">Ver Banda</a>
              <button @click="shareBand(band)" class="btn-share">Compartilhar</button>
              <button @click="openManageBandModal(band)" class="btn-manage">
                <span class="material-symbols-outlined">settings</span>
                Gerenciar
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Aba de Listas -->
      <div v-if="activeTab === 'lists'" class="tab-content">
        <h1>Minhas Listas de Músicas</h1>
        <button class="btn-create" @click="startNewList">Criar Nova Lista</button>
        <div v-if="showListForm" class="list-form">
          <h4>{{ editingList ? 'Editar lista' : 'Nova lista' }}</h4>
          <input v-model="listForm.name" class="form-control" placeholder="Nome" required />
          <textarea
            v-model="listForm.description"
            class="form-control"
            placeholder="Descrição (opcional)"
            rows="3"
          ></textarea>
          <label class="form-check mt-2">
            <input v-model="listForm.is_public" class="form-check-input" type="checkbox" />
            <span class="form-check-label">Lista pública</span>
          </label>
          <div class="list-form-actions">
            <button class="btn btn-primary" @click="saveList" :disabled="listForm.processing">
              Salvar
            </button>
            <button class="btn btn-outline-secondary" @click="showListForm = false">
              Cancelar
            </button>
          </div>
        </div>
        <div class="lists-grid">
          <div v-for="list in userLists" :key="list.id" class="list-card">
            <h3>{{ list.name }}</h3>
            <p>{{ list.songs?.length || 0 }} músicas</p>
            <div class="list-actions">
              <button @click="editList(list)" class="btn-edit" :disabled="!canManageList(list)">Editar</button>
              <button @click="shareList(list.id)" class="btn-share">Compartilhar</button>
              <button @click="deleteList(list.id)" class="btn-delete" :disabled="!canManageList(list)">Excluir</button>
            </div>
          </div>
        </div>
      </div>
    </LayoutIndexPages>
    <CreateBandModal ref="createBandModalRef" />
    <ManageBandModal
      v-if="selectedBand"
      ref="manageBandModalRef"
      :band="selectedBand"
      @band-deleted="handleBandDeleted"
    />
  </div>
</template>

<style lang="css" scoped>
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

.tabs {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 2rem;
  -webkit-tap-highlight-color: transparent;
  background: #f8f9fa;
  padding: 0.5rem;
  border-radius: 12px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.tabs button {
  flex: 1;
  padding: 1rem;
  border: none;
  background: transparent;
  cursor: pointer;
  border-radius: 8px;
  touch-action: manipulation;
  -webkit-tap-highlight-color: transparent;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s ease;
  color: #6c757d;
  font-weight: 500;
}

.tabs button .material-symbols-outlined {
  font-size: 24px;
  line-height: 1;
  margin-bottom: 0.25rem;
}

.list-form {
  background: #fff;
  border-radius: 12px;
  padding: 16px;
  margin: 16px 0;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
}

.list-form-actions {
  display: flex;
  gap: 12px;
  margin-top: 12px;
}

.tabs button.active {
  background: #007bff;
  color: white;
  box-shadow: 0 2px 4px rgba(0,123,255,0.2);
}

.tabs button:not(.active):hover {
  background: rgba(0,123,255,0.1);
  color: #007bff;
}

.tabs button.active .material-symbols-outlined {
  color: white;
}

.bands-grid, .lists-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 1rem;
  margin-top: 1rem;
  -webkit-tap-highlight-color: transparent;
}

.band-card, .list-card {
  padding: 1rem;
  border: 1px solid #ddd;
  border-radius: 8px;
  background: white;
  touch-action: manipulation;
  -webkit-tap-highlight-color: transparent;
}

.band-actions, .list-actions {
  display: flex;
  gap: 0.5rem;
  margin-top: 1rem;
  -webkit-tap-highlight-color: transparent;
}

.btn-view, .btn-edit, .btn-share, .btn-delete, .btn-create {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  touch-action: manipulation;
  -webkit-tap-highlight-color: transparent;
}

.btn-view {
  background: #007bff;
  color: white;
  text-decoration: none;
}

.btn-edit {
  background: #28a745;
  color: white;
}

.btn-share {
  background: #17a2b8;
  color: white;
}

.btn-delete {
  background: #dc3545;
  color: white;
}

.btn-create {
  background: #28a745;
  color: white;
  margin-bottom: 1rem;
}

/* Otimizações para mobile */
@media (max-width: 768px) {
  .tabs {
    padding: 0.25rem;
    margin: 0.5rem 1rem 1.5rem;
  }

  .tabs button {
    padding: 0.75rem;
  }

  .tabs button .material-symbols-outlined {
    font-size: 1.25rem;
  }

  .tabs button span {
    font-size: 0.8rem;
  }

  .bands-grid, .lists-grid {
    grid-template-columns: 1fr;
    padding: 0 0.5rem;
  }

  .band-actions, .list-actions {
    flex-direction: column;
    gap: 0.5rem;
  }

  .btn-view, .btn-edit, .btn-share, .btn-delete, .btn-create {
    width: 100%;
    padding: 0.75rem;
  }

  .band-card, .list-card {
    margin: 0.5rem 0;
  }
}

.header-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.header-actions h1 {
  margin: 0;
}

.btn-create {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  background: #28a745;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.btn-create .material-symbols-outlined {
  font-size: 20px;
}

.btn-create:hover {
  background: #218838;
  transform: translateY(-1px);
}

.btn-manage {
  background: #6c757d;
  color: white;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-manage .material-symbols-outlined {
  font-size: 18px;
}

@media (max-width: 768px) {
  .band-actions {
    flex-direction: column;
    gap: 0.5rem;
  }

  .btn-manage {
    width: 100%;
    justify-content: center;
  }
}
</style>
