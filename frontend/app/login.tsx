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
      const token = response.data.token;

      await AsyncStorage.setItem('user_token', token);
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

          {/* Top Green Hero Section */}
          <View style={styles.heroSection}>
            <View style={styles.logoWrap}>
              <View style={styles.logoCircleOuter}>
                <View style={styles.logoCircleInner}>
                  <Text style={styles.logoEmoji}>⚡</Text>
                </View>
              </View>
            </View>
            <Text style={styles.heroTitle}>STIFIn</Text>
            <Text style={styles.heroSubtitle}>Information System</Text>
          </View>

          {/* White Card Form */}
          <View style={styles.formCard}>
            <Text style={styles.cardTitle}>Masuk ke Akun</Text>
            <Text style={styles.cardSubtitle}>Selamat datang kembali!</Text>

            {/* Username Field */}
            <View style={styles.fieldGroup}>
              <Text style={styles.fieldLabel}>Username</Text>
              <View style={styles.inputRow}>
                <View style={styles.iconBox}>
                  <Ionicons name="person-outline" size={18} color="#00AA5B" />
                </View>
                <TextInput
                  style={styles.textInput}
                  value={username}
                  onChangeText={setUsername}
                  placeholder="Masukkan username"
                  placeholderTextColor="#b0bec5"
                  autoCapitalize="none"
                />
              </View>
            </View>

            {/* Password Field */}
            <View style={styles.fieldGroup}>
              <Text style={styles.fieldLabel}>Password</Text>
              <View style={styles.inputRow}>
                <View style={styles.iconBox}>
                  <Ionicons name="lock-closed-outline" size={18} color="#00AA5B" />
                </View>
                <TextInput
                  style={styles.textInput}
                  value={password}
                  onChangeText={setPassword}
                  secureTextEntry={!showPassword}
                  placeholder="Masukkan password"
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

            {/* Login Button */}
            <TouchableOpacity 
              style={styles.primaryBtn} 
              onPress={handleLogin} 
              disabled={loading}
              activeOpacity={0.85}
            >
              {loading ? (
                <ActivityIndicator color="#ffffff" />
              ) : (
                <Text style={styles.btnText}>Masuk Sekarang</Text>
              )}
            </TouchableOpacity>

            {/* Divider */}
            <View style={styles.dividerRow}>
              <View style={styles.dividerLine} />
              <Text style={styles.dividerText}>atau</Text>
              <View style={styles.dividerLine} />
            </View>

            {/* Register Link */}
            <View style={styles.footerSection}>
              <Text style={styles.footerText}>Belum punya akun? </Text>
              <TouchableOpacity onPress={() => router.push('/register')}>
                <Text style={styles.linkText}>Daftar di sini</Text>
              </TouchableOpacity>
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
    backgroundColor: '#00AA5B',
  },
  scrollContainer: { 
    flexGrow: 1,
  },
  heroSection: {
    alignItems: 'center',
    paddingTop: 60,
    paddingBottom: 40,
    backgroundColor: '#00AA5B',
  },
  logoWrap: {
    marginBottom: 16,
  },
  logoCircleOuter: {
    width: 90,
    height: 90,
    borderRadius: 45,
    backgroundColor: 'rgba(255,255,255,0.2)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  logoCircleInner: {
    width: 68,
    height: 68,
    borderRadius: 34,
    backgroundColor: '#fff',
    justifyContent: 'center',
    alignItems: 'center',
  },
  logoEmoji: {
    fontSize: 34,
  },
  heroTitle: {
    fontSize: 32,
    fontWeight: '900',
    color: '#fff',
    letterSpacing: 2,
  },
  heroSubtitle: {
    fontSize: 14,
    color: 'rgba(255,255,255,0.8)',
    marginTop: 4,
    letterSpacing: 1,
  },
  formCard: {
    flex: 1,
    backgroundColor: '#fff',
    borderTopLeftRadius: 32,
    borderTopRightRadius: 32,
    padding: 32,
    paddingBottom: 48,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: -4 },
    shadowOpacity: 0.08,
    shadowRadius: 12,
    elevation: 10,
  },
  cardTitle: {
    fontSize: 24,
    fontWeight: '800',
    color: '#1a1a2e',
    marginBottom: 4,
  },
  cardSubtitle: {
    fontSize: 14,
    color: '#90a4ae',
    marginBottom: 28,
  },
  fieldGroup: {
    marginBottom: 18,
  },
  fieldLabel: {
    fontSize: 12,
    fontWeight: '700',
    color: '#37474f',
    marginBottom: 8,
    textTransform: 'uppercase',
    letterSpacing: 0.8,
  },
  inputRow: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#f5f9f7',
    borderRadius: 14,
    borderWidth: 1.5,
    borderColor: '#e0f2ec',
  },
  iconBox: {
    width: 44,
    height: 48,
    justifyContent: 'center',
    alignItems: 'center',
  },
  textInput: {
    flex: 1,
    color: '#1a1a2e',
    paddingVertical: 14,
    fontSize: 15,
  },
  eyeBtn: {
    padding: 12,
  },
  primaryBtn: {
    backgroundColor: '#00AA5B',
    paddingVertical: 16,
    borderRadius: 14,
    alignItems: 'center',
    marginTop: 8,
    shadowColor: '#00AA5B',
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.35,
    shadowRadius: 10,
    elevation: 6,
  },
  btnText: {
    color: '#ffffff',
    fontWeight: '800',
    fontSize: 16,
    letterSpacing: 0.5,
  },
  dividerRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginVertical: 20,
  },
  dividerLine: {
    flex: 1,
    height: 1,
    backgroundColor: '#e8f5e9',
  },
  dividerText: {
    color: '#b0bec5',
    fontSize: 13,
    marginHorizontal: 12,
  },
  footerSection: {
    flexDirection: 'row',
    justifyContent: 'center',
  },
  footerText: {
    color: '#90a4ae',
    fontSize: 14,
  },
  linkText: {
    color: '#00AA5B',
    fontWeight: '800',
    fontSize: 14,
  },
});