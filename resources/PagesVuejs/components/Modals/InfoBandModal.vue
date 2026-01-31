<template>
  <LayoutModalMobile ref="RefInfoBandModal">
    <template #header>
      <h2>Informações da Banda</h2>
    </template>
    <div class="info-band-content">
      <div class="modal-header">
        <h2>{{ band?.name }}</h2>
      </div>

      <div class="modal-content">
        <div v-if="message" class="text-center p-4">
          <h2>{{ message }}</h2>
          <button class="btn btn-primary mt-3" @click="goBack">Voltar para minhas bandas</button>
          <img src="/Assets/image/error/403.webp" alt="Logo" width="200" class="mt-3" />
        </div>

        <div v-if="band">
          <!-- Nav Tabs -->
          <ul class="nav nav-tabs nav-fill mb-3" id="bandTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab">Informações</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="members-tab" data-bs-toggle="tab" data-bs-target="#members" type="button" role="tab">Membros</button>
            </li>
          </ul>

          <!-- Tab Content -->
          <div class="tab-content" id="bandTabContent">
            <!-- Informações da Banda -->
            <div class="tab-pane fade show active" id="info" role="tabpanel">
                    <p><strong>Gênero:</strong> {{ band.genre }}</p>
                    <p><strong>Membros:</strong> {{ band.members.length }}</p>
                    <p><strong>Criado em:</strong> {{ formatDate(band.created_at) }}</p>
                    <p><strong>Atualizado em:</strong> {{ formatDate(band.updated_at) }}</p>
                    <p><strong>Descrição:</strong> <br>{{ band.description }}</p>
            </div>

            <!-- Membros -->
            <div class="tab-pane fade" id="members" role="tabpanel">
              <div v-for="member in band.members" :key="member.id" class="mb-2 member-item">
                {{ member.user.name }} <span class="text-muted">{{ member.role }}</span>
              </div>
            </div>

          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button @click="closeModal" class="btn-cancel">Fechar</button>
      </div>
    </div>
  </LayoutModalMobile>
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'

const page = usePage()

const props = defineProps({
  band: { type: Object, required: true },
})

// Acessa a versão mais atualizada de band, vinda do page.props
const band = computed(() => page.props.band)


const RefInfoBandModal = ref(null)

const ExecuteInfoBandModal = () => {
  if (RefInfoBandModal.value) {
    RefInfoBandModal.value.openModal()
  } else {
    console.error('Modal reference not found')
  }
}

defineExpose({
  ExecuteInfoBandModal
})

const closeModal = () => {
  if (RefInfoBandModal.value) {
    RefInfoBandModal.value.closeModal();
  }
};


const showEventForm = ref(false)

function toggleEventForm() {
  showEventForm.value = !showEventForm.value
}


const currentUserId = computed(() => page.props.auth.user.id)


const newEvent = ref({
  title: '',
  date: '',
  description: '',
})

const goBack = () => {
  router.visit('/bands')
}

const currentMember = computed(() => {
  if (!props.band?.members || !currentUserId.value) return null;
  return props.band.members.find(m => m.user_id === currentUserId.value) || null;
});

const isAdmin = computed(() => currentMember.value?.role === 'admin');
const isEditor = computed(() => currentMember.value?.role === 'editor');


const formatDate = date => new Date(date).toLocaleDateString('pt-BR')

const createEvent = async () => {
  await router.post(`/bands/${props.band.id}/events`, newEvent.value, {
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire('Evento criado!', '', 'success')
      newEvent.value = { title: '', date: '', description: '' }
    },
    onError: errors => {
      Swal.fire('Erro', Object.values(errors).flat().join('\n'), 'error')
    }
  })
}

const viewEvent = id => router.visit(`/bands/${props.band.id}/events/${id}`)
</script>

<style lang="css" scoped>
.modal-content {
  padding: 0rem;
  overflow-y: auto;
}

.section {
  margin-bottom: 2rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #eee;
}

.section h3 {
  margin-bottom: 1rem;
  color: #333;
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  color: #666;
}

.form-group input,
.form-group textarea,
.form-group select {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.members-list {
  margin-bottom: 1.5rem;
}

.member-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem;
  border: 1px solid #eee;
  border-radius: 4px;
  margin-bottom: 0.5rem;
}

.member-info {
  display: flex;
  flex-direction: column;
}

.member-name {
  font-weight: 500;
}

.member-role {
  font-size: 0.875rem;
  color: #666;
}

.member-actions {
  display: flex;
  gap: 0.5rem;
}

.add-member {
  margin-top: 1.5rem;
}

.add-member h4 {
  margin-bottom: 1rem;
}

.btn-update,
.btn-add,
.btn-transfer,
.btn-delete {
  width: 100%;
  padding: 0.75rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 500;
  margin-bottom: 0.5rem;
}

.btn-update {
  background: #28a745;
  color: white;
}

.btn-add {
  background: #17a2b8;
  color: white;
}

.btn-transfer {
  background: #ffc107;
  color: #000;
}

.btn-delete {
  background: #dc3545;
  color: white;
}

.btn-remove {
  padding: 0.25rem 0.5rem;
  background: #dc3545;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.btn-leave {
  background: #d6922b;
  color: white;
  width: 100%;
  padding: 0.75rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 500;
  margin-bottom: 0.5rem;
}

.danger-zone {
  border-color: #dc3545;
}

.danger-zone h3 {
  color: #dc3545;
}

.modal-footer {
  padding: 1rem;
  border-top: 1px solid #eee;
  text-align: right;
}

.btn-cancel {
  padding: 0.5rem 1rem;
  background: #6c757d;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
</style> 