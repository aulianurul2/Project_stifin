import React, { useState } from 'react';
import { 
  View, 
  Text, 
  TextInput, 
  TouchableOpacity, 
  StyleSheet, 
  SafeAreaView, 
  Alert, 
  ActivityIndicator, 
  KeyboardAvoidingView, 
  Platform, 
  ScrollView 
} from 'react-native';
import { useRouter } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import axiosInstance from '@/src/api/axiosConfig';

export default function LoginScreen() {
  const router = useRouter();
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [loading, setLoading] = useState(false);

// Di dalam login.tsx, ubah fungsi handleLogin menjadi seperti ini:

const handleLogin = async () => {
  if (!username || !password) {
    Alert.alert("Perhatian", "Silakan masukkan username dan password Anda.");
    return;
  }

  setLoading(true);
  try {
    const response = await axiosInstance.post('/login', {
      username,
      password,
    });

    if (response.data.success) {
      const userNama = response.data.user.nama;
      const userHp = response.data.user.no_hp;
      const token = response.data.token; // 1. Ambil token dari response

      // SIMPAN DATA KE MEMORI HP (AsyncStorage)
      await AsyncStorage.setItem('user_token', token); // 2. SIMPAN TOKEN INI (PENTING!)
      await AsyncStorage.setItem('user_name', userNama);
      await AsyncStorage.setItem('user_phone', userHp || "");

      router.replace('/(tabs)');
    } else {
      Alert.alert("Gagal", response.data.message || "Username atau password salah.");
    }
  } catch (error: any) {
    console.error(error);
    Alert.alert("Login Gagal", "Terjadi kesalahan koneksi ke server.");
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
          <View style={styles.loginCard}>
            
            {/* Header Section */}
            <View style={styles.headerSection}>
              <View style={styles.logoCircle}>
                <Text style={styles.logoIcon}>⚡</Text>
              </View>
              <Text style={styles.title}>Sign In STIFIn</Text>
              <Text style={styles.subtitle}>Selamat datang kembali!</Text>
            </View>

            {/* Form Section */}
            <View style={styles.formSection}>
              
              {/* Input Username */}
              <View style={styles.inputGroup}>
                <Text style={styles.inputLabel}>Username</Text>
                <View style={styles.inputWrapper}>
                  <Ionicons name="person-outline" size={18} color="#64748b" style={styles.fieldIcon} />
                  <TextInput
                    style={styles.textInput}
                    value={username}
                    onChangeText={setUsername}
                    placeholder="Masukkan username"
                    placeholderTextColor="#475569"
                    autoCapitalize="none"
                  />
                </View>
              </View>

              {/* Input Password */}
              <View style={styles.inputGroup}>
                <Text style={styles.inputLabel}>Password</Text>
                <View style={styles.inputWrapper}>
                  <Ionicons name="lock-closed-outline" size={18} color="#64748b" style={styles.fieldIcon} />
                  <TextInput
                    style={styles.textInput}
                    value={password}
                    onChangeText={setPassword}
                    secureTextEntry={!showPassword}
                    placeholder="Masukkan password"
                    placeholderTextColor="#475569"
                  />
                  <TouchableOpacity onPress={() => setShowPassword(!showPassword)} style={styles.eyeBtn}>
                    <Ionicons 
                      name={showPassword ? "eye-off-outline" : "eye-outline"} 
                      size={20} 
                      color="#3b82f6" 
                    />
                  </TouchableOpacity>
                </View>
              </View>

              {/* Tombol Login */}
              <TouchableOpacity 
                style={styles.primaryBtn} 
                onPress={handleLogin} 
                disabled={loading}
              >
                {loading ? (
                  <ActivityIndicator color="#ffffff" />
                ) : (
                  <Text style={styles.btnText}>Masuk Sekarang</Text>
                )}
              </TouchableOpacity>

              {/* Footer Section (NAVIGASI REGISTER) */}
              <View style={styles.footerSection}>
                <Text style={styles.footerText}>Belum punya akun? </Text>
                <TouchableOpacity onPress={() => router.push('/register')}>
                  <Text style={styles.linkText}>Daftar di sini</Text>
                </TouchableOpacity>
              </View>

            </View>
          </View>
        </ScrollView>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { 
    flex: 1, 
    backgroundColor: '#0f172a' 
  },
  scrollContainer: { 
    flexGrow: 1, 
    justifyContent: 'center', 
    padding: 24 
  },
  loginCard: { 
    backgroundColor: '#1e293b', 
    borderRadius: 28, 
    padding: 32, 
    borderWidth: 1, 
    borderColor: 'rgba(255, 255, 255, 0.05)', 
    elevation: 8 
  },
  headerSection: { 
    alignItems: 'center', 
    marginBottom: 32 
  },
  logoCircle: { 
    width: 70, 
    height: 70, 
    borderRadius: 35, 
    backgroundColor: 'rgba(59, 130, 246, 0.1)', 
    justifyContent: 'center', 
    alignItems: 'center', 
    marginBottom: 16 
  },
  logoIcon: { 
    fontSize: 36 
  },
  title: { 
    fontSize: 26, 
    fontWeight: '800', 
    color: '#ffffff' 
  },
  subtitle: { 
    color: '#94a3b8', 
    fontSize: 15, 
    marginTop: 6 
  },
  formSection: { 
    width: '100%' 
  },
  inputGroup: { 
    marginBottom: 20 
  },
  inputLabel: { 
    color: '#94a3b8', 
    fontSize: 12, 
    fontWeight: '600', 
    marginBottom: 8, 
    textTransform: 'uppercase', 
    letterSpacing: 1 
  },
  inputWrapper: { 
    flexDirection: 'row', 
    alignItems: 'center', 
    backgroundColor: 'rgba(15, 23, 42, 0.5)', 
    borderRadius: 14, 
    borderWidth: 1, 
    borderColor: 'rgba(255, 255, 255, 0.1)', 
    paddingHorizontal: 16 
  },
  fieldIcon: { 
    marginRight: 12 
  },
  textInput: { 
    flex: 1, 
    color: '#ffffff', 
    paddingVertical: 14, 
    fontSize: 15 
  },
  eyeBtn: { 
    padding: 8 
  },
  primaryBtn: { 
    backgroundColor: '#3b82f6', 
    paddingVertical: 16, 
    borderRadius: 14, 
    alignItems: 'center', 
    marginTop: 10 
  },
  btnText: { 
    color: '#ffffff', 
    fontWeight: '700', 
    fontSize: 16 
  },
  footerSection: { 
    flexDirection: 'row', 
    justifyContent: 'center', 
    marginTop: 24 
  },
  footerText: { 
    color: '#94a3b8', 
    fontSize: 14 
  },
  linkText: { 
    color: '#3b82f6', 
    fontWeight: '700', 
    fontSize: 14 
  },
});