<template>
  <nav :style="styles.navbar">
    <h1 :style="styles.logo">Admin Dashboard</h1>
    <div :style="styles.menu">
      <Link href="/tasks" :style="styles.link">Home</Link>
      <Link href="/users" :style="styles.link">Users</Link>
      <Link href="/task-management" :style="styles.link">Task Management</Link> <!-- New Link -->
    </div>
  </nav>
</template>

<script>
import { Link } from '@inertiajs/vue3'; // Import Link from Inertia.js

export default {
  components: {
    Link
  },
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
        }
      }
    };
  }
};
</script>
