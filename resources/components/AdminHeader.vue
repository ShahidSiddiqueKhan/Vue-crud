<template>
  <nav :style="styles.navbar">
    <h1 :style="styles.logo">Admin Dashboard</h1>
    <div :style="styles.menu">
      <router-link to="/tasks" :style="styles.link">Home</router-link>
      <router-link to="/users" :style="styles.link">Users</router-link>
    </div>
  </nav>
</template>

<script>
export default {
  props: ['userRole'],
  data() {
    return {
      styles: {
        navbar: {
          display: 'flex',
          justifyContent: 'space-between',
          alignItems: 'center',
          backgroundColor: '#2c3e50',
          padding: '10px 20px',
          color: 'white'
        },
        logo: {
          fontSize: '20px',
          fontWeight: 'bold'
        },
        menu: {
          display: 'flex',
          gap: '20px'
        },
        link: {
          color: 'white',
          textDecoration: 'none',
          fontSize: '16px',
          padding: '5px 10px',
          transition: 'background 0.3s',
          cursor: 'pointer' // Ensure it's clickable
        }
      }
    };
  }
};
</script>
