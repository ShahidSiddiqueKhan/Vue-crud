<template>
    <div :style="styles.containerStyle">
      <h1 :style="styles.titleStyle">Login</h1>
  
      <form @submit.prevent="submit" :style="styles.formStyle">
        <input 
          v-model="form.email" 
          type="email" 
          placeholder="Email" 
          :style="styles.inputStyle"
        >
        <input 
          v-model="form.password" 
          type="password" 
          placeholder="Password" 
          :style="styles.inputStyle"
        >
        <button 
          type="submit" 
          :style="styles.buttonStyle" 
          @mouseover="hoverLogin = true" 
          @mouseleave="hoverLogin = false"
          :class="{ 'hover': hoverLogin }"
        >
          Login
        </button>
      </form>
  
     
      <p :style="styles.signupTextStyle">
        Don't have an account? 
        <button 
          @click="redirectToSignup" 
          :style="styles.signupButtonStyle"
        >
          Sign Up
        </button>
      </p>
    </div>
  </template>
  
  <script>
  import { router } from '@inertiajs/vue3';
  import styles from '@/config/StyleConfig.js'; 
  
  export default {
    data() {
      return {
        form: { email: '', password: '' },
        styles,  
        hoverLogin: false
      };
    },
    methods: {
      submit() {
        router.post('/login', this.form);
      },
      redirectToSignup() {
        router.get('/signup');
      }
    }
  };
  </script>
  