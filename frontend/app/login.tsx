import React, { useState } from 'react';
import { 
  View, Text, TextInput, TouchableOpacity, StyleSheet, 
  SafeAreaView, Alert, ActivityIndicator, KeyboardAvoidingView, Platform, ScrollView 
} from 'react-native';
import { useRouter } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';
import axios from 'axios';
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
        
        // SIMPAN NAMA KE MEMORI HP
        await AsyncStorage.setItem('user_name', userNama);
        await AsyncStorage.setItem('user_phone', userHp);
        
        // Pindah ke Dashboard
        router.replace('/(tabs)');
      } else {
        Alert.alert("Gagal", response.data.message);
      }
    } catch (error: any) {
      Alert.alert("Login Gagal", "Username atau password salah.");
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView style={styles.container}>
      <KeyboardAvoidingView behavior={Platform.OS === 'ios' ? 'padding' : 'height'} style={{ flex: 1 }}>
        <ScrollView contentContainerStyle={styles.scrollContainer} bounces={false}>
          <View style={styles.loginCard}>
            <View style={styles.headerSection}>
              <View style={styles.logoCircle}><Text style={styles.logoIcon}>⚡</Text></View>
              <Text style={styles.title}>Sign In STIFIn</Text>
              <Text style={styles.subtitle}>Selamat datang kembali!</Text>
            </View>

            <View style={styles.formSection}>
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
                    <Ionicons name={showPassword ? "eye-off-outline" : "eye-outline"} size={20} color="#3b82f6" />
                  </TouchableOpacity>
                </View>
              </View>

              <TouchableOpacity style={styles.primaryBtn} onPress={handleLogin} disabled={loading}>
                {loading ? <ActivityIndicator color="#ffffff" /> : <Text style={styles.btnText}>Masuk Sekarang</Text>}
              </TouchableOpacity>
            </View>
          </View>
        </ScrollView>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#0f172a' },
  scrollContainer: { flexGrow: 1, justifyContent: 'center', padding: 24 },
  loginCard: { backgroundColor: '#1e293b', borderRadius: 28, padding: 32, borderWidth: 1, borderColor: 'rgba(255, 255, 255, 0.05)', elevation: 8 },
  headerSection: { alignItems: 'center', marginBottom: 32 },
  logoCircle: { width: 70, height: 70, borderRadius: 35, backgroundColor: 'rgba(59, 130, 246, 0.1)', justifyContent: 'center', alignItems: 'center', marginBottom: 16 },
  logoIcon: { fontSize: 36 },
  title: { fontSize: 26, fontWeight: '800', color: '#ffffff' },
  subtitle: { color: '#94a3b8', fontSize: 15, marginTop: 6 },
  formSection: { width: '100%' },
  inputGroup: { marginBottom: 20 },
  inputLabel: { color: '#94a3b8', fontSize: 13, fontWeight: '600', marginBottom: 8, textTransform: 'uppercase', letterSpacing: 1 },
  inputWrapper: { flexDirection: 'row', alignItems: 'center', backgroundColor: 'rgba(15, 23, 42, 0.5)', borderRadius: 14, borderWidth: 1, borderColor: 'rgba(255, 255, 255, 0.1)', paddingHorizontal: 16 },
  fieldIcon: { marginRight: 12 },
  textInput: { flex: 1, color: '#ffffff', paddingVertical: 14, fontSize: 15 },
  eyeBtn: { padding: 8 },
  primaryBtn: { backgroundColor: '#3b82f6', paddingVertical: 16, borderRadius: 14, alignItems: 'center', marginTop: 10 },
  btnText: { color: '#ffffff', fontWeight: '700', fontSize: 16 },
});