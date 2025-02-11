<template>
    <div class="p-6">
      <h1 class="text-xl font-bold mb-4">Edit Task</h1>
      <form @submit.prevent="updateTask">
        <input v-model="form.title" placeholder="Title" class="border p-2">
        <textarea v-model="form.description" placeholder="Description" class="border p-2 mt-2"></textarea>
        <button type="submit" class="bg-blue-500 text-white p-2 mt-2">Update</button>
      </form>
    </div>
  </template>
  
  <script>
  import { router } from '@inertiajs/vue3';
  
  export default {
    props: ['task'],
    data() {
      return {
        form: { title: this.task.title, description: this.task.description },
      };
    },
    methods: {
      updateTask() {
        router.put(`/tasks/${this.task.id}`, this.form);
      },
    },
  };
  </script>
  