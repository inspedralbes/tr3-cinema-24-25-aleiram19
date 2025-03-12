<template>
  <div class="min-h-screen flex flex-col bg-[#051D40]">
    <LandingPageNavBar />
    <div class="pt-20"><CarteleraPeliculasGrid /></div>
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
