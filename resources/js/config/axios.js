import axios from 'axios';

const apiClient = axios.create({
    baseURL: window.location.origin + '/api',
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    }
});

export default apiClient;
