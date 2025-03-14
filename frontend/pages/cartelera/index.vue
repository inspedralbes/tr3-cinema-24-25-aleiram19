<template>
  <div class="min-h-screen flex flex-col bg-[#051D40]">
    <LandingPageNavBar />
    <!-- Ajustado el padding top para el navbar fijo -->
    <div class="pt-24">
      <CarteleraPeliculasGrid />
    </div>
    <LandingPageFooter />
  </div>
</template>

<script>
export default {
  name: 'CarteleraPage',
  head() {
    return {
      title: 'Cartelera - CineXeperience',
      meta: [
        { hid: 'description', name: 'description', content: 'Descubre las películas en cartelera en CineXeperience' }
      ]
    }
  }
}
</script>