<script setup lang="ts">
const config = useRuntimeConfig()

useSeoMeta({
  title: 'TaskFlow — Make the next step obvious',
  description: 'Create, organize and finish tasks with clear deadlines and simple status tracking.',
  ogTitle: 'TaskFlow — Make the next step obvious',
  ogDescription: 'A focused task list for clear daily progress.',
  ogType: 'website',
  robots: 'index, follow',
})

useHead({ link: [{ rel: 'canonical', href: config.public.siteUrl }] })
</script>

<template>
  <div class="landing">
    <section class="hero">
      <div class="hero__copy">
        <p class="eyebrow">A quieter way to plan</p>
        <h1>Make the next step <span>obvious.</span></h1>
        <p class="hero__lead">TaskFlow turns a crowded week into a short, honest list: what is waiting, what is moving, and what is done.</p>
        <div class="hero__actions">
          <UButton to="/login" label="Open your task list" size="xl" />
          <a href="#how-it-works" class="text-link">See the flow</a>
        </div>
      </div>
      <div class="status-rail" aria-label="Task status flow">
        <article class="status-card status-card--pending">
          <span>Pending</span><strong>Plan the week</strong><small>Due tomorrow</small>
        </article>
        <article class="status-card status-card--progress">
          <span>In progress</span><strong>Write project notes</strong><small>Moving now</small>
        </article>
        <article class="status-card status-card--done">
          <span>Completed</span><strong>Review priorities</strong><small>Closed today</small>
        </article>
      </div>
    </section>
    <section id="how-it-works" class="feature-strip" aria-labelledby="features-title">
      <div><p class="eyebrow">One useful loop</p><h2 id="features-title">Capture. Focus. Finish.</h2></div>
      <article><strong>Capture clearly</strong><p>Give each task a useful title, context, and deadline.</p></article>
      <article><strong>Find what matters</strong><p>Search, filter, and sort without losing your place.</p></article>
      <article><strong>Close the loop</strong><p>Move work forward and keep completed tasks visible.</p></article>
    </section>
  </div>
</template>
