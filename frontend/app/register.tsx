import React, { useState, type ComponentProps } from 'react';
import { 
  View, Text, TextInput, TouchableOpacity, StyleSheet, 
  ScrollView, SafeAreaView, Alert, ActivityIndicator, 
  KeyboardAvoidingView, Platform 
} from 'react-native';
import { useRouter } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';
import axiosInstance from '@/src/api/axiosConfig';

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
    golongan_darah: '',
    no_hp: '',
    alamat: '',
    institusi: '',
    sosmed: '',
    email: '',
    domisili: ''
  });

  const handleDateChange = (text: string) => {
    let cleaned = text.replace(/\D/g, '');
    let formatted = cleaned;
    if (cleaned.length > 2) formatted = `${cleaned.slice(0, 2)}/${cleaned.slice(2, 4)}`;
    if (cleaned.length > 4) formatted = `${cleaned.slice(0, 2)}/${cleaned.slice(2, 4)}/${cleaned.slice(4, 8)}`;
    setForm({ ...form, tanggal_lahir: formatted });
  };

// Cari fungsi handleRegister di register.tsx
const handleRegister = async () => {
    // 1. Validasi lokal yang lebih ketat
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!form.email || !emailRegex.test(form.email.trim())) {
        Alert.alert("Perhatian", "Format email tidak valid. Pastikan tidak ada spasi.");
        return;
    }

    setLoading(true);
    try {
        // Format tanggal ke YYYY-MM-DD
        const parts = form.tanggal_lahir.split('/');
        const formattedDate = `${parts[2]}-${parts[1]}-${parts[0]}`;

        // 2. Buat payload yang bersih (MENGHAPUS SPASI DI SEMUA FIELD KRUSIAL)
        const payload = {
            ...form,
            nama: form.nama.trim(),
            username: form.username.trim().toLowerCase(),
            email: form.email.trim().toLowerCase(), // Pastikan email bersih
            tanggal_lahir: formattedDate,
            no_hp: form.no_hp.trim()
        };

        console.log("Kirim Data ke Server:", payload); // Cek ini di terminal Metro kamu

        const response = await axiosInstance.post('/addnew', payload);

        Alert.alert("Sukses", "Pendaftaran berhasil! Silakan login.");
        router.push('/login');
    } catch (error: any) {
        console.log("XHR Error Detail:", error.response?.data);
        
        // Tangkap pesan error spesifik dari Laravel
        const serverErrors = error.response?.data?.errors;
        let errorMessage = "Gagal mendaftar";
        
        if (serverErrors) {
            // Jika ada error spesifik (seperti email atau username unik)
            const details = Object.values(serverErrors).flat().join('\n');
            errorMessage = details;
        } else {
            errorMessage = error.response?.data?.message || errorMessage;
        }

        Alert.alert("Registrasi Gagal", errorMessage);
    } finally {
        setLoading(false);
    }
};

  return (
    <SafeAreaView style={styles.container}>
      <KeyboardAvoidingView behavior={Platform.OS === 'ios' ? 'padding' : 'height'} style={{flex: 1}}>
        <ScrollView contentContainerStyle={styles.scrollContent}>
          <View style={styles.card}>
            <View style={styles.header}>
              <Text style={styles.title}>Registrasi Klien</Text>
              <Text style={styles.subtitle}>Silakan lengkapi profil STIFIn Anda</Text>
            </View>

            <View style={styles.form}>
              <InputBox label="Nama Lengkap" icon="person-outline" placeholder="Nama Lengkap" onChangeText={(v) => setForm({...form, nama: v})} />
              <InputBox label="Username" icon="at-outline" placeholder="Username" onChangeText={(v) => setForm({...form, username: v})} />
              <InputBox label="Password" icon="lock-closed-outline" placeholder="Password" secureTextEntry onChangeText={(v) => setForm({...form, password: v})} />
              
              <View style={styles.inputGroupOuter}>
                <Text style={styles.inputLabel}>Tanggal Lahir (Tanggal/Bulan/Tahun)</Text>
                <View style={styles.inputWrapper}>
                  <Ionicons name="calendar-outline" size={18} color="#64748b" style={styles.fieldIcon} />
                  <TextInput style={styles.textInput} placeholder="17/08/1945" value={form.tanggal_lahir} onChangeText={handleDateChange} keyboardType="numeric" maxLength={10} placeholderTextColor="#64748b" />
                </View>
              </View>

              <View style={styles.row}>
                <View style={{ flex: 1, marginRight: 10 }}>
  <Text style={styles.inputLabel}>Gender</Text>

  <View style={styles.genderContainer}>
    <TouchableOpacity
      style={[
        styles.genderBox,
        form.jenis_kelamin === 'L' &&
          styles.boxActive
      ]}
      onPress={() =>
        setForm({
          ...form,
          jenis_kelamin: 'L'
        })
      }
    >
      <Text
        style={[
          styles.boxText,
          form.jenis_kelamin === 'L' &&
            styles.textActive
        ]}
      >
        Laki-laki
      </Text>
    </TouchableOpacity>

    <TouchableOpacity
      style={[
        styles.genderBox,
        form.jenis_kelamin === 'P' &&
          styles.boxActive
      ]}
      onPress={() =>
        setForm({
          ...form,
          jenis_kelamin: 'P'
        })
      }
    >
      <Text
        style={[
          styles.boxText,
          form.jenis_kelamin === 'P' &&
            styles.textActive
        ]}
      >
        Perempuan
      </Text>
    </TouchableOpacity>
  </View>
</View>
                <View style={{ flex: 1 }}>
  <Text style={styles.inputLabel}>Gol. Darah</Text>

  <View style={styles.goldarContainer}>
    {['A', 'B', 'AB', 'O'].map((item) => (
      <TouchableOpacity
        key={item}
        style={[
          styles.goldarBox,
          form.golongan_darah === item && styles.boxActive
        ]}
        onPress={() =>
          setForm({ ...form, golongan_darah: item })
        }
      >
        <Text
          style={[
            styles.boxText,
            form.golongan_darah === item &&
              styles.textActive
          ]}
        >
          {item}
        </Text>
      </TouchableOpacity>
    ))}
  </View>
</View>
              </View>

              <InputBox label="No. HP" icon="call-outline" placeholder="812xxx" keyboardType="numeric" onChangeText={(v) => setForm({...form, no_hp: v})} />
              <InputBox label="Email" icon="mail-outline" placeholder="email@anda.com" keyboardType="email-address" onChangeText={(v) => setForm({...form, email: v})} />
              <InputBox label="Institusi" icon="business-outline" placeholder="Nama Sekolah/Kantor" onChangeText={(v) => setForm({...form, institusi: v})} />
              <InputBox label="FB/Instagram" icon="logo-instagram" placeholder="@username" onChangeText={(v) => setForm({...form, sosmed: v})} />
              <InputBox label="Domisili" icon="map-outline" placeholder="Kota saat ini" onChangeText={(v) => setForm({...form, domisili: v})} />
              <InputBox label="Alamat Lengkap" icon="location-outline" placeholder="Alamat detail" multiline onChangeText={(v) => setForm({...form, alamat: v})} />

              <TouchableOpacity style={styles.primaryBtn} onPress={handleRegister} disabled={loading}>
                {loading ? <ActivityIndicator color="#fff" /> : <Text style={styles.btnText}>Daftar Sekarang</Text>}
              </TouchableOpacity>
            </View>
          </View>
        </ScrollView>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}

