import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage'; // Tambahkan import ini

const API_BASE_URL = process.env.EXPO_PUBLIC_API_URL;

const axiosInstance = axios.create({
  baseURL: API_BASE_URL,
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Interceptor untuk menyisipkan Token secara otomatis
axiosInstance.interceptors.request.use(
  async (config) => {
    try {
      // Ambil token yang disimpan saat login/register
      const token = await AsyncStorage.getItem('user_token'); 
      
      if (token) {
        // Sisipkan ke Header Authorization
        config.headers.Authorization = `Bearer ${token}`;
      }
    } catch (error) {
      console.error("Gagal mengambil token dari storage", error);
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

export default axiosInstance;