<template>
    <div :style="styles.containerStyle">
      <button @click="logout" :style="styles.logoutButtonStyle">Logout</button>
      
      <div v-if="user.role === 'admin'">
        <button @click="openModal(null)" :style="styles.buttonStyle">Add Task</button>
      </div>
  
      <table :style="styles.tableStyle">
        <thead>
          <tr>
            <th :style="styles.thStyle">Title</th>
            <th :style="styles.thStyle">Description</th>
            <th :style="styles.thStyle">Status</th>
            <th v-if="user.role === 'admin'" :style="styles.thStyle">Assigned To</th>
            <th :style="styles.thStyle">Due Date</th>
            <th :style="styles.thStyle">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="task in tasks" :key="task.id">
            <td :style="styles.tdStyle">{{ task.title }}</td>
            <td :style="styles.tdStyle">{{ task.description }}</td>
            <td :style="styles.tdStyle">{{ task.status }}</td>
            <td v-if="user.role === 'admin'" :style="styles.tdStyle">{{ task.assigned_user }}</td>
            <td :style="styles.tdStyle">{{ task.due_date }}</td>
            <td :style="styles.tdStyle">
              <button v-if="user.role === 'admin'" @click="openModal(task)" :style="styles.editButtonStyle">Edit</button>
              <button v-if="user.role === 'admin'" @click="confirmDelete(task.id)" :style="styles.deleteButtonStyle">Delete</button>
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
  
            <div>
              <label>Status:</label>
              <select v-model="form.status" :style="styles.inputStyle">
                <option value="Pending">Pending</option>
                <option value="Completed">Completed</option>
              </select>
            </div>
  
            <div v-if="user.role === 'admin'">
              <label for="assignedTo">Assign To:</label>
              <select v-model="form.assigned_to" :style="styles.inputStyle">
                <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
              </select>
            </div>
  
            <div>
              <label for="dueDate">Due Date:</label>
              <input type="date" v-model="form.due_date" :style="styles.inputStyle">
            </div>
  
            <button type="submit" :style="styles.buttonStyle">{{ editingTask ? 'Update' : 'Add' }}</button>
            <button type="button" @click="closeModal" :style="styles.closeButtonStyle">Cancel</button>
          </form>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import apiClient from '@/config/axios.js';
  import styles from '@/config/StyleConfig.js';
  import { getUser } from '@/auth.js';
  
  export default {
    data() {
      return {
        form: { title: '', description: '', status: 'Pending', assigned_to: '', due_date: '' },
        showModal: false,
        editingTask: null,
        tasks: [],
        users: [],
        user: {},
        styles,
      };
    },
    async created() {
      this.user = await getUser();
      this.fetchTasks();
      if (this.user.role === 'admin') {
        this.fetchUsers();
      }
    },
    methods: {
      async fetchTasks() {
        try {
          const response = await apiClient.get('/tasks');
          this.tasks = response.data;
        } catch (error) {
          console.error("Error fetching tasks:", error);
        }
      },
      async fetchUsers() {
        try {
          const response = await apiClient.get('/users');
          this.users = response.data;
        } catch (error) {
          console.error("Error fetching users:", error);
        }
      },
      openModal(task) {
        if (task) {
          this.editingTask = task;
          this.form = { ...task };
        } else {
          this.editingTask = null;
          this.form = { title: '', description: '', status: 'Pending', assigned_to: '', due_date: '' };
        }
        this.showModal = true;
      },
      async saveTask() {
        const apiUrl = this.editingTask ? `/tasks/${this.editingTask.id}` : '/tasks';
        const method = this.editingTask ? 'put' : 'post';
        try {
          await apiClient({ method, url: apiUrl, data: this.form });
          this.fetchTasks();
          this.showModal = false;
        } catch (error) {
          console.error("Error saving task:", error);
        }
      },
      async deleteTask(id) {
        try {
          await apiClient.delete(`/tasks/${id}`);
          this.fetchTasks();
        } catch (error) {
          console.error("Error deleting task:", error);
        }
      },
      confirmDelete(id) {
        if (confirm("Are you sure you want to delete this task?")) {
          this.deleteTask(id);
        }
      },
      async logout() {
        try {
          await apiClient.post('/logout');
          window.location.href = '/login';
        } catch (error) {
          console.error("Error logging out:", error);
        }
      }
    }
  };
  </script>
  