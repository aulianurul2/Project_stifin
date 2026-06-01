import { Stack } from 'expo-router';
import Toast, { BaseToast, ErrorToast, ToastConfig } from 'react-native-toast-message';
import { View } from 'react-native';
import { Ionicons } from '@expo/vector-icons';

// Kustomisasi UI Toast agar terlihat Premium, Bersih, dan Profesional
const toastConfig: ToastConfig = {
  success: (props) => (
    <BaseToast
      {...props}
      style={{ borderLeftColor: '#00AA5B', backgroundColor: '#ffffff', height: 70 }}
      contentContainerStyle={{ paddingHorizontal: 15 }}
      text1Style={{ fontSize: 15, fontWeight: '700', color: '#1a1a2e' }}
      text2Style={{ fontSize: 13, color: '#455a64' }}
      renderLeadingIcon={() => (
        <View style={{ justifyContent: 'center', paddingLeft: 15 }}>
          <Ionicons name="checkmark-circle" size={24} color="#00AA5B" />
        </View>
      )}
    />
  ),
  error: (props) => (
    <ErrorToast
      {...props}
      style={{ borderLeftColor: '#d32f2f', backgroundColor: '#ffffff', height: 70 }}
      contentContainerStyle={{ paddingHorizontal: 15 }}
      text1Style={{ fontSize: 15, fontWeight: '700', color: '#1a1a2e' }}
      text2Style={{ fontSize: 13, color: '#455a64' }}
      renderLeadingIcon={() => (
        <View style={{ justifyContent: 'center', paddingLeft: 15 }}>
          <Ionicons name="alert-circle" size={24} color="#d32f2f" />
        </View>
      )}
    />
  )
};

export default function RootLayout() {
  return (
    <>
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
        <Stack.Screen 
          name="form-pendaftaran" 
          options={{ 
            headerShown: false 
          }} 
        />
      </Stack>

      {/* Daftarkan konfigurasi custom di sini */}
      <Toast config={toastConfig} />
    </>
  );
}