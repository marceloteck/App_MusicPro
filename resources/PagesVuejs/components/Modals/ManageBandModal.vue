<template>
  <div class="ManageBandModalClass">
    <LayoutModalMobile
      ref="RefManageBandModal"
      :height="modalHeight"
    >
      <div class="modal-header"  v-if="isAdmin">
        <h2>Gerenciar Banda: {{ band?.name }}</h2>
      </div>

      <div class="modal-content">
        <!-- Seção de Informações da Banda -->
        <div class="section"  v-if="isAdmin || isEditor">
          <h3>Informações da Banda</h3>
          <div class="form-group">
            <label for="bandName">Nome da Banda</label>
            <input 
              type="text" 
              id="bandName" 
              v-model="form.name" 
              placeholder="Digite o novo nome da banda"
            >
          </div>
          <div class="form-group">
            <label for="bandDescription">Descrição</label>
            <textarea 
              id="bandDescription" 
              v-model="form.description" 
              rows="3"
              placeholder="Descreva sua banda"
            ></textarea>
          </div>
          <div class="form-group">
            <label for="bandGenre">Gênero Musical</label>
            <input 
              type="text" 
              id="bandGenre" 
              v-model="form.genre" 
              placeholder="Ex: Rock, Pop, Jazz..."
            >
          </div>
          <button @click="updateBand" class="btn-update">Atualizar Informações</button>
        </div>

        <!-- Seção de Gerenciamento de Membros -->
        <div class="section">
          <h3  v-if="isAdmin">Gerenciar Membros</h3>
          <h3  v-if="!isAdmin">Membros da banda</h3>
          <div class="members-list">
            <div v-for="member in band?.members || []" :key="member.id" class="member-item">
              <div class="member-info">
                <span class="member-name">{{ member.user?.name }}</span>
                <span class="member-role">{{ member.role }}</span>
              </div>
              <div class="member-actions" v-if="isAdmin">
                <select v-model="member.role" @change="updateMemberRole(member.id, member.role)">
                  <option value="admin">Admin</option>
                  <option value="editor">Editor</option>
                  <option value="viewer">Visualizador</option>
                </select>
                <button @click="removeMember(member)" class="btn-remove">Remover</button>
              </div>
            </div>
          </div>

          <!-- Adicionar Novo Membro -->
          <div class="add-member"  v-if="isAdmin">
            <h4>Adicionar Novo Membro</h4>
            <div class="form-group">
              <input 
                type="email" 
                v-model="newMemberEmail" 
                placeholder="Digite o email do usuário"
                @keyup.enter="addMember"
              >
              <select v-model="newMemberRole">
                <option value="editor">Editor</option>
                <option value="viewer">Visualizador</option>
              </select>
            </div>
            <button @click="addMember" class="btn-add">Adicionar Membro</button>
          </div>
        </div>

        <!-- Seção de Transferência de Banda -->
        <div class="section" v-if="isvalue">
          <h3>Transferir Banda</h3>
          <div class="form-group">
            <select v-model="transferToUserId">
              <option value="" disabled>Selecione um membro</option>
              <option 
                v-for="member in availableMembers" 
                :key="member.id" 
                :value="member.user_id">
                {{ member.user && member.user.name }}
              </option>
            </select>
          </div>
          <button @click="transferBand" class="btn-transfer">Transferir Banda</button>
        </div>


        <!-- Seção de Exclusão -->
        <div class="section danger-zone" v-if="isAdmin">
          <h3>Zona de Perigo</h3>
          <button @click="deleteBand" class="btn-delete">Excluir Banda</button>
        </div>

        <!-- Seção para Sair da Banda -->
        <div class="section danger-zone" v-if="!isAdmin">
          <h3>Sair da Banda</h3>
          <button @click="leaveBand" class="btn-leave">Sair da Banda</button>
        </div>
      </div>

      <div class="modal-footer">
        <button @click="closeModal" class="btn-cancel">Fechar</button>
      </div>
    </LayoutModalMobile>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { useAuthStore } from '@resources/plugins/stores/auth.js';
import axios from 'axios';
import Swal from 'sweetalert2';

const isvalue = ref(false);

const props = defineProps({
  band: {
    type: Object,
    required: true,
    validator: (value) => {
      return value && typeof value === 'object' && value.id;
    }
  }
});

const RefManageBandModal = ref(null);
const authStore = useAuthStore();
const modalHeight = ref('90vh');
const error = ref(null);

const form = ref({
  name: props.band?.name || '',
  description: props.band?.description || '',
  genre: props.band?.genre || ''
});

// Observa mudanças na prop band e atualiza o form
watch(() => props.band, (newBand) => {
  if (newBand) {
    form.value = {
      name: newBand.name || '',
      description: newBand.description || '',
      genre: newBand.genre || ''
    };
  }
}, { immediate: true });

const newMemberEmail = ref('');
const newMemberRole = ref('viewer');
const transferToUserId = ref('');

const currentUserId = computed(() => authStore.user?.id);
const isAdmin = computed(() => {
  if (!props.band?.members || !currentUserId.value) return false;
  const member = props.band.members.find(m => m && m.user_id === currentUserId.value);
  return member?.role === 'admin';
});
const isEditor = computed(() => {
  if (!props.band?.members || !currentUserId.value) return false;
  const member = props.band.members.find(m => m && m.user_id === currentUserId.value);
  return member?.role === 'editor';
});

const ExecuteManageBandModal = () => {
  if (RefManageBandModal.value) {
    RefManageBandModal.value.openModal();
  } else {
    console.error('Modal não encontrado');
  }
};

defineExpose({ ExecuteManageBandModal });

const closeModal = () => {
  if (RefManageBandModal.value) {
    RefManageBandModal.value.closeModal();
  }
};

const updateBand = () => {
  if (!props.band?.id) {
    console.error('ID da banda não encontrado');
    return;
  }

  console.log('Dados sendo enviados para atualização:', {
    bandId: props.band.id,
    formData: form.value
  });

  router.put(`/bands/${props.band.id}`, {
    ...form.value,
    _method: 'PUT'
  }, {
    preserveScroll: true,
    onSuccess: (response) => {
      console.log('Banda atualizada com sucesso:', response);
      if (response.band) {
        props.band = response.band;
      }
      
      // Exibe o alerta de sucesso
      if (response.props?.flash?.success) {
        Swal.fire({
          title: response.props.flash.success,
          icon: 'success',
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true
        });
      }
      
      closeModal();
      router.reload();
    },
    onError: (errors) => {
      console.error('Erro ao atualizar banda:', errors);
      error.value = errors.message || 'Erro ao atualizar banda';
      
      // Exibe o alerta de erro
      if (errors.flash?.error) {
        Swal.fire({
          title: errors.flash.error,
          icon: 'error'
        });
      } else {
        Swal.fire({
          title: 'Erro ao atualizar banda',
          text: errors.message || 'Ocorreu um erro inesperado',
          icon: 'error'
        });
      }
    }
  });
};

const updateMemberRole = async (memberId, newRole) => {
  try {
    // Verifica se está tentando remover o último administrador
    const adminCount = props.band.members.filter(m => m.role === 'admin').length;
    const member = props.band.members.find(m => m.id === memberId);
    
    if (member.role === 'admin' && newRole !== 'admin' && adminCount <= 1) {
      Swal.fire({
        title: 'Ação não permitida',
        text: 'A banda deve ter pelo menos um administrador.',
        icon: 'warning'
      });
      return;
    }
    
    // Atualiza o papel do membro localmente
    const memberIndex = props.band.members.findIndex(m => m.id === memberId);
    if (memberIndex !== -1) {
      props.band.members[memberIndex].role = newRole;
    }
    
    // Envia a atualização para o servidor
    await router.put(`/bands/${props.band.id}/members/${memberId}`, {
      role: newRole
    }, {
      preserveScroll: true,
      onSuccess: () => {
        // Atualização bem sucedida
        console.log('Papel do membro atualizado com sucesso');
        Swal.fire({
          title: 'Papel atualizado',
          text: 'O papel do membro foi atualizado com sucesso.',
          icon: 'success',
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true
        });
        ExecuteManageBandModal();
      },
      onError: (errors) => {
        // Em caso de erro, reverte a mudança local
        if (memberIndex !== -1) {
          props.band.members[memberIndex].role = member.role;
        }
        console.error('Erro ao atualizar papel do membro:', errors);
        Swal.fire({
          title: 'Erro',
          text: errors?.message || 'Erro ao atualizar papel do membro',
          icon: 'error'
        });
      }
    });
  } catch (error) {
    console.error('Erro ao atualizar papel do membro:', error);
    Swal.fire({
      title: 'Erro',
      text: error?.message || 'Erro inesperado ao atualizar papel do membro',
      icon: 'error'
    });
  }
};

const addMember = async () => {
  if (!props.band?.id) return;

  try {
    await router.post(`/bands/${props.band.id}/members`, {
      email: newMemberEmail.value,
      role: newMemberRole.value
    }, {
      preserveScroll: true,
      onSuccess: (response) => {
        // Exibe o alerta de sucesso
        if (response.props?.flash?.success) {
          Swal.fire({
            title: response.props.flash.success,
            icon: 'success',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
          });
        }
        
        
        newMemberEmail.value = '';
        newMemberRole.value = '';
        
        // Atualiza a lista de membros
        if (response.props?.band) {
          props.band = response.props.band;
        }
      },
      onError: (errors) => {
        // Exibe o alerta de erro
        if (errors.flash?.error) {
          Swal.fire({
            title: errors.flash.error,
            icon: 'error'
          });
        } else {
          Swal.fire({
            title: 'Erro ao adicionar membro',
            text: errors.message || 'Ocorreu um erro inesperado',
            icon: 'error'
          });
        }
      }
    });
  } catch (error) {
    Swal.fire({
      title: 'Erro ao adicionar membro',
      text: error?.response?.data?.message || 'Erro inesperado ao adicionar membro',
      icon: 'error'
    });
  }
};