const InputBox = ({ label, icon, ...props }: InputBoxProps) => (
  <View style={styles.inputGroupOuter}>
    <Text style={styles.inputLabel}>{label}</Text>
    <View style={styles.inputWrapper}>
      <Ionicons name={icon} size={18} color="#64748b" style={styles.fieldIcon} />
      <TextInput style={styles.textInput} placeholderTextColor="#64748b" {...props} />
    </View>
  </View>
);

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#0f172a' },
  scrollContent: { padding: 20, paddingBottom: 40 },
  card: { backgroundColor: '#1e293b', borderRadius: 20, padding: 20 },
  header: { marginBottom: 20, alignItems: 'center' },
  title: { fontSize: 22, fontWeight: 'bold', color: '#fff' },
  subtitle: { color: '#94a3b8', fontSize: 13 },
  form: { 
    width: '100%' 
  },
  inputGroupOuter: { marginBottom: 15 },
  inputLabel: { color: '#94a3b8', fontSize: 11, fontWeight: '600', marginBottom: 5, textTransform: 'uppercase' },
  inputWrapper: { flexDirection: 'row', alignItems: 'center', backgroundColor: '#0f172a', borderRadius: 10, paddingHorizontal: 12, borderWidth: 1, borderColor: '#334155' },
  fieldIcon: { marginRight: 10 },
  textInput: { flex: 1, color: '#fff', paddingVertical: 10, fontSize: 14 },
  row: { flexDirection: 'row', justifyContent: 'space-between' },
  genderContainer: {
  flexDirection: 'row',
  justifyContent: 'space-between'
},

genderBox: {
  flex: 1,
  backgroundColor: '#0f172a',
  borderRadius: 10,
  justifyContent: 'center',
  alignItems: 'center',
  borderWidth: 1,
  borderColor: '#334155',
  paddingVertical: 10,
  marginRight: 6
},

  goldarContainer: {
  flexDirection: 'row',
  flexWrap: 'wrap',
  justifyContent: 'space-between'
},

goldarBox: {
  width: '48%',
  backgroundColor: '#0f172a',
  borderRadius: 10,
  justifyContent: 'center',
  alignItems: 'center',
  borderWidth: 1,
  borderColor: '#334155',
  paddingVertical: 10,
  marginBottom: 8
},
  
  boxActive: { backgroundColor: '#3b82f6', borderColor: '#3b82f6' },
  boxText: { color: '#64748b', fontWeight: "bold", fontSize: 11 },
  textActive: { color: '#fff' },
  primaryBtn: { backgroundColor: '#3b82f6', paddingVertical: 15, borderRadius: 10, alignItems: 'center', marginTop: 10 },
  btnText: { color: '#fff', fontWeight: 'bold', fontSize: 16 }
});