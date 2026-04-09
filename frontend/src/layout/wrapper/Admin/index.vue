<template>
  <div class="app-shell">
    <Sidebar :sidebar-collapsed="collapsed" />
    <Navbar 
      :sidebar-collapsed="collapsed" 
      @toggle-sidebar="collapsed = !collapsed"
    />
    <main class="main-content" :class="{ 'sidebar-collapsed': collapsed }">
      <router-view />
    </main>
  </div>
</template>

<script>
import Sidebar from '../../components/Admin/Sidebar.vue'
import Navbar from '../../components/Admin/Navbar.vue'

export default {
  name: 'AdminLayout',
  components: { Sidebar, Navbar },
  data() {
    return { 
      collapsed: false
    }
  }
}
</script>
