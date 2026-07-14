// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  modules: ['@nuxt/ui', '@nuxt/eslint'],
  ui: {
    colorMode: false,
  },
  components: [{ path: '~/components', pathPrefix: false }],
  css: ['~/assets/css/main.css'],
  devtools: { enabled: false },
  runtimeConfig: {
    apiInternalBaseUrl: process.env.NUXT_API_INTERNAL_BASE_URL || 'http://127.0.0.1:8000',
    public: {
      siteUrl: process.env.NUXT_PUBLIC_SITE_URL || 'http://localhost',
    },
  },
  icon: {
    localApiEndpoint: '/_nuxt_icon',
  },
  nitro: {
    devProxy: {
      '/api': { target: 'http://127.0.0.1:8000/api' },
      '/sanctum': { target: 'http://127.0.0.1:8000/sanctum' },
    },
  },
  vite: {
    optimizeDeps: {
      include: ['zod'],
    },
  },
  typescript: {
    strict: true,
  },
})
