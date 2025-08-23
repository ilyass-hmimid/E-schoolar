<template>
  <div class="fixed top-4 right-4 z-50 space-y-4">
    <Toast
      v-for="notification in notifications"
      :key="notification.id"
      :show="true"
      :type="notification.type"
      :title="notification.title"
      :message="notification.message"
      :duration="notification.duration"
      @close="removeNotification(notification.id)"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Toast from './Toast.vue'

const notifications = ref([])
let nextId = 1

const addNotification = (notification) => {
  const id = nextId++
  const newNotification = {
    id,
    type: notification.type || 'info',
    title: notification.title || '',
    message: notification.message || '',
    duration: notification.duration || 5000
  }
  
  notifications.value.push(newNotification)
  
  // Auto-remove after duration
  if (newNotification.duration > 0) {
    setTimeout(() => {
      removeNotification(id)
    }, newNotification.duration)
  }
  
  return id
}

const removeNotification = (id) => {
  const index = notifications.value.findIndex(n => n.id === id)
  if (index > -1) {
    notifications.value.splice(index, 1)
  }
}

const success = (title, message, duration = 5000) => {
  return addNotification({ type: 'success', title, message, duration })
}

const error = (title, message, duration = 5000) => {
  return addNotification({ type: 'error', title, message, duration })
}

const warning = (title, message, duration = 5000) => {
  return addNotification({ type: 'warning', title, message, duration })
}

const info = (title, message, duration = 5000) => {
  return addNotification({ type: 'info', title, message, duration })
}

// Expose methods globally
onMounted(() => {
  window.$notify = {
    success,
    error,
    warning,
    info,
    add: addNotification,
    remove: removeNotification
  }
})
</script>
