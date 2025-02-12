import axios from "axios";

const apiClient = axios.create({
  baseURL: "http://127.0.0.1:8000/api",
  withCredentials: true, // This ensures cookies (session data) are sent with every request
  headers: {
    "Content-Type": "application/json",
    "Accept": "application/json"
  }
});

export default apiClient;
