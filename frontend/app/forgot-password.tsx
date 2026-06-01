import React, { useState } from 'react';
import { 
  View, 
  Text, 
  TextInput, 
  TouchableOpacity, 
  StyleSheet, 
  SafeAreaView, 
  ActivityIndicator, 
  KeyboardAvoidingView, 
  Platform, 
  ScrollView 
} from 'react-native';
import { useRouter } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';
import axiosInstance from '@/src/api/axiosConfig';
import Toast from 'react-native-toast-message'; // Import Toast

export default function ForgotPasswordScreen() {
  const router = useRouter();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [loading, setLoading] = useState(false);

  const handleResetPassword = async () => {
    if (!email || !password || !confirmPassword) {
      Toast.show({
        type: 'error',
        text1: 'Perhatian',
        text2: 'Semua kolom wajib diisi.',
        position: 'top'
      });
      return;
    }

    if (password !== confirmPassword) {
      Toast.show({
        type: 'error',
        text1: 'Gagal',
        text2: 'Konfirmasi password tidak cocok.',
        position: 'top'
      });
      return;
    }

    setLoading(true);
    try {
      const response = await axiosInstance.post('/forgot-password', {
        email: email,
        password: password,
        password_confirmation: confirmPassword
      });

      if (response.data.success) {
        // Tampilkan Toast Sukses
        Toast.show({
          type: 'success',
          text1: 'Berhasil',
          text2: 'Password Anda berhasil diperbarui!',
          position: 'top',
          visibilityTime: 3000,
        });

        // Berikan sedikit delay 1.5 detik agar user sempat membaca Toast sebelum pindah ke Login
        setTimeout(() => {
          router.replace('/login');
        }, 1500);

      } else {
        Toast.show({
          type: 'error',
          text1: 'Gagal',
          text2: response.data.message || 'Terjadi kesalahan.',
          position: 'top'
        });
      }
    } catch (error: any) {
      console.error(error);
      const msg = error.response?.data?.message || "Terjadi kesalahan koneksi ke server.";
      Toast.show({
        type: 'error',
        text1: 'Gagal',
        text2: msg,
        position: 'top'
      });
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView style={styles.container}>
      <KeyboardAvoidingView 
        behavior={Platform.OS === 'ios' ? 'padding' : 'height'} 
        style={{ flex: 1 }}
      >
        <ScrollView contentContainerStyle={styles.scrollContainer} bounces={false}>
          
          <View style={styles.heroSection}>
            <TouchableOpacity style={styles.backBtn} onPress={() => router.back()}>
              <Ionicons name="arrow-back" size={24} color="#fff" />
            </TouchableOpacity>
            <Text style={styles.heroTitle}>Reset Password</Text>
            <Text style={styles.heroSubtitle}>Masukkan Email & Password Baru Anda</Text>
          </View>

          <View style={styles.formCard}>
            {/* Email Field */}
            <View style={styles.fieldGroup}>
              <Text style={styles.fieldLabel}>Email Terdaftar</Text>
              <View style={styles.inputRow}>
                <View style={styles.iconBox}>
                  <Ionicons name="mail-outline" size={18} color="#00AA5B" />
                </View>
                <TextInput
                  style={styles.textInput}
                  value={email}
                  onChangeText={setEmail}
                  placeholder="Masukkan email Anda"
                  placeholderTextColor="#b0bec5"
                  keyboardType="email-address"
                  autoCapitalize="none"
                />
              </View>
            </View>

            {/* Password Field */}
            <View style={styles.fieldGroup}>
              <Text style={styles.fieldLabel}>Password Baru</Text>
              <View style={styles.inputRow}>
                <View style={styles.iconBox}>
                  <Ionicons name="lock-closed-outline" size={18} color="#00AA5B" />
                </View>
                <TextInput
                  style={styles.textInput}
                  value={password}
                  onChangeText={setPassword}
                  secureTextEntry={!showPassword}
                  placeholder="Minimal 6 karakter"
                  placeholderTextColor="#b0bec5"
                />
              </View>
            </View>

            {/* Confirm Password Field */}
            <View style={styles.fieldGroup}>
              <Text style={styles.fieldLabel}>Konfirmasi Password Baru</Text>
              <View style={styles.inputRow}>
                <View style={styles.iconBox}>
                  <Ionicons name="checkmark-done-outline" size={18} color="#00AA5B" />
                </View>
                <TextInput
                  style={styles.textInput}
                  value={confirmPassword}
                  onChangeText={setConfirmPassword}
                  secureTextEntry={!showPassword}
                  placeholder="Ulangi password baru"
                  placeholderTextColor="#b0bec5"
                />
                <TouchableOpacity onPress={() => setShowPassword(!showPassword)} style={styles.eyeBtn}>
                  <Ionicons 
                    name={showPassword ? "eye-off-outline" : "eye-outline"} 
                    size={20} 
                    color="#00AA5B" 
                  />
                </TouchableOpacity>
              </View>
            </View>

            {/* Submit Button */}
            <TouchableOpacity 
              style={styles.primaryBtn} 
              onPress={handleResetPassword} 
              disabled={loading}
            >
              {loading ? (
                <ActivityIndicator color="#ffffff" />
              ) : (
                <Text style={styles.btnText}>Perbarui Password</Text>
              )}
            </TouchableOpacity>
          </View>

        </ScrollView>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#00AA5B' },
  scrollContainer: { flexGrow: 1 },
  heroSection: { paddingHorizontal: 24, paddingTop: 40, paddingBottom: 30 },
  backBtn: { width: 40, height: 40, justifyContent: 'center', marginBottom: 10 },
  heroTitle: { fontSize: 28, fontWeight: '900', color: '#fff' },
  heroSubtitle: { fontSize: 14, color: 'rgba(255,255,255,0.8)', marginTop: 4 },
  formCard: { 
    flex: 1, 
    backgroundColor: '#fff', 
    borderTopLeftRadius: 32, 
    borderTopRightRadius: 32, 
    padding: 32,
    elevation: 10 
  },
  fieldGroup: { marginBottom: 20 },
  fieldLabel: { fontSize: 12, fontWeight: '700', color: '#37474f', marginBottom: 8, textTransform: 'uppercase' },
  inputRow: { flexDirection: 'row', alignItems: 'center', backgroundColor: '#f5f9f7', borderRadius: 14, borderWidth: 1.5, borderColor: '#e0f2ec' },
  iconBox: { width: 44, height: 48, justifyContent: 'center', alignItems: 'center' },
  textInput: { flex: 1, color: '#1a1a2e', paddingVertical: 14, fontSize: 15 },
  eyeBtn: { padding: 12 },
  primaryBtn: { 
    backgroundColor: '#00AA5B', 
    paddingVertical: 16, 
    borderRadius: 14, 
    alignItems: 'center', 
    marginTop: 15,
    shadowColor: '#00AA5B',
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.35,
    shadowRadius: 10,
    elevation: 6
  },
  btnText: { color: '#ffffff', fontWeight: '800', fontSize: 16 }
});