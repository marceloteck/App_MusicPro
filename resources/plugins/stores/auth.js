import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const isAuthenticated = ref(false)

  const setUser = (userData) => {
    user.value = userData
    isAuthenticated.value = !!userData
  }

  const clearUser = () => {
    user.value = null
    isAuthenticated.value = false
  }

  const checkUserRole = (band) => {
    if (!user.value) return 'viewer'
    
    // Procura o membro da banda que corresponde ao usuário atual
    const member = band.members?.find(m => m.user_id === user.value.id)
    if (!member) return 'viewer'
    
    return member.role
  }

  return {
    user,
    isAuthenticated,
    setUser,
    clearUser,
    checkUserRole
  }
}) 