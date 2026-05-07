import { Stack } from 'expo-router';

export default function RootLayout() {
  return (
    <Stack
      screenOptions={{
        // Menyembunyikan header default agar tampilan 
        // Login & Register bersih sesuai desain STIFIn
        headerShown: false,
        animation: 'fade', // Memberikan efek transisi yang halus
      }}
    >
      {/* 1. Halaman Login */}
      <Stack.Screen 
        name="login" 
        options={{ 
          title: 'Login',
        }} 
      />

      {/* 2. Halaman Register */}
      <Stack.Screen 
        name="register" 
        options={{ 
          title: 'Register',
        }} 
      />

      {/* 3. Folder (tabs) - Ini adalah Dashboard setelah Login */}
      <Stack.Screen 
        name="(tabs)" 
        options={{ 
          headerShown: false,
          // Mencegah user kembali ke Login dengan tombol back 
          // setelah berhasil masuk ke Dashboard
          gestureEnabled: false, 
        }} 
      />
      <Stack>
  <Stack.Screen name="(tabs)" options={{ headerShown: false }} />
  <Stack.Screen name="login" options={{ headerShown: false }} />
  <Stack.Screen 
    name="form-pendaftaran" 
    options={{ 
      title: 'Formulir Pendaftaran',
      headerShown: false // Atau true jika ingin header bawaan Expo
    }} 
  />
</Stack>
    </Stack>
  );
}