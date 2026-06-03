<template>
  <div id="app">
    <Navbar v-if="!$route.meta.hideNavbar" :cart-count="cartItemCount" />
    <main>
      <router-view />
    </main>
    <Footer v-if="!$route.meta.hideFooter" />
  </div>
</template>
<script setup>
import { onMounted } from 'vue';
import Navbar from './components/Navbar.vue';
import Footer from './components/Footer.vue';
import { cartItemCount, updateCartCount } from './cartState';

onMounted(() => {
  updateCartCount();
});
</script>

<style>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

html, body {
  width: 100%;
  overflow-x: hidden;
}

#app {
  width: 100%;
  min-height: 100vh;
}

main {
  width: 100%;
}
</style>