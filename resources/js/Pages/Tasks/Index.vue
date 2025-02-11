<template>
  <div :style="styles.containerStyle">

    <button @click="logout" :style="styles.logoutButtonStyle">Logout</button>

  
    <button @click="openModal(null)" :style="styles.buttonStyle">Add Task</button>
    
  
    <table :style="styles.tableStyle">
      <thead>
        <tr>
          <th :style="styles.thStyle">Title</th>
          <th :style="styles.thStyle">Description</th>
          <th :style="styles.thStyle">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="task in tasks" :key="task.id">
          <td :style="styles.tdStyle">{{ task.title }}</td>
          <td :style="styles.tdStyle">{{ task.description }}</td>
          <td :style="styles.tdStyle">
            <button @click="openModal(task)" :style="styles.editButtonStyle">Edit</button>
            <button @click="confirmDelete(task.id)" :style="styles.deleteButtonStyle">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>

  
    <div v-if="showModal" :style="styles.modalOverlay">
      <div :style="styles.modalContent">
        <h2 :style="styles.titleStyle">{{ editingTask ? 'Edit Task' : 'Add Task' }}</h2>
        <form @submit.prevent="saveTask" :style="styles.formStyle">
          <input v-model="form.title" placeholder="Task Title" :style="styles.inputStyle">
          <textarea v-model="form.description" placeholder="Task Description" :style="styles.textareaStyle"></textarea>
          <button type="submit" :style="styles.buttonStyle">{{ editingTask ? 'Update' : 'Add' }}</button>
          <button type="button" @click="closeModal" :style="styles.closeButtonStyle">Cancel</button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { router } from '@inertiajs/vue3';
import styles from '@/config/StyleConfig.js';

export default {
  props: ['tasks'],
  data() {
    return {
      form: { title: '', description: '' },
      showModal: false,
      editingTask: null,
      styles
    };
  },
  methods: {
    openModal(task) {
      this.editingTask = task;
      this.form = task ? { ...task } : { title: '', description: '' };
      this.showModal = true;
    },
    saveTask() {
      if (this.editingTask) {
        router.put(`/tasks/${this.editingTask.id}`, this.form, {
          onSuccess: () => this.closeModal()
        });
      } else {
        router.post('/tasks', this.form, {
          onSuccess: () => this.closeModal()
        });
      }
    },
    confirmDelete(id) {
      if (confirm("Are you sure you want to delete this task?")) {
        this.deleteTask(id);
      }
    },
    deleteTask(id) {
      router.delete(`/tasks/${id}`);
    },
    closeModal() {
      this.showModal = false;
      this.editingTask = null;
      this.form = { title: '', description: '' };
    },
    logout() {
      router.post('/logout', {}, {
        onSuccess: () => router.visit('/login')
      });
    }
  }
};
</script>
