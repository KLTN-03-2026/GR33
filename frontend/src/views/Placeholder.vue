<template>
  <div class="d-flex flex-column align-items-center justify-content-center" style="min-height:60vh; gap:16px">
    <div style="width:72px;height:72px;background:var(--primary);border-radius:20px;display:flex;align-items:center;justify-content:center;font-size:2rem;color:var(--primary-text)">
      <i :class="'bi ' + icon"></i>
    </div>
    <h2 style="font-size:22px;font-weight:700;color:var(--text-main)">{{ title }}</h2>
    <p style="color:var(--text-muted);font-size:14px">Trang này đang được phát triển</p>
    <router-link to="/" style="padding:8px 20px;background:var(--primary);color:var(--primary-text);border-radius:10px;text-decoration:none;font-weight:600;font-size:13px">
      ← Về Dashboard
    </router-link>
  </div>
</template>

<script>
export default {
  name: 'PlaceholderPage',
  props: {
    title: { type: String, default: 'Coming Soon' },
    icon:  { type: String, default: 'bi-box' }
  }
}
</script>
