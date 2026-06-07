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
  FlatList,
  Platform,
  StatusBar,
} from 'react-native';
import { useRouter } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';
import Toast from 'react-native-toast-message';
import AsyncStorage from '@react-native-async-storage/async-storage'; // ✅ TAMBAHAN
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
  icon?: keyof typeof Ionicons.glyphMap;
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
    { label: 'O', value: 'O' },
  ];

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
        // ✅ PERBAIKAN: Simpan nama terbaru ke AsyncStorage agar home.tsx langsung sinkron
        await AsyncStorage.setItem('user_name', formData.nama);

        Toast.show({
          type: 'success',
          text1: 'Sukses',
          text2: 'Data berhasil diperbarui',
          position: 'top',
          visibilityTime: 3000,
        });
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
        <View style={styles.loadingCard}>
          <ActivityIndicator size="large" color="#00AA5B" />
          <Text style={styles.loadingText}>Memuat data profil...</Text>
        </View>
      </View>
    );
  }

  const labelJenisKelaminAktif = opsiJK.find(o => o.value === formData.jenis_kelamin)?.label || 'Pilih';

  return (
    <SafeAreaView style={styles.container}>

      {/* Green Top Bar */}
      <View style={styles.topBar}>
        <TouchableOpacity style={styles.backBtn} onPress={() => router.replace('/profile')}>
          <Ionicons name="arrow-back" size={22} color="#fff" />
        </TouchableOpacity>
        <View style={styles.topBarCenter}>
          <Text style={styles.topBarTitle}>Edit Profil</Text>
          <Text style={styles.topBarSub}>Perbarui informasi Anda</Text>
        </View>
        <View style={{ width: 38 }} />
      </View>

      <ScrollView contentContainerStyle={styles.content} showsVerticalScrollIndicator={false}>

        {/* Section 1: Akun */}
        <View style={styles.section}>
          <View style={styles.sectionHeader}>
            <View style={styles.sectionIcon}>
              <Ionicons name="person-outline" size={16} color="#00AA5B" />
            </View>
            <Text style={styles.sectionTitle}>Data Akun & Personal</Text>
          </View>

          <InputGroup 
            label="Email Aktif" 
            value={formData.email} 
            keyboardType="email-address"
            onChange={(val) => setFormData({...formData, email: val})}
            icon="mail-outline"
          />

          <InputGroup 
            label="Nama Lengkap" 
            value={formData.nama} 
            onChange={(val) => setFormData({...formData, nama: val})}
            icon="person-outline"
          />

          <InputGroup 
            label="Nomor WhatsApp" 
            value={formData.no_hp} 
            keyboardType="phone-pad"
            onChange={(val) => setFormData({...formData, no_hp: val})}
            icon="call-outline"
          />

          {/* Gender & Goldar Row */}
          <View style={styles.row}>
            <View style={{ flex: 1, marginRight: 10 }}>
              <Text style={styles.labelSimple}>Jenis Kelamin</Text>
              <TouchableOpacity 
                style={styles.selectorField} 
                activeOpacity={0.7}
                onPress={() => setActivePicker('jk')}
              >
                <Ionicons name={formData.jenis_kelamin === 'L' ? 'male-outline' : 'female-outline'} size={16} color="#00AA5B" />
                <Text style={styles.selectorValueText}>{labelJenisKelaminAktif}</Text>
                <Ionicons name="chevron-down" size={14} color="#90a4ae" />
              </TouchableOpacity>
            </View>

            <View style={{ width: 120 }}>
              <Text style={styles.labelSimple}>Gol. Darah</Text>
              <TouchableOpacity 
                style={styles.selectorField} 
                activeOpacity={0.7}
                onPress={() => setActivePicker('golDarah')}
              >
                <Ionicons name="water-outline" size={16} color="#00AA5B" />
                <Text style={styles.selectorValueText}>{formData.golongan_darah}</Text>
                <Ionicons name="chevron-down" size={14} color="#90a4ae" />
              </TouchableOpacity>
            </View>
          </View>

          <InputGroup 
            label="Tanggal Lahir (Tahun-Bulan-Tanggal)" 
            value={formData.tanggal_lahir} 
            onChange={(val) => setFormData({...formData, tanggal_lahir: val})}
            icon="calendar-outline"
          />
        </View>

        {/* Section 2: Tambahan */}
        <View style={styles.section}>
          <View style={styles.sectionHeader}>
            <View style={styles.sectionIcon}>
              <Ionicons name="information-circle-outline" size={16} color="#00AA5B" />
            </View>
            <Text style={styles.sectionTitle}>Data Tambahan</Text>
          </View>

          <InputGroup 
            label="Institusi / Pekerjaan" 
            value={formData.institusi} 
            onChange={(val) => setFormData({...formData, institusi: val})}
            icon="business-outline"
          />

          <InputGroup 
            label="Username Sosial Media" 
            value={formData.sosmed} 
            onChange={(val) => setFormData({...formData, sosmed: val})}
            icon="logo-instagram"
          />

          <InputGroup 
            label="Kota Domisili" 
            value={formData.domisili} 
            onChange={(val) => setFormData({...formData, domisili: val})}
            icon="map-outline"
          />

          <InputGroup 
            label="Alamat Lengkap" 
            value={formData.alamat} 
            multiline
            onChange={(val) => setFormData({...formData, alamat: val})}
            icon="location-outline"
          />
        </View>

        {/* Save Button */}
        <View style={styles.footer}>
          <TouchableOpacity
            style={[styles.btnSubmit, loading && { opacity: 0.7 }]}
            onPress={handleSimpan}
            disabled={loading}
            activeOpacity={0.85}
          >
            {loading ? (
              <ActivityIndicator color="#fff" />
            ) : (
              <View style={styles.btnInner}>
                <Ionicons name="checkmark-circle-outline" size={20} color="#fff" />
                <Text style={styles.btnText}>Simpan Perubahan</Text>
              </View>
            )}
          </TouchableOpacity>
        </View>

      </ScrollView>

      {/* Custom Picker Modal */}
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
                const isSelected = (activePicker === 'jk' ? formData.jenis_kelamin : formData.golongan_darah) === item.value;
                return (
                  <TouchableOpacity
                    style={[styles.modalItem, isSelected && styles.modalItemSelected]}
                    onPress={() => {
                      if (activePicker === 'jk') {
                        setFormData({ ...formData, jenis_kelamin: item.value });
                      } else {
                        setFormData({ ...formData, golongan_darah: item.value });
                      }
                      setActivePicker(null);
                    }}
                  >
                    <Text style={[styles.modalItemText, isSelected && styles.modalItemTextSelected]}>{item.label}</Text>
                    {isSelected && (
                      <Ionicons name="checkmark-circle" size={20} color="#00AA5B" />
                    )}
                  </TouchableOpacity>
                );
              }}
            />
          </View>
        </TouchableOpacity>
      </Modal>
    </SafeAreaView>
  );
}

const InputGroup = ({ label, value, onChange, keyboardType = 'default', multiline = false, editable = true, icon }: InputGroupProps) => (
  <View style={styles.inputGroup}>
    <Text style={styles.labelSimple}>{label}</Text>
    <View style={[styles.inputRow, !editable && styles.inputRowDisabled]}>
      {icon && <Ionicons name={icon} size={16} color="#00AA5B" style={styles.inputIcon} />}
      <TextInput 
        style={[styles.input, multiline && styles.textArea]} 
        value={value}
        onChangeText={onChange}
        keyboardType={keyboardType}
        multiline={multiline}
        editable={editable}
        placeholderTextColor="#b0bec5"
      />
    </View>
  </View>
);

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f5faf7' },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center', backgroundColor: '#f5faf7' },
  loadingCard: { alignItems: 'center', gap: 12 },
  loadingText: { fontSize: 14, color: '#546e7a', fontWeight: '600' },

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
    width: 38,
    height: 38,
    borderRadius: 19,
    backgroundColor: 'rgba(255,255,255,0.2)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  topBarCenter: { flex: 1, alignItems: 'center' },
  topBarTitle: { fontSize: 18, fontWeight: '800', color: '#fff' },
  topBarSub: { fontSize: 11, color: 'rgba(255,255,255,0.8)', marginTop: 2 },

  content: { padding: 16, paddingBottom: 48 },

  section: {
    backgroundColor: '#fff',
    borderRadius: 18,
    padding: 18,
    marginBottom: 14,
    shadowColor: '#00AA5B',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.06,
    shadowRadius: 8,
    elevation: 2,
    borderWidth: 1,
    borderColor: '#e8f5e9',
  },
  sectionHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 18,
    gap: 8,
  },
  sectionIcon: {
    width: 28,
    height: 28,
    borderRadius: 8,
    backgroundColor: '#e8f5e9',
    justifyContent: 'center',
    alignItems: 'center',
  },
  sectionTitle: { fontSize: 15, fontWeight: '800', color: '#1a1a2e' },

  inputGroup: { marginBottom: 14 },
  labelSimple: { fontSize: 11, fontWeight: '700', color: '#546e7a', marginBottom: 6, textTransform: 'uppercase', letterSpacing: 0.6 },
  inputRow: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#f5faf7',
    borderWidth: 1.5,
    borderColor: '#e0f2ec',
    borderRadius: 12,
    paddingHorizontal: 12,
  },
  inputRowDisabled: {
    backgroundColor: '#f1f5f9',
    borderColor: '#e2e8f0',
  },
  inputIcon: { marginRight: 8 },
  input: { flex: 1, color: '#1a1a2e', paddingVertical: 12, fontSize: 14 },
  textArea: { height: 80, textAlignVertical: 'top' },

  row: { flexDirection: 'row', marginBottom: 14 },
  selectorField: {
    backgroundColor: '#f5faf7',
    borderWidth: 1.5,
    borderColor: '#e0f2ec',
    borderRadius: 12,
    height: 48,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 12,
    gap: 6,
  },
  selectorValueText: { flex: 1, fontSize: 14, color: '#1a1a2e', fontWeight: 'normal' },

  footer: { marginTop: 6, marginBottom: 16 },
  btnSubmit: {
    backgroundColor: '#00AA5B',
    padding: 16,
    borderRadius: 14,
    alignItems: 'center',
    shadowColor: '#00AA5B',
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.3,
    shadowRadius: 10,
    elevation: 6,
  },
  btnInner: { flexDirection: 'row', alignItems: 'center', gap: 8 },
  btnText: { color: '#fff', fontWeight: '800', fontSize: 16 },

  modalOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.4)', justifyContent: 'flex-end' },
  modalSheet: {
    backgroundColor: '#fff',
    borderTopLeftRadius: 24,
    borderTopRightRadius: 24,
    padding: 20,
    maxHeight: '50%',
  },
  modalHandle: {
    width: 40,
    height: 4,
    borderRadius: 2,
    backgroundColor: '#e0f2ec',
    alignSelf: 'center',
    marginBottom: 16,
  },
  modalHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 16 },
  modalClose: {
    width: 32,
    height: 32,
    borderRadius: 16,
    backgroundColor: '#f5faf7',
    justifyContent: 'center',
    alignItems: 'center',
  },
  modalTitle: { fontSize: 16, fontWeight: '800', color: '#1a1a2e' },
  modalItem: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingVertical: 14,
    paddingHorizontal: 12,
    borderRadius: 10,
    marginBottom: 4,
  },
  modalItemSelected: { backgroundColor: '#e8f5e9' },
  modalItemText: { fontSize: 15, color: '#37474f', fontWeight: '600' },
  modalItemTextSelected: { color: '#00AA5B', fontWeight: '700' },
});