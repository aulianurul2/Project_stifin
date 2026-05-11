import { Stack } from 'expo-router';

export default function RootLayout() {
  return (
    <Stack
      screenOptions={{
        // Menyembunyikan header default agar tampilan bersih
        headerShown: false,
        animation: 'fade', 
      }}
    >
      {/* 1. Halaman Login */}
      <Stack.Screen 
        name="login" 
        options={{ title: 'Login' }} 
      />

      {/* 2. Halaman Register */}
      <Stack.Screen 
        name="register" 
        options={{ title: 'Register' }} 
      />

      {/* 3. Dashboard (Tabs) */}
      <Stack.Screen 
        name="(tabs)" 
        options={{ 
          headerShown: false,
          gestureEnabled: false, // Mencegah balik ke login pakai swipe
        }} 
      />

      {/* 4. Halaman Form Pendaftaran */}
      {/* Pastikan nama filenya 'form-pendaftaran.tsx' di folder app */}
      <Stack.Screen 
        name="form-pendaftaran" 
        options={{ 
          headerShown: false 
        }} 
      />
    </Stack>
  );
}