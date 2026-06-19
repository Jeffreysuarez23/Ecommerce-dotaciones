<template>
  <div class="dashboard-layout">
    
    <!-- LEFT SIDEBAR -->
    <AppSidebar :is-open="mobileMenuOpen" @close="mobileMenuOpen = false" />

    <!-- RIGHT MAIN WRAPPER -->
    <div class="main-viewport-container">
      
      <!-- TOP HEADER BAR -->
      <AppHeader @toggle-sidebar="mobileMenuOpen = !mobileMenuOpen" />

      <!-- PAGE BODY -->
      <main class="main-content">
        <router-view v-slot="{ Component }">
          <transition name="fade" mode="out-in">
            <component :is="Component" />
          </transition>
        </router-view>
      </main>

    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue'
import AppSidebar from './components/AppSidebar.vue'
import AppHeader from './components/AppHeader.vue'

const mobileMenuOpen = ref(false)
</script>

<style scoped>
.dashboard-layout {
  display: flex;
  width: 100%;
  min-height: 100vh;
}

.main-viewport-container {
  flex: 1;
  width: calc(100% - var(--sidebar-width));
  margin-left: var(--sidebar-width);
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  transition: width 0.3s ease, margin-left 0.3s ease;
}

.main-content {
  flex: 1;
  width: 100%;
  padding: 40px;
  display: block;
}

/* Page transitions */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.15s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Responsive adjustment for Sidebar shift */
@media (max-width: 992px) {
  .main-viewport-container {
    margin-left: 0;
  }
  .main-content {
    padding: 24px;
  }
}
</style>
