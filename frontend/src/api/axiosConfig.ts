import axios from 'axios';

// Mengambil URL dari file .env
// Expo secara otomatis memuat variabel EXPO_PUBLIC_
const API_BASE_URL = process.env.EXPO_PUBLIC_API_URL;

const axiosInstance = axios.create({
  baseURL: API_BASE_URL,
  timeout: 10000, // Memberikan batas waktu 10 detik jika server lambat
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Kamu juga bisa menambahkan interceptor di sini jika nanti butuh Token/Auth
axiosInstance.interceptors.request.use(
  async (config) => {
    // Contoh: Jika butuh kirim Token otomatis di masa depan
    // const token = await AsyncStorage.getItem('user_token');
    // if (token) config.headers.Authorization = `Bearer ${token}`;
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

export default axiosInstance;