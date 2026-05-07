import React, { useState, type ComponentProps } from 'react';
import { 
  View, 
  Text, 
  TextInput, 
  TouchableOpacity, 
  StyleSheet, 
  ScrollView, 
  SafeAreaView, 
  Alert, 
  ActivityIndicator, 
  KeyboardAvoidingView, 
  Platform 
} from 'react-native';
import { useRouter } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';
import axios from 'axios';

// --- Definisi Interface untuk Props agar TypeScript tidak Error ---
interface InputBoxProps {
  label: string;
  icon: ComponentProps<typeof Ionicons>['name'];
  placeholder: string;
  value?: string;
  onChangeText: (text: string) => void;
  secureTextEntry?: boolean;
  keyboardType?: 'default' | 'numeric' | 'email-address';
  multiline?: boolean;
}

export default function RegisterScreen() {
  const router = useRouter();
  const [loading, setLoading] = useState(false);
  const [form, setForm] = useState({
    nama: '',
    username: '',
    password: '',
    tanggal_lahir: '',
    jenis_kelamin: '', 
    no_hp: '',
    alamat: ''
  });

  // Fungsi mengatur format tanggal DD/MM/YYYY otomatis
  const handleDateChange = (text: string) => {
    let cleaned = text.replace(/\D/g, '');
    let formatted = cleaned;
    if (cleaned.length > 2) formatted = `${cleaned.slice(0, 2)}/${cleaned.slice(2, 4)}`;
    if (cleaned.length > 4) formatted = `${cleaned.slice(0, 2)}/${cleaned.slice(2, 4)}/${cleaned.slice(4, 8)}`;
    setForm({ ...form, tanggal_lahir: formatted });
  };

  const handleRegister = async () => {
    if (!form.nama || !form.username || !form.password || !form.tanggal_lahir || !form.jenis_kelamin) {
      Alert.alert("Perhatian", "Mohon lengkapi semua data wajib.");
      return;
    }

    setLoading(true);
    try {
      // Konversi DD/MM/YYYY menjadi YYYY-MM-DD untuk Laravel/MySQL
      const parts = form.tanggal_lahir.split('/');
      const formattedDate = `${parts[2]}-${parts[1]}-${parts[0]}`;

      const response = await axios.post('http://192.168.100.117:8000/api/addnew', {
        ...form,
        tanggal_lahir: formattedDate
      });

      Alert.alert("Sukses", response.data.message);
      router.push('/login');
    } catch (error: any) {
      const msg = error.response?.data?.message || "Gagal terhubung ke server backend";
      Alert.alert("Pendaftaran Gagal", msg);
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView style={styles.container}>
      <KeyboardAvoidingView behavior={Platform.OS === 'ios' ? 'padding' : 'height'} style={{flex: 1}}>
        <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
          <View style={styles.card}>
            <View style={styles.header}>
              <View style={styles.logoCircle}><Text style={styles.logoIcon}>⚡</Text></View>
              <Text style={styles.title}>Sign Up STIFIn</Text>
              <Text style={styles.subtitle}>Lengkapi data diri Anda</Text>
            </View>

            <View style={styles.form}>
              <InputBox label="Nama Lengkap" icon="person-outline" placeholder="Nama sesuai identitas" onChangeText={(v: string) => setForm({...form, nama: v})} />
              <InputBox label="Username" icon="at-outline" placeholder="Buat username unik" onChangeText={(v: string) => setForm({...form, username: v})} />
              <InputBox label="Password" icon="lock-closed-outline" placeholder="Minimal 6 karakter" secureTextEntry onChangeText={(v: string) => setForm({...form, password: v})} />
              
              <View style={styles.inputGroupOuter}>
                <Text style={styles.inputLabel}>Tanggal Lahir (DD/MM/YYYY)</Text>
                <View style={styles.inputWrapper}>
                  <Ionicons name="calendar-outline" size={18} color="#64748b" style={styles.fieldIcon} />
                  <TextInput 
                    style={styles.textInput} 
                    placeholder="Contoh: 17/08/1945" 
                    value={form.tanggal_lahir} 
                    onChangeText={handleDateChange} 
                    keyboardType="numeric" 
                    maxLength={10} 
                    placeholderTextColor="#64748b" 
                  />
                </View>
              </View>

              <View style={styles.inputGroupOuter}>
                <Text style={styles.inputLabel}>Jenis Kelamin</Text>
                <View style={styles.genderContainer}>
                  <TouchableOpacity style={[styles.genderBox, form.jenis_kelamin === 'L' && styles.genderBoxActive]} onPress={() => setForm({...form, jenis_kelamin: 'L'})}>
                    <Ionicons name="male" size={24} color={form.jenis_kelamin === 'L' ? '#fff' : '#64748b'} />
                    <Text style={[styles.genderText, form.jenis_kelamin === 'L' && styles.genderTextActive]}>Laki-laki</Text>
                  </TouchableOpacity>
                  <TouchableOpacity style={[styles.genderBox, form.jenis_kelamin === 'P' && styles.genderBoxActive]} onPress={() => setForm({...form, jenis_kelamin: 'P'})}>
                    <Ionicons name="female" size={24} color={form.jenis_kelamin === 'P' ? '#fff' : '#64748b'} />
                    <Text style={[styles.genderText, form.jenis_kelamin === 'P' && styles.genderTextActive]}>Perempuan</Text>
                  </TouchableOpacity>
                </View>
              </View>

              <InputBox label="Nomor HP" icon="call-outline" placeholder="8123456..." keyboardType="numeric" onChangeText={(v: string) => setForm({...form, no_hp: v})} />
              <InputBox label="Alamat Lengkap" icon="location-outline" placeholder="Alamat tinggal saat ini" multiline onChangeText={(v: string) => setForm({...form, alamat: v})} />

              <TouchableOpacity style={styles.primaryBtn} onPress={handleRegister} disabled={loading}>
                {loading ? <ActivityIndicator color="#fff" /> : <Text style={styles.btnText}>Daftar Sekarang</Text>}
              </TouchableOpacity>

              <View style={styles.footer}>
                <Text style={styles.footerText}>Sudah punya akun? </Text>
                <TouchableOpacity onPress={() => router.push('/login')}><Text style={styles.linkText}>Login di sini</Text></TouchableOpacity>
              </View>
            </View>
          </View>
        </ScrollView>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}

// --- Komponen InputBox yang sudah di-fix TypeScript-nya ---
const InputBox = ({ label, icon, ...props }: InputBoxProps) => (
  <View style={styles.inputGroupOuter}>
    <Text style={styles.inputLabel}>{label}</Text>
    <View style={styles.inputWrapper}>
      <Ionicons name={icon} size={18} color="#64748b" style={styles.fieldIcon} />
      <TextInput 
        style={styles.textInput} 
        placeholderTextColor="#64748b" 
        {...props} 
      />
    </View>
  </View>
);

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#0f172a' },
  scrollContent: { padding: 24, flexGrow: 1, justifyContent: 'center' },
  card: { backgroundColor: '#1e293b', borderRadius: 28, padding: 25, borderWidth: 1, borderColor: 'rgba(255, 255, 255, 0.05)', elevation: 8 },
  header: { alignItems: 'center', marginBottom: 25 },
  logoCircle: { width: 60, height: 60, borderRadius: 30, backgroundColor: 'rgba(59, 130, 246, 0.1)', justifyContent: 'center', alignItems: 'center', marginBottom: 15 },
  logoIcon: { fontSize: 30 },
  title: { fontSize: 24, fontWeight: '800', color: '#fff' },
  subtitle: { color: '#94a3b8', fontSize: 14, marginTop: 4 },
  form: { width: '100%' },
  inputGroupOuter: { marginBottom: 18 },
  inputLabel: { color: '#94a3b8', fontSize: 12, fontWeight: '600', marginBottom: 8, marginLeft: 4, textTransform: 'uppercase' },
  inputWrapper: { flexDirection: 'row', alignItems: 'center', backgroundColor: '#0f172a', borderRadius: 14, borderWidth: 1, borderColor: 'rgba(255, 255, 255, 0.1)', paddingHorizontal: 15 },
  fieldIcon: { marginRight: 12 },
  textInput: { flex: 1, color: '#fff', paddingVertical: 12, fontSize: 15 },
  genderContainer: { flexDirection: 'row', justifyContent: 'space-between' },
  genderBox: { flex: 0.48, flexDirection: 'row', backgroundColor: '#0f172a', borderRadius: 14, padding: 12, alignItems: 'center', justifyContent: 'center', borderWidth: 1, borderColor: 'rgba(255, 255, 255, 0.1)' },
  genderBoxActive: { backgroundColor: '#3b82f6', borderColor: '#3b82f6' },
  genderText: { color: '#64748b', marginLeft: 8, fontWeight: '600' },
  genderTextActive: { color: '#fff' },
  primaryBtn: { backgroundColor: '#3b82f6', paddingVertical: 16, borderRadius: 14, alignItems: 'center', marginTop: 20 },
  btnText: { color: '#fff', fontWeight: 'bold', fontSize: 16 },
  footer: { flexDirection: 'row', justifyContent: 'center', marginTop: 20 },
  footerText: { color: '#94a3b8' },
  linkText: { color: '#3b82f6', fontWeight: 'bold' }
});