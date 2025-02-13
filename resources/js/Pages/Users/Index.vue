<template>
    <div>
      <!-- Admin Header -->
      <AdminHeader />
  
      <h1>User List</h1>
  
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Tasks</th> <!-- Added a new column for tasks -->
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users" :key="user.id">
            <td>{{ user.id }}</td>
            <td>{{ user.name }}</td>
            <td>{{ user.email }}</td>
            <td>
              <ul v-if="user.tasks?.length">
                <li v-for="task in user.tasks" :key="task.id">
                  {{ task.title }}
                </li>
              </ul>
              <span v-else>No Tasks Assigned</span>
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
      AdminHeader
    },
    props: {
      users: {
        type: Array,
        default: () => [] // Ensures no errors if users are not passed
      }
    }
  };
  </script>
  
  <style scoped>
  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
  }
  
  th, td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
  }
  
  th {
    background-color: #f4f4f4;
  }
  
  ul {
    padding-left: 0;
  }
  
  li {
    list-style-type: none;
    background: #eef;
    padding: 5px;
    margin: 5px 0;
    border-radius: 5px;
  }
  </style>
  