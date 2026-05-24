import React, { useState, useEffect } from 'react';
import { 
  View, 
  Text, 
  StyleSheet, 
  SafeAreaView, 
  TextInput, 
  TouchableOpacity, 
  ScrollView, 
  Alert,
  ActivityIndicator,
  KeyboardTypeOptions,
  Modal,
  FlatList
} from 'react-native';
import { useRouter } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';
import Toast from 'react-native-toast-message';
import axiosInstance from '@/src/api/axiosConfig';

interface ProfileData {
  nama: string;
  no_hp: string;
  tanggal_lahir: string;
  jenis_kelamin: string;
  golongan_darah: string;
  email: string;
  alamat: string;
  institusi: string;
  sosmed: string;
  domisili: string;
}

interface InputGroupProps {
  label: string;
  value: string;
  onChange: (val: string) => void;
  keyboardType?: KeyboardTypeOptions;
  multiline?: boolean;
  editable?: boolean;
}

export default function EditProfile() {
  const router = useRouter();
  const [loading, setLoading] = useState<boolean>(false);
  const [fetching, setFetching] = useState<boolean>(true);

  const [formData, setFormData] = useState<ProfileData>({
    nama: '',
    no_hp: '',
    tanggal_lahir: '',
    jenis_kelamin: 'L',
    golongan_darah: '-',
    email: '',
    alamat: '',
    institusi: '',
    sosmed: '',
    domisili: ''
  });

  // State untuk mengontrol Modal Picker Kustom
  const [activePicker, setActivePicker] = useState<'jk' | 'golDarah' | null>(null);

  const opsiJK = [
    { label: 'Laki-laki', value: 'L' },
    { label: 'Perempuan', value: 'P' }
  ];

  const opsiGolDarah = [
    { label: '-', value: '-' },
    { label: 'A', value: 'A' },
    { label: 'B', value: 'B' },
    { label: 'AB', value: 'AB' },
    { label: 'O', value: 'O' }
  ];

  // 1. Load Data Profil Saat Ini
  useEffect(() => {
    const fetchProfile = async () => {
      try {
        const response = await axiosInstance.get('/profile');
        const u = response.data;

        if (u) {
          setFormData({
            nama: u.nama || '',
            no_hp: u.no_hp || '',
            tanggal_lahir: u.tanggal_lahir || '',
            jenis_kelamin: u.jenis_kelamin || 'L',
            golongan_darah: u.golongan_darah || '-',
            email: u.email || '', 
            alamat: u.alamat || '',
            institusi: u.institusi || '',
            sosmed: u.sosmed || '',
            domisili: u.domisili || ''
          });
        }
      } catch (error: any) {
        console.log("Gagal mengambil data profil:", error.message);
        Alert.alert("Error", "Gagal memuat data profil.");
      } finally {
        setFetching(false);
      }
    };
    fetchProfile();
  }, []);

  // 2. Kirim Perubahan ke Backend
  const handleSimpan = async () => {
  if (!formData.nama || !formData.no_hp || !formData.email || !formData.alamat) {
    Toast.show({
      type: 'error',
      text1: 'Perhatian',
      text2: 'Nama, No HP, Email, dan Alamat wajib diisi.'
    });
    return;
  }

  setLoading(true);
  try {
    const response = await axiosInstance.put('/profile/update', formData);
    if (response.status === 200) {
      // GANTI ALERT DENGAN TOAST SUKSES
      Toast.show({
        type: 'success',
        text1: 'Sukses',
        text2: 'Data berhasil diperbarui',
        position: 'top',
        visibilityTime: 3000,
      });
      
      // Opsional: kembali ke home setelah 1-2 detik
      setTimeout(() => router.replace('/home'), 1500);
    }
  } catch (error: any) {
    console.log("Error Update Profil:", error.response?.data || error.message);
    
    Toast.show({
      type: 'error',
      text1: 'Gagal',
      text2: error.response?.status === 422 
        ? 'Email sudah digunakan orang lain.' 
        : 'Terjadi kesalahan saat memperbarui profil.'
    });
  } finally {
    setLoading(false);
  }
};

  if (fetching) {
    return (
      <View style={styles.center}>
        <ActivityIndicator size="large" color="#1e40af" />
        <Text style={{ marginTop: 10 }}>Memuat data profil...</Text>
      </View>
    );
  }

  const labelJenisKelaminAktif = opsiJK.find(o => o.value === formData.jenis_kelamin)?.label || 'Pilih';

  return (
    <SafeAreaView style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => router.replace('/home')}>
          <Ionicons name="arrow-back" size={24} color="#000" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Edit Profil</Text>
      </View>

      <ScrollView contentContainerStyle={styles.content} showsVerticalScrollIndicator={false}>
        <Text style={styles.sectionTitle}>Data Akun & Personal</Text>

        <InputGroup 
          label="Email Aktif" 
          value={formData.email} 
          keyboardType="email-address"
          onChange={(val) => setFormData({...formData, email: val})} 
          editable={true} 
        />

        <InputGroup 
          label="Nama Lengkap" 
          value={formData.nama} 
          onChange={(val) => setFormData({...formData, nama: val})} 
        />

        <InputGroup 
          label="Nomor WhatsApp" 
          value={formData.no_hp} 
          keyboardType="phone-pad"
          onChange={(val) => setFormData({...formData, no_hp: val})} 
        />

        {/* BARU: Dropdown Menggunakan Selector Box Kustom yang Aman */}
        <View style={styles.row}>
          <View style={{ flex: 1, marginRight: 10 }}>
            <Text style={styles.labelSimple}>Jenis Kelamin</Text>
            <TouchableOpacity 
              style={styles.selectorField} 
              activeOpacity={0.7}
              onPress={() => setActivePicker('jk')}
            >
              <Text style={styles.selectorValueText}>{labelJenisKelaminAktif}</Text>
              <Ionicons name="chevron-down" size={16} color="#64748b" />
            </TouchableOpacity>
          </View>

          <View style={{ width: 120 }}>
            <Text style={styles.labelSimple}>Gol. Darah</Text>
            <TouchableOpacity 
              style={styles.selectorField} 
              activeOpacity={0.7}
              onPress={() => setActivePicker('golDarah')}
            >
              <Text style={styles.selectorValueText}>{formData.golongan_darah}</Text>
              <Ionicons name="chevron-down" size={16} color="#64748b" />
            </TouchableOpacity>
          </View>
        </View>

        <InputGroup 
          label="Tanggal Lahir (Tahun-Bulan-Tanggal)" 
          value={formData.tanggal_lahir} 
          onChange={(val) => setFormData({...formData, tanggal_lahir: val})} 
        />

        <Text style={styles.sectionTitle}>Data Tambahan</Text>

        <InputGroup 
          label="Institusi / Pekerjaan" 
          value={formData.institusi} 
          onChange={(val) => setFormData({...formData, institusi: val})} 
        />

        <InputGroup 
          label="Username Sosial Media" 
          value={formData.sosmed} 
          onChange={(val) => setFormData({...formData, sosmed: val})} 
        />

        <InputGroup 
          label="Kota Domisili" 
          value={formData.domisili} 
          onChange={(val) => setFormData({...formData, domisili: val})} 
        />

        <InputGroup 
          label="Alamat Lengkap" 
          value={formData.alamat} 
          multiline
          onChange={(val) => setFormData({...formData, alamat: val})} 
        />

        {/* Tombol Simpan */}
        <View style={styles.footer}>
          <TouchableOpacity 
            style={[styles.btnSubmit, loading && { opacity: 0.7 }]} 
            onPress={handleSimpan}
            disabled={loading}
          >
            {loading ? (
              <ActivityIndicator color="#fff" />
            ) : (
              <Text style={styles.btnText}>Simpan Perubahan</Text>
            )}
          </TouchableOpacity>
        </View>
      </ScrollView>

      {/* KUSTOM PICKER BOTTOM SHEET MODAL */}
      <Modal
        visible={activePicker !== null}
        transparent={true}
        animationType="slide"
        onRequestClose={() => setActivePicker(null)}
      >
        <TouchableOpacity 
          style={styles.modalOverlay} 
          activeOpacity={1} 
          onPress={() => setActivePicker(null)}
        >
          <View style={styles.modalSheet}>
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>
                {activePicker === 'jk' ? 'Pilih Jenis Kelamin' : 'Pilih Golongan Darah'}
              </Text>
              <TouchableOpacity onPress={() => setActivePicker(null)}>
                <Ionicons name="close-circle" size={24} color="#94a3b8" />
              </TouchableOpacity>
            </View>

            <FlatList
              data={activePicker === 'jk' ? opsiJK : opsiGolDarah}
              keyExtractor={(item) => item.value}
              renderItem={({ item }) => (
                <TouchableOpacity
                  style={[
                    styles.modalItem,
                    ((activePicker === 'jk' ? formData.jenis_kelamin : formData.golongan_darah) === item.value) && styles.modalItemOptionSelected
                  ]}
                  onPress={() => {
                    if (activePicker === 'jk') {
                      setFormData({ ...formData, jenis_kelamin: item.value });
                    } else {
                      setFormData({ ...formData, golongan_darah: item.value });
                    }
                    setActivePicker(null);
                  }}
                >
                  <Text style={styles.modalItemText}>{item.label}</Text>
                  {((activePicker === 'jk' ? formData.jenis_kelamin : formData.golongan_darah) === item.value) && (
                    <Ionicons name="checkmark-circle" size={20} color="#1e40af" />
                  )}
                </TouchableOpacity>
              )}
            />
          </View>
        </TouchableOpacity>
      </Modal>
    </SafeAreaView>
  );
}

const InputGroup = ({ label, value, onChange, keyboardType = 'default', multiline = false, editable = true }: InputGroupProps) => (
  <View style={styles.inputGroup}>
    <Text style={styles.labelSimple}>{label}</Text>
    <TextInput 
      style={[styles.input, multiline && styles.textArea, !editable && styles.disabledInput]} 
      value={value}
      onChangeText={onChange}
      keyboardType={keyboardType}
      multiline={multiline}
      editable={editable}
      placeholderTextColor="#94a3b8"
    />
  </View>
);

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f8fafc' },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  header: { padding: 20, backgroundColor: '#fff', flexDirection: 'row', alignItems: 'center', gap: 15, borderBottomWidth: 1, borderColor: '#e2e8f0' },
  headerTitle: { fontSize: 18, fontWeight: 'bold', color: '#0f172a' },
  content: { padding: 20 },
  sectionTitle: { fontSize: 16, fontWeight: 'bold', color: '#1e40af', marginBottom: 15, marginTop: 10 },
  inputGroup: { marginBottom: 15 },
  labelSimple: { fontSize: 13, fontWeight: '600', color: '#475569', marginBottom: 6, marginLeft: 2 },
  input: { backgroundColor: '#fff', borderWidth: 1, borderColor: '#cbd5e1', borderRadius: 10, padding: 12, fontSize: 15, color: '#1e293b' },
  disabledInput: { backgroundColor: '#f1f5f9', color: '#64748b', borderColor: '#e2e8f0' },
  textArea: { height: 80, textAlignVertical: 'top' },
  row: { flexDirection: 'row', marginBottom: 15 },
  
  // Style untuk Custom Selector UI Baru pengganti Picker Native
  selectorField: { backgroundColor: '#fff', borderWidth: 1, borderColor: '#cbd5e1', borderRadius: 10, height: 48, flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', paddingHorizontal: 12 },
  selectorValueText: { fontSize: 15, color: '#1e293b' },

  // Style Modal Bottom Sheet Pop up
  modalOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.4)', justifyContent: 'flex-end' },
  modalSheet: { backgroundColor: '#fff', borderTopLeftRadius: 20, borderTopRightRadius: 20, padding: 20, maxHeight: '50%' },
  modalHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 15, paddingBottom: 10, borderBottomWidth: 1, borderBottomColor: '#f1f5f9' },
  modalTitle: { fontSize: 16, fontWeight: 'bold', color: '#0f172a' },
  modalItem: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', paddingVertical: 14, paddingHorizontal: 10, borderRadius: 8 },
  modalItemOptionSelected: { backgroundColor: '#eff6ff' },
  modalItemText: { fontSize: 15, color: '#334155' },

  footer: { marginTop: 10, marginBottom: 40 },
  btnSubmit: { backgroundColor: '#1e40af', padding: 16, borderRadius: 12, alignItems: 'center', elevation: 4 },
  btnText: { color: '#fff', fontWeight: 'bold', fontSize: 16 }
});