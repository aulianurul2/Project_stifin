import React, { useState, type ComponentProps } from 'react';
import { 
  View, Text, TextInput, TouchableOpacity, StyleSheet, 
  ScrollView, SafeAreaView, ActivityIndicator, 
  KeyboardAvoidingView, Platform, Modal, FlatList,
  StatusBar,
} from 'react-native';
import { useRouter } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';
import axiosInstance from '@/src/api/axiosConfig';
import Toast from 'react-native-toast-message';

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
  const [activePicker, setActivePicker] = useState<'jk' | 'golDarah' | null>(null);

const opsiJK = [
  { label: 'Laki-laki', value: 'L' },
  { label: 'Perempuan', value: 'P' },
];

const opsiGolDarah = [
  { label: '-', value: '-' },
  { label: 'A', value: 'A' },
  { label: 'B', value: 'B' },
  { label: 'AB', value: 'AB' },
  { label: 'O', value: 'O' },
];

const labelJK = opsiJK.find(o => o.value === form.jenis_kelamin)?.label || 'Pilih';

  const handleDateChange = (text: string) => {
    let cleaned = text.replace(/\D/g, '');
    let formatted = cleaned;
    if (cleaned.length > 2) formatted = `${cleaned.slice(0, 2)}/${cleaned.slice(2, 4)}`;
    if (cleaned.length > 4) formatted = `${cleaned.slice(0, 2)}/${cleaned.slice(2, 4)}/${cleaned.slice(4, 8)}`;
    setForm({ ...form, tanggal_lahir: formatted });
  };

  // Terjemahkan pesan error dari server ke Bahasa Indonesia
  const terjemahkanError = (pesan: string): string => {
    const kamus: Record<string, string> = {
      // Laravel validation umum
      'The nama field is required.': 'Nama lengkap wajib diisi.',
      'The username field is required.': 'Username wajib diisi.',
      'The username has already been taken.': 'Username sudah digunakan.',
      'The password field is required.': 'Password wajib diisi.',
      'The password must be at least 6 characters.': 'Password minimal 6 karakter.',
      'The email field is required.': 'Email wajib diisi.',
      'The email has already been taken.': 'Email sudah terdaftar, gunakan email lain.',
      'The email must be a valid email address.': 'Format email tidak valid.',
      'The no hp field is required.': 'Nomor HP wajib diisi.',
      'The no_hp field is required.': 'Nomor HP wajib diisi.',
      'The tanggal lahir field is required.': 'Tanggal lahir wajib diisi.',
      'The tanggal_lahir field is required.': 'Tanggal lahir wajib diisi.',
      'The jenis kelamin field is required.': 'Jenis kelamin wajib dipilih.',
      'The jenis_kelamin field is required.': 'Jenis kelamin wajib dipilih.',
      'The alamat field is required.': 'Alamat wajib diisi.',
      'The given data was invalid.': 'Data yang dikirim tidak valid.',
      'These credentials do not match our records.': 'Data tidak cocok dengan yang ada di sistem.',
      'Too Many Attempts.': 'Terlalu banyak percobaan. Coba lagi beberapa saat.',
      'Server Error': 'Terjadi kesalahan pada server. Coba lagi nanti.',
      'The tanggal lahir field must be a valid date.': 'Isi tanggal lahir dengan benar.',
      'The domisili field is required.': 'Domisili wajib diisi.',
      'The sosmed field is required.': 'Sosial media wajib diisi.',
      'The golongan darah field is required.': 'Golongan darah wajib diisi.',
      'Network Error': 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.',
    };
    return kamus[pesan] ?? pesan;
  };

  const handleRegister = async () => {
    if (!form.nama || !form.username || !form.password || !form.tanggal_lahir || !form.jenis_kelamin) {
      Toast.show({
        type: 'error',
        text1: 'Data Belum Lengkap',
        text2: 'Isi semua data pada bagian Akun & Kontak.',
        position: 'top'
      });
      return;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!form.email || !emailRegex.test(form.email.trim())) {
      Toast.show({
        type: 'error',
        text1: 'Email Tidak Valid',
        text2: 'Masukkan alamat email yang benar.',
        position: 'top'
      });
      return;
    }

    if (form.password.length < 6) {
      Toast.show({
        type: 'error',
        text1: 'Password Terlalu Pendek',
        text2: 'Password minimal harus 6 karakter.',
        position: 'top'
      });
      return;
    }

    setLoading(true);
    try {
      const parts = form.tanggal_lahir.split('/');
      if (parts.length !== 3) {
        Toast.show({
          type: 'error',
          text1: 'Format Tanggal Salah',
          text2: 'Masukkan tanggal lahir dengan format Tanggal/Bulan/Tahun.',
          position: 'top'
        });
        setLoading(false);
        return;
      }
      const formattedDate = `${parts[2]}-${parts[1]}-${parts[0]}`;

      const payload = {
        ...form,
        nama: form.nama.trim(),
        username: form.username.trim().toLowerCase(),
        email: form.email.trim().toLowerCase(), 
        tanggal_lahir: formattedDate,
        no_hp: form.no_hp.trim()
      };

      const response = await axiosInstance.post('/addnew', payload);

      Toast.show({
        type: 'success',
        text1: 'Registrasi Berhasil',
        text2: 'Akun Anda telah terdaftar. Silakan masuk.',
        position: 'top',
        visibilityTime: 2500
      });

      setTimeout(() => {
        router.push('/login');
      }, 1500);

    } catch (error: any) {
      console.log("XHR Error Detail:", error.response?.data);
      
      const serverErrors = error.response?.data?.errors;
      let pesanError = 'Pendaftaran gagal. Silakan coba lagi.';
      
      if (serverErrors) {
        // Terjemahkan setiap pesan error dari server
        const semuaError = (Object.values(serverErrors).flat() as string[])
          .map(terjemahkanError);
        pesanError = semuaError.join('\n');
      } else if (error.response?.data?.message) {
        pesanError = terjemahkanError(error.response.data.message);
      } else if (error.message) {
        pesanError = terjemahkanError(error.message);
      }

      Toast.show({
        type: 'error',
        text1: 'Pendaftaran Gagal',
        text2: pesanError,
        position: 'top'
      });
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView style={styles.container}>
      <KeyboardAvoidingView behavior={Platform.OS === 'ios' ? 'padding' : 'height'} style={{flex: 1}}>
        
        <View style={styles.topBar}>
          <TouchableOpacity style={styles.backBtn} onPress={() => router.back()}>
            <Ionicons name="arrow-back-outline" size={22} color="#fff" />
          </TouchableOpacity>
          <View style={styles.topBarCenter}>
            <Text style={styles.topBarTitle}>Registrasi Klien</Text>
            <Text style={styles.topBarSub}>Lengkapi profil STIFIn Anda</Text>
          </View>
          <View style={{ width: 38 }} />
        </View>

        <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
          
          <View style={styles.progressHint}>
            <Ionicons name="shield-checkmark-outline" size={16} color="#00AA5B" />
            <Text style={styles.progressHintText}>Data Anda aman dan terenkripsi</Text>
          </View>

          <View style={styles.section}>
            <View style={styles.sectionHeader}>
              <View style={styles.sectionBadge}>
                <Text style={styles.sectionBadgeText}>01</Text>
              </View>
              <Text style={styles.sectionTitle}>Akun & Identitas</Text>
            </View>

            <InputBox label="Nama Lengkap" icon="person-outline" placeholder="Nama Lengkap" onChangeText={(v) => setForm({...form, nama: v})} />
            <InputBox label="Username" icon="at-outline" placeholder="Username" onChangeText={(v) => setForm({...form, username: v})} />
            <InputBox label="Password" icon="lock-closed-outline" placeholder="Minimal 6 karakter" secureTextEntry onChangeText={(v) => setForm({...form, password: v})} />
            
            <View style={styles.inputGroupOuter}>
              <Text style={styles.inputLabel}>Tanggal Lahir (Tanggal/Bulan/Tahun)</Text>
              <View style={styles.inputWrapper}>
                <Ionicons name="calendar-outline" size={18} color="#00AA5B" style={styles.fieldIcon} />
                <TextInput style={styles.textInput} placeholder="17/08/1945" value={form.tanggal_lahir} onChangeText={handleDateChange} keyboardType="numeric" maxLength={10} placeholderTextColor="#b0bec5" />
              </View>
            </View>

            <View style={styles.row}>
              <View style={{ flex: 1, marginRight: 10 }}>
                <Text style={styles.inputLabel}>Gender</Text>
                <TouchableOpacity style={styles.selectorField} activeOpacity={0.7} onPress={() => setActivePicker('jk')}>
                  <Ionicons name={form.jenis_kelamin === 'L' ? 'male-outline' : 'female-outline'} size={16} color="#00AA5B" />
                  <Text style={styles.selectorValueText}>{labelJK}</Text>
                  <Ionicons name="chevron-down" size={14} color="#90a4ae" />
                </TouchableOpacity>
              </View>
              <View style={{ flex: 1 }}>
                <Text style={styles.inputLabel}>Gol. Darah</Text>
                <TouchableOpacity style={styles.selectorField} activeOpacity={0.7} onPress={() => setActivePicker('golDarah')}>
                  <Ionicons name="water-outline" size={16} color="#00AA5B" />
                  <Text style={styles.selectorValueText}>{form.golongan_darah || '-'}</Text>
                  <Ionicons name="chevron-down" size={14} color="#90a4ae" />
                </TouchableOpacity>
              </View>
            </View>
          </View>

          <View style={styles.section}>
            <View style={styles.sectionHeader}>
              <View style={styles.sectionBadge}>
                <Text style={styles.sectionBadgeText}>02</Text>
              </View>
              <Text style={styles.sectionTitle}>Kontak & Lainnya</Text>
            </View>

            <InputBox label="No. HP / WhatsApp" icon="call-outline" placeholder="0812xxx" keyboardType="numeric" onChangeText={(v) => setForm({...form, no_hp: v})} />
            <InputBox label="Email" icon="mail-outline" placeholder="email@anda.com" keyboardType="email-address" onChangeText={(v) => setForm({...form, email: v})} />
            <InputBox label="Institusi" icon="business-outline" placeholder="Nama Sekolah/Kantor" onChangeText={(v) => setForm({...form, institusi: v})} />
            <InputBox label="FB/Instagram" icon="logo-instagram" placeholder="@username" onChangeText={(v) => setForm({...form, sosmed: v})} />
            <InputBox label="Domisili" icon="map-outline" placeholder="Kota saat ini" onChangeText={(v) => setForm({...form, domisili: v})} />
            <InputBox label="Alamat Lengkap" icon="location-outline" placeholder="Alamat detail" multiline onChangeText={(v) => setForm({...form, alamat: v})} />
          </View>

          <TouchableOpacity style={styles.primaryBtn} onPress={handleRegister} disabled={loading} activeOpacity={0.85}>
            {loading ? <ActivityIndicator color="#fff" /> : (
              <View style={styles.btnInner}>
                <Text style={styles.btnText}>Daftar Sekarang</Text>
                <Ionicons name="arrow-forward" size={18} color="#fff" />
              </View>
            )}
          </TouchableOpacity>

          <View style={styles.loginLink}>
            <Text style={styles.loginLinkText}>Sudah punya akun? </Text>
            <TouchableOpacity onPress={() => router.push('/login')}>
              <Text style={styles.loginLinkBold}>Masuk di sini</Text>
            </TouchableOpacity>
          </View>

          <Modal
            visible={activePicker !== null}
            transparent
            animationType="slide"
            onRequestClose={() => setActivePicker(null)}
          >
            <TouchableOpacity style={styles.modalOverlay} activeOpacity={1} onPress={() => setActivePicker(null)}>
              <View style={styles.modalSheet}>
                <View style={styles.modalHandle} />
                <View style={styles.modalHeader}>
                  <Text style={styles.modalTitle}>
                    {activePicker === 'jk' ? 'Pilih Jenis Kelamin' : 'Pilih Golongan Darah'}
                  </Text>
                  <TouchableOpacity onPress={() => setActivePicker(null)} style={styles.modalClose}>
                    <Ionicons name="close" size={20} color="#90a4ae" />
                  </TouchableOpacity>
                </View>
                <FlatList
                  data={activePicker === 'jk' ? opsiJK : opsiGolDarah}
                  keyExtractor={(item) => item.value}
                  renderItem={({ item }) => {
                    const isSelected = (activePicker === 'jk' ? form.jenis_kelamin : form.golongan_darah) === item.value;
                    return (
                      <TouchableOpacity
                        style={[styles.modalItem, isSelected && styles.modalItemSelected]}
                        onPress={() => {
                          if (activePicker === 'jk') {
                            setForm({ ...form, jenis_kelamin: item.value });
                          } else {
                            setForm({ ...form, golongan_darah: item.value });
                          }
                          setActivePicker(null);
                        }}
                      >
                        <Text style={[styles.modalItemText, isSelected && styles.modalItemTextSelected]}>
                          {item.label}
                        </Text>
                        {isSelected && <Ionicons name="checkmark-circle" size={20} color="#00AA5B" />}
                      </TouchableOpacity>
                    );
                  }}
                />
              </View>
            </TouchableOpacity>
          </Modal>
        </ScrollView>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}

const InputBox = ({ label, icon, ...props }: InputBoxProps) => (
  <View style={styles.inputGroupOuter}>
    <Text style={styles.inputLabel}>{label}</Text>
    <View style={styles.inputWrapper}>
      <Ionicons name={icon} size={18} color="#00AA5B" style={styles.fieldIcon} />
      <TextInput style={styles.textInput} placeholderTextColor="#b0bec5" {...props} />
    </View>
  </View>
);

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f5faf7' },
  topBar: {
    backgroundColor: '#00AA5B',
    paddingTop: Platform.OS === 'android' ? (StatusBar.currentHeight ? StatusBar.currentHeight + 12 : 30) : 16,
    paddingBottom: 18,
    paddingHorizontal: 16,
    flexDirection: 'row',
    alignItems: 'center',
    shadowColor: '#00AA5B',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.25,
    shadowRadius: 8,
    elevation: 6,
  },
  backBtn: {
    width: 38, height: 38, borderRadius: 19,
    backgroundColor: 'rgba(255,255,255,0.2)',
    justifyContent: 'center', alignItems: 'center',
  },
  topBarCenter: { flex: 1, alignItems: 'center' },
  topBarTitle: { fontSize: 18, fontWeight: '800', color: '#fff' },
  topBarSub: { fontSize: 12, color: 'rgba(255,255,255,0.8)', marginTop: 2 },
  scrollContent: { padding: 16, paddingBottom: 48 },
  progressHint: {
    flexDirection: 'row', alignItems: 'center',
    backgroundColor: '#e8f5e9', borderRadius: 10,
    paddingHorizontal: 12, paddingVertical: 8, marginBottom: 16, gap: 6,
  },
  progressHintText: { fontSize: 12, color: '#2e7d32', fontWeight: '600' },
  section: {
    backgroundColor: '#fff', borderRadius: 18, padding: 18, marginBottom: 14,
    shadowColor: '#00AA5B', shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.06, shadowRadius: 8, elevation: 2,
  },
  sectionHeader: { flexDirection: 'row', alignItems: 'center', marginBottom: 18, gap: 10 },
  sectionBadge: {
    width: 28, height: 28, borderRadius: 14,
    backgroundColor: '#00AA5B', justifyContent: 'center', alignItems: 'center',
  },
  sectionBadgeText: { color: '#fff', fontSize: 11, fontWeight: '800' },
  sectionTitle: { fontSize: 15, fontWeight: '800', color: '#1a1a2e' },
  inputGroupOuter: { marginBottom: 14 },
  inputLabel: { color: '#546e7a', fontSize: 11, fontWeight: '700', marginBottom: 6, textTransform: 'uppercase', letterSpacing: 0.6 },
  inputWrapper: {
    flexDirection: 'row', alignItems: 'center',
    backgroundColor: '#f5faf7', borderRadius: 12,
    paddingHorizontal: 12, borderWidth: 1.5, borderColor: '#e0f2ec',
  },
  fieldIcon: { marginRight: 10 },
  textInput: { flex: 1, color: '#1a1a2e', paddingVertical: 12, fontSize: 14 },
  row: { flexDirection: 'row', justifyContent: 'space-between' },
  selectorField: {
    backgroundColor: '#f5faf7', borderWidth: 1.5, borderColor: '#e0f2ec',
    borderRadius: 12, height: 48, flexDirection: 'row',
    alignItems: 'center', paddingHorizontal: 12, gap: 6,
  },
  selectorValueText: { flex: 1, fontSize: 14, color: '#1a1a2e' },
  modalOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.4)', justifyContent: 'flex-end' },
  modalSheet: {
    backgroundColor: '#fff', borderTopLeftRadius: 24,
    borderTopRightRadius: 24, padding: 20, maxHeight: '50%',
  },
  modalHandle: {
    width: 40, height: 4, borderRadius: 2,
    backgroundColor: '#e0f2ec', alignSelf: 'center', marginBottom: 16,
  },
  modalHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 16 },
  modalClose: {
    width: 32, height: 32, borderRadius: 16,
    backgroundColor: '#f5faf7', justifyContent: 'center', alignItems: 'center',
  },
  modalTitle: { fontSize: 16, fontWeight: '800', color: '#1a1a2e' },
  modalItem: {
    flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center',
    paddingVertical: 14, paddingHorizontal: 12, borderRadius: 10, marginBottom: 4,
  },
  modalItemSelected: { backgroundColor: '#e8f5e9' },
  modalItemText: { fontSize: 15, color: '#37474f', fontWeight: '600' },
  modalItemTextSelected: { color: '#00AA5B', fontWeight: '700' },
  primaryBtn: {
    backgroundColor: '#00AA5B', paddingVertical: 16, borderRadius: 14,
    alignItems: 'center', marginTop: 8, marginBottom: 4,
    shadowColor: '#00AA5B', shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.35, shadowRadius: 10, elevation: 6,
  },
  btnInner: { flexDirection: 'row', alignItems: 'center', gap: 8 },
  btnText: { color: '#fff', fontWeight: '800', fontSize: 16 },
  loginLink: { flexDirection: 'row', justifyContent: 'center', marginTop: 16 },
  loginLinkText: { color: '#90a4ae', fontSize: 14 },
  loginLinkBold: { color: '#00AA5B', fontWeight: '800', fontSize: 14 },
});