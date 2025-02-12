import apiClient from '@/config/axios.js';

export async function getUser() {
    try {
        const response = await apiClient.get('/user'); 
        return response.data;
    } catch (error) {
        console.error("Error fetching user:", error);
        return null;
    }
}
