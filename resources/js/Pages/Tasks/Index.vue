<template>
  <AdminHeader v-if="userRole === 'admin' || userRole === 'super-admin'" :userRole="userRole" />
<div :style="styles.containerStyle">

<button @click="logout" :style="styles.logoutButtonStyle">Logout</button>
<button 
 v-if="userRole === 'admin' || userRole === 'super-admin'" 
 @click="openModal(null)" 
 :style="styles.buttonStyle">
 Add Task
</button>

<table :style="styles.tableStyle">
 <thead>
   <tr>
     <th :style="styles.thStyle">Title</th>
     <th :style="styles.thStyle">Description</th>
     <th :style="styles.thStyle">Status</th>
     <th :style="styles.thStyle">Due Date</th>
     <th :style="styles.thStyle">Priority</th>
     <th :style="styles.thStyle">Reminder</th>
     <th :style="styles.thStyle">User</th>
     <th v-if="userRole === 'admin' || userRole === 'super-admin'" :style="styles.thStyle">Actions</th>
   </tr>
 </thead>
 <tbody>
   <tr v-for="task in tasks" :key="task.id">
     <td :style="styles.tdStyle">{{ task.title }}</td>
     <td :style="styles.tdStyle">{{ task.description }}</td>
     <td :style="styles.tdStyle">
<select 
v-model="task.status" 
@change="updateStatus(task)" 
:disabled="(userRole !== 'admin' && userRole !== 'super-admin') && task.status === 'Completed'" 
:style="styles.inputStyle">
<option value="Pending">Pending</option>
<option value="Completed">Completed</option>
</select>
<br>
<span v-if="task.status === 'Completed'">
Completed at:  {{ formatDateTime(task.completed_at) }}
</span>
</td>



     <td :style="styles.tdStyle">{{ task.due_date }}</td>
     <td :style="styles.tdStyle">{{ task.priority }}</td>
     <td :style="styles.tdStyle">{{ task.reminder }}</td>
     <td :style="styles.tdStyle">
<span v-if="userRole !== 'admin' && userRole !== 'super-admin'">
<template v-if="task.assigned_users && task.assigned_users.length > 0">
 <span v-for="(user, index) in task.assigned_users" :key="user.id">
   {{ user.name }}<span v-if="index !== task.assigned_users.length - 1">, </span>
 </span>
</template>
<span v-else>Unassigned</span>
</span>

<div v-if="userRole === 'admin' || userRole === 'super-admin'">
<label v-for="user in users" :key="user.id" :style="{ display: 'block' }">
  <input 
      type="checkbox" 
      :value="user.id" 
      v-model="task.assigned_to" 
      :checked="task.assigned_to === user.id"
    />
 {{ user.name }}
</label>
<button @click="assignTask(task)" :style="styles.buttonStyle">Assign</button>
</div>
</td>



     <td :style="styles.tdStyle">
       <button v-if="userRole === 'admin' || userRole === 'super-admin'" @click="openModal(task)" :style="styles.editButtonStyle">Edit</button>
       <button v-if="userRole === 'admin' || userRole === 'super-admin'" @click="confirmDelete(task.id)" :style="styles.deleteButtonStyle">Delete</button>
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

     <div>
       <label for="reminderToggle">Set Reminder:</label>
       <input type="checkbox" id="reminderToggle" v-model="reminderToggle" />
     </div>

     <div v-if="reminderToggle">
       <label for="reminder">Select Reminder Date:</label>
       <input type="date" v-model="form.reminder" :style="styles.inputStyle">
     </div>

     <div>
       <label for="priority">Set Priority:</label>
       <select id="priority" v-model="form.priority" :style="styles.inputStyle">
         <option value="Low">Low</option>
         <option value="Medium">Medium</option>
         <option value="High">High</option>
       </select>
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
import AdminHeader from '@/components/AdminHeader.vue';
import styles from '@/config/StyleConfig.js';

export default {
components: {
AdminHeader,
},
props: {
userRole: String,  
},

props: ['tasks', 'users', 'userRole'],
data() {
return {
 form: { 
   title: '', 
   description: '', 
   status: 'Pending', 
   due_date: '', 
   priority: 'Low', 
   reminder: '', 
   assigned_to: '' 
 },
 showModal: false,
 editingTask: null,
 styles,
 errorMessage: "",
 dueDateToggle: false,
 reminderToggle: false  
};
},

methods: {
formatDateTime(dateTime) {
 if (!dateTime) return "No time and date";
 return new Date(dateTime).toLocaleString(); 
},
openModal(task) {
 if (task && task.id) {
   this.editingTask = task;
   this.form = { ...task };
   this.dueDateToggle = !!task.due_date;
   this.reminderToggle = !!task.reminder;
 } else {
   this.editingTask = null;
   this.form = { 
     title: '', 
     description: '', 
     status: 'Pending', 
     due_date: '', 
     priority: 'Low', 
     reminder: '', 
     assigned_to: '' 
   };
   this.dueDateToggle = false;
   this.reminderToggle = false;
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

async updateStatus(task) {
try {
const response = await apiClient.put(`/tasks/${task.id}/status`, { status: task.status });

if (task.status === "Completed") {
 task.completed_at = response.data.task.completed_at || new Date().toISOString(); 
} else {
 task.completed_at = null; 
}

alert("Task status updated successfully!");
this.refreshTasks();
} catch (error) {
console.error("❌ Error updating status:", error.response?.data || error.message);
alert(error.response?.data?.message || "Failed to update status. Please try again.");
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

async assignTask(task) {
  try {
    // Ensure assigned_to is always an array
    if (!task.assigned_to) {
      task.assigned_to = [];  // If no user is assigned, send an empty array
    } else if (!Array.isArray(task.assigned_to)) {
      task.assigned_to = [task.assigned_to];  // Convert to array if it's a single value
    }

    // Send the request with assigned_to as an array
    await apiClient.put(`/tasks/${task.id}/assign`, { assigned_to: task.assigned_to });
    this.refreshTasks();
  } catch (error) {
    console.error("Error assigning task:", error.response?.data || error.message);
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
 this.form = { 
   title: '', 
   description: '', 
   status: 'Pending', 
   due_date: '', 
   priority: 'Low', 
   reminder: '', 
   assigned_to: '' 
 };
 this.dueDateToggle = false;
 this.reminderToggle = false;
},

logout() {
 window.location.href = "/login";
}
}

};
</script>