const removeMember = async (member) => {
  if (!props.band?.id || !member?.id) return;

  const result = await Swal.fire({
    title: 'Tem certeza?',
    text: "Deseja remover este membro?",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sim, remover!',
    cancelButtonText: 'Cancelar'
  });

  if (!result.isConfirmed) return;

  try {
    await router.delete(`/bands/${props.band.id}/members/${member.id}`, {
      preserveScroll: true,
      onSuccess: () => {
        Swal.fire(
          'Removido!',
          'Membro removido com sucesso.',
          'success'
        );
        ExecuteManageBandModal();
      },
      onError: (errors) => {
        Swal.fire({
          title: errors?.message || 'Erro ao remover membro',
          icon: 'error'
        });
      }
    });
  } catch (error) {
    Swal.fire({
      title: 'Erro ao remover membro: ' + (error?.message || 'Erro desconhecido'),
      icon: 'error'
    });
  }
};

const transferBand = async () => {
  if (!props.band?.id || !transferToUserId.value) return;
  
  const result = await Swal.fire({
    title: 'Transferir liderança',
    text: 'Tem certeza que deseja transferir a liderança desta banda?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sim, transferir!',
    cancelButtonText: 'Cancelar'
  });
  
  if (!result.isConfirmed) return;
  
  try {
    await router.put(`/bands/${props.band.id}/transfer`, {
      user_id: transferToUserId.value
    });
    Swal.fire({
      title: 'Transferido!',
      text: 'A liderança da banda foi transferida com sucesso.',
      icon: 'success',
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true
    });
    router.reload();
  } catch (error) {
    console.error('Erro ao transferir banda:', error);
    Swal.fire({
      title: 'Erro',
      text: error?.message || 'Erro ao transferir liderança da banda',
      icon: 'error'
    });
  }
};

const deleteBand = () => {
  if (!props.band?.id) {
    console.error('ID da banda não encontrado');
    return;
  }

  Swal.fire({
    title: 'Excluir banda',
    text: 'Tem certeza que deseja excluir esta banda? Esta ação não pode ser desfeita.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sim, excluir!',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      //console.log('Tentando excluir banda:', props.band.id);
      closeModal();

      router.delete(`/bands/${props.band.id}`, {}, {
        preserveScroll: true,
        onSuccess: () => {
          console.log('Banda excluída com sucesso');
          Swal.fire({
            title: 'Excluído!',
            text: 'A banda foi excluída com sucesso.',
            icon: 'success',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
          });
          router.visit('/bands');
        },
        onError: (errors) => {
          console.error('Erro ao excluir banda:', errors);
          error.value = errors.message || 'Erro ao excluir banda';
          Swal.fire({
            title: 'Erro',
            text: errors?.message || 'Erro ao excluir banda',
            icon: 'error'
          });
        }
      });
    }
  });
};

const leaveBand = () => {
  if (!props.band?.id) {
    console.error('ID da banda não encontrado');
    return;
  }

  Swal.fire({
    title: 'Sair da banda',
    text: 'Tem certeza que deseja sair desta banda?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sim, sair!',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      //console.log('Tentando sair da banda:', props.band.id);
      closeModal();

      router.delete(`/bands/${props.band.id}/leave`, {}, {
        preserveScroll: true,
        onSuccess: (response) => {
          console.log('Usuário saiu da banda com sucesso');
          if (response.props?.flash?.success) {
            Swal.fire({
              title: response.props.flash.success,
              icon: 'success',
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 3000,
              timerProgressBar: true
            });
          }
          router.visit('/bands');
        },
        onError: (errors) => {
          console.error('Erro ao sair da banda:', errors);
          if (errors.flash?.error) {
            Swal.fire({
              title: errors.flash.error,
              icon: 'error'
            });
          } else {
            Swal.fire({
              title: 'Erro ao sair da banda',
              text: errors.message || 'Ocorreu um erro inesperado',
              icon: 'error'
            });
          }
        }
      });
    }
  });
};

const availableMembers = computed(() => {
  if (!props.band?.members || !currentUserId.value) return [];
  return props.band.members.filter(member => 
    member && member.user_id && member.user_id !== currentUserId.value
  );
});
</script>

<style lang="css" scoped>
.modal-content {
  padding: 1rem;
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