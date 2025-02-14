<template>
    <div>
      <AdminHeader />
      <h1>Task Management</h1>
      <table border="1">
        <thead>
          <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Assigned Users</th>
            <th>Completed By</th>
            <th>Pending Users</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="task in tasks" :key="task.id">
            <td>{{ task.title }}</td>
            <td>{{ task.description }}</td>
            <td>
              <template v-if="task.assigned_users && task.assigned_users.length > 0">
                <span v-for="(user, index) in task.assigned_users" :key="user.id">
                  {{ user.name }}<span v-if="index !== task.assigned_users.length - 1">, </span>
                </span>
              </template>
              <span v-else>No users assigned</span>
            </td>
            <td>
              <template v-if="task.completed_users && task.completed_users.length > 0">
                <div v-for="user in task.completed_users" :key="user.id">
                  ✅ {{ user.name }} - Completed at: {{ formatDateTime(user.pivot.completed_at) }}
                </div>
              </template>
              <span v-else>❌ No user has completed this task</span>
            </td>
            <td>
              <template v-if="task.assigned_users && task.completed_users">
                <div v-for="user in getPendingUsers(task.assigned_users, task.completed_users)" :key="user.id">
                  ⏳ {{ user.name }}
                </div>
              </template>
              <span v-if="!task.assigned_users || task.assigned_users.length === 0">No users assigned</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </template>
  
  <script>
  import AdminHeader from '@/components/AdminHeader.vue';
  
  export default {
    components: {
      AdminHeader,
    },
    props: {
      tasks: Array
    },
    methods: {
      formatDateTime(dateTime) {
        if (!dateTime) return "No time recorded";
        return new Date(dateTime).toLocaleString();
      },
      getPendingUsers(assignedUsers, completedUsers) {
        const completedUserIds = new Set(completedUsers.map(user => user.id));
        return assignedUsers.filter(user => !completedUserIds.has(user.id));
      }
    }
  };
  </script>
  