<template>
  <div :style="styles.containerStyle">
    <button @click="logout" :style="styles.logoutButtonStyle">Logout</button>
    <button @click="openModal(null)" :style="styles.buttonStyle">Add Task</button>

    <table :style="styles.tableStyle">
      <thead>
        <tr>
          <th :style="styles.thStyle">Title</th>
          <th :style="styles.thStyle">Description</th>
          <th :style="styles.thStyle">Status</th>
          <th :style="styles.thStyle">Due Date</th>
          <th :style="styles.thStyle">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="task in tasks" :key="task.id">
          <td :style="styles.tdStyle">{{ task.title }}</td>
          <td :style="styles.tdStyle">{{ task.description }}</td>
          <td :style="styles.tdStyle">{{ task.status }}</td>
          <td :style="styles.tdStyle">{{ task.due_date }}</td>
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
          <span v-if="errorMessage" style="color: red; font-size: 12px;">{{ errorMessage }}</span>
          <textarea v-model="form.description" placeholder="Task Description" :style="styles.textareaStyle"></textarea>

          <div>
            <label>Status:</label>
            <input type="radio" id="pending" value="Pending" v-model="form.status">
            <label for="pending">Pending</label>
            <input type="radio" id="completed" value="Completed" v-model="form.status">
            <label for="completed">Completed</label>
          </div>

          <div>
            <label for="dueDateToggle">Set Due Date:</label>
            <input type="checkbox" id="dueDateToggle" v-model="dueDateToggle" />
          </div>

          <div v-if="dueDateToggle">
            <label for="dueDate">Select Due Date:</label>
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

export default {
  props: ['tasks'],
  data() {
    return {
      form: { title: '', description: '', status: 'Pending', due_date: '' },
      showModal: false,
      editingTask: null,
      styles,
      errorMessage: "",
      dueDateToggle: false  
    };
  },
  methods: {
    openModal(task) {
      if (task && task.id) {
        this.editingTask = task;
        this.form = { ...task };
        this.dueDateToggle = !!task.due_date; 
      } else {
        this.editingTask = null;
        this.form = { title: '', description: '', status: 'Pending', due_date: '' };
        this.dueDateToggle = false;
      }
      this.showModal = true;
    },

    async saveTask() {
      const alphaRegex = /^[A-Za-z\s]+$/;

      if (!alphaRegex.test(this.form.title)) {
        this.errorMessage = "Title must only contain alphabetic characters and spaces.";
        return; 
      }

      this.errorMessage = "";
      const taskId = this.editingTask?.id;
      const apiUrl = taskId ? `/tasks/${taskId}` : '/tasks';
      
      try {
        await apiClient({
          method: taskId ? 'put' : 'post',
          url: apiUrl,
          data: this.form
        });
        this.refreshTasks();
        this.closeModal();
      } catch (error) {
        console.error("Error saving task:", error.response?.data || error.message);
      }
    },

    async deleteTask(id) {
      if (!id) {
        console.error("Error: Task ID is undefined");
        return;
      }
      try {
        await apiClient.delete(`/tasks/${id}`);
        this.refreshTasks();
      } catch (error) {
        console.error("Error deleting task:", error.response?.data || error.message);
      }
    },

    confirmDelete(id) {
      if (confirm("Are you sure you want to delete this task?")) {
        this.deleteTask(id);
      }
    },

    async refreshTasks() {
      try {
        const response = await apiClient.get('/tasks');
        this.$emit('update:tasks', response.data);
      } catch (error) {
        console.error("Error fetching tasks:", error.response?.data || error.message);
      }
    },

    closeModal() {
      this.showModal = false;
      this.editingTask = null;
      this.form = { title: '', description: '', status: 'Pending', due_date: '' };
      this.dueDateToggle = false;
    },

    async logout() {
      try {
        await apiClient.post('/logout');
        window.location.href = '/login';
      } catch (error) {
        console.error("Error logging out:", error.response?.data || error.message);
      }
    }
  }
};
</script>
