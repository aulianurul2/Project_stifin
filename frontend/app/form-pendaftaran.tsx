import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  SafeAreaView,
  TextInput,
  TouchableOpacity,
  ScrollView,
  ActivityIndicator,
  KeyboardTypeOptions,
  Platform,
  Image,
} from 'react-native';

import { useRouter, useLocalSearchParams } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';
import * as ImagePicker from 'expo-image-picker';
import Toast from 'react-native-toast-message'; 
import axiosInstance from '@/src/api/axiosConfig';


interface KlienFormData {
  id_klien: string;
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

interface CustomInputProps {
  label: string;
  value: string;
  onChange: (val: string) => void;
  keyboardType?: KeyboardTypeOptions;
  multiline?: boolean;
  placeholder?: string;
  icon?: keyof typeof Ionicons.glyphMap;
}

export default function FormPendaftaran() {
  const router = useRouter();
  const params = useLocalSearchParams();

  // Terima semua params dari halaman pendaftaran
  const { id_jadwal, tanggal, waktu, is_luar_subang } = params;

  const [loading, setLoading] = useState(false);
  const [fetchingData, setFetchingData] = useState(true);

  // State untuk bukti transfer
  const [bukti, setBukti] = useState<ImagePicker.ImagePickerAsset | null>(null);

  const [formData, setFormData] = useState<KlienFormData>({
    id_klien: '',
    nama: '',
    no_hp: '',
    tanggal_lahir: '',
    jenis_kelamin: 'L',
    golongan_darah: '-',
    email: '',
    alamat: '',
    institusi: '',
    sosmed: '',
    domisili: '',
  });

  useEffect(() => {
    loadProfileData();
  }, []);

  const loadProfileData = async () => {
    try {
      const response = await axiosInstance.get('/profile');
      const u = response.data;

      setFormData({
        id_klien: u.id_user || '',
        nama: u.nama || '',
        no_hp: u.no_hp || '',
        tanggal_lahir: u.tanggal_lahir || '',
        jenis_kelamin: u.jenis_kelamin || 'L',
        golongan_darah: u.golongan_darah || '-',
        email: u.email || '',
        alamat: u.alamat || '',
        institusi: u.institusi || '',
        sosmed: u.sosmed || '',
        domisili: u.domisili || '',
      });
    } catch (error) {
      console.log('Gagal memuat profil:', error);
    } finally {
      setFetchingData(false);
    }
  };

// Fungsi pilih foto dari galeri
  const pilihFoto = async () => {
    const permission = await ImagePicker.requestMediaLibraryPermissionsAsync();
    if (!permission.granted) {
      Toast.show({
        type: 'error',
        text1: 'Izin Ditolak',
        text2: 'Izin akses galeri diperlukan untuk upload bukti transfer.',
        position: 'top',
      });
      return;
    }

    const result = await ImagePicker.launchImageLibraryAsync({
      mediaTypes: ImagePicker.MediaTypeOptions.Images,
      allowsEditing: true,
      quality: 0.8,
    });

    if (!result.canceled && result.assets.length > 0) {
      setBukti(result.assets[0]);
    }
  };

  // Fungsi submit pendaftaran
  const handleKirim = async () => {
    if (!formData.nama || !formData.no_hp || !formData.email || !formData.alamat) {
      Toast.show({
        type: 'error',
        text1: 'Data Tidak Lengkap',
        text2: 'Mohon isi Nama, Nomor HP, Email, dan Alamat.',
        position: 'top',
      });
      return;
    }

    if (!bukti) {
      Toast.show({
        type: 'error',
        text1: 'Bukti Transfer Diperlukan',
        text2: 'Mohon upload bukti transfer terlebih dahulu.',
        position: 'top',
      });
      return;
    }

    setLoading(true);

    try {
      const data = new FormData();

      data.append('file_bukti', {
        uri: bukti.uri,
        name: bukti.fileName || 'bukti_transfer.jpg',
        type: bukti.mimeType || 'image/jpeg',
      } as any);

      data.append('id_jadwal', String(id_jadwal));
      data.append('is_luar_subang', is_luar_subang === '1' ? '1' : '0');
      data.append('nama_klien', formData.nama);
      data.append('no_hp', formData.no_hp);
      data.append('email', formData.email);
      data.append('alamat', formData.alamat);
      data.append('tanggal_lahir', formData.tanggal_lahir);
      data.append('jenis_kelamin', formData.jenis_kelamin);
      data.append('golongan_darah', formData.golongan_darah);
      data.append('domisili', formData.domisili);
      data.append('institusi', formData.institusi);
      data.append('sosmed', formData.sosmed);

      const response = await axiosInstance.post('/pendaftaran/submit', data, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });

      if (response.status === 200 || response.status === 201) {
        Toast.show({
          type: 'success',
          text1: 'Pendaftaran Berhasil',
          text2: 'Data Anda telah berhasil dikirim!',
          position: 'top',
          visibilityTime: 2000,
        });

        setTimeout(() => {
          router.replace('/riwayat');
        }, 1000);
      }
    } catch (error: any) {
      console.log('=== ERROR SUBMIT ===');
      if (error.response) {
        console.log('Data Error:', error.response.data);
        console.log('Status Error:', error.response.status);
        Toast.show({
          type: 'error',
          text1: 'Pendaftaran Gagal',
          text2: error.response.data.message || 'Terjadi kesalahan pada validasi data.',
          position: 'top',
        });
      } else {
        console.log('Pesan Error:', error.message);
        Toast.show({
          type: 'error',
          text1: 'Koneksi Gagal',
          text2: 'Tidak dapat terhubung ke server.',
          position: 'top',
        });
      }
    } finally {
      setLoading(false);
    }
  };

  if (fetchingData) {
    return (
      <View style={styles.center}>
        <View style={styles.loadingCard}>
          <ActivityIndicator size="large" color="#00AA5B" />
          <Text style={styles.loadingText}>Menyiapkan formulir...</Text>
        </View>
      </View>
    );
  }

  return (
    <SafeAreaView style={styles.container}>

      {/* Green Top Bar */}
      <View style={styles.topBar}>
        <TouchableOpacity style={styles.backBtn} onPress={() => router.back()}>
          <Ionicons name="arrow-back" size={22} color="#fff" />
        </TouchableOpacity>
        <View style={styles.topBarCenter}>
          <Text style={styles.topBarTitle}>Konfirmasi Data</Text>
          <Text style={styles.topBarSub}>Periksa dan lengkapi data Anda</Text>
        </View>
        <View style={{ width: 38 }} />
      </View>

      <ScrollView contentContainerStyle={styles.content} showsVerticalScrollIndicator={false}>

        {/* Schedule Banner */}
        <View style={styles.schedBanner}>
          <View style={styles.schedIconWrap}>
            <Ionicons name="calendar" size={22} color="#fff" />
          </View>
          <View style={styles.schedInfo}>
            <Text style={styles.schedLabel}>Jadwal Tes Terpilih</Text>
            <Text style={styles.schedValue}>{tanggal} • {waktu} WIB</Text>
          </View>
          <View style={styles.schedCheck}>
            <Ionicons name="checkmark-circle" size={20} color="rgba(255,255,255,0.8)" />
          </View>
        </View>

        {/* Section 1: Data Personal */}
        <View style={styles.section}>
          <View style={styles.sectionHeader}>
            <View style={styles.sectionNum}><Text style={styles.sectionNumText}>1</Text></View>
            <Text style={styles.sectionTitle}>Data Personal</Text>
          </View>

          <CustomInput
            label="Nama Lengkap"
            value={formData.nama}
            icon="person-outline"
            onChange={(val) => setFormData({ ...formData, nama: val })}
          />

          <CustomInput
            label="Nomor WhatsApp"
            value={formData.no_hp}
            keyboardType="phone-pad"
            icon="call-outline"
            onChange={(val) => setFormData({ ...formData, no_hp: val })}
          />

          {/* Tgl Lahir + Goldar */}
          <View style={styles.row}>
            <View style={{ flex: 1, marginRight: 10 }}>
              <CustomInput
                label="Tgl Lahir (Thn-Bln-Tgl)"
                value={formData.tanggal_lahir}
                icon="calendar-outline"
                placeholder="2000-01-31"
                onChange={(val) => setFormData({ ...formData, tanggal_lahir: val })}
              />
            </View>

            <View style={{ width: 145 }}>
              <Text style={styles.fieldLabel}>Gol. Darah</Text>
              <View style={styles.bloodRow}>
                {['A', 'B', 'AB', 'O'].map((item) => (
                  <TouchableOpacity
                    key={item}
                    style={[
                      styles.bloodBtn,
                      formData.golongan_darah === item && styles.bloodBtnActive,
                    ]}
                    onPress={() => setFormData({ ...formData, golongan_darah: item })}
                    activeOpacity={0.7}
                  >
                    <Text
                      style={[
                        styles.bloodText,
                        formData.golongan_darah === item && styles.bloodTextActive,
                      ]}
                    >
                      {item}
                    </Text>
                  </TouchableOpacity>
                ))}
              </View>
            </View>
          </View>

          {/* Jenis Kelamin */}
          <View style={styles.inputGroup}>
            <Text style={styles.fieldLabel}>Jenis Kelamin</Text>
            <View style={styles.bloodRow}>
              {[{ label: 'Laki-laki', val: 'L' }, { label: 'Perempuan', val: 'P' }].map((item) => (
                <TouchableOpacity
                  key={item.val}
                  style={[
                    styles.genderBtn,
                    formData.jenis_kelamin === item.val && styles.bloodBtnActive,
                  ]}
                  onPress={() => setFormData({ ...formData, jenis_kelamin: item.val })}
                  activeOpacity={0.7}
                >
                  <Text
                    style={[
                      styles.bloodText,
                      formData.jenis_kelamin === item.val && styles.bloodTextActive,
                    ]}
                  >
                    {item.label}
                  </Text>
                </TouchableOpacity>
              ))}
            </View>
          </View>

          <CustomInput
            label="Email Aktif"
            value={formData.email}
            keyboardType="email-address"
            icon="mail-outline"
            onChange={(val) => setFormData({ ...formData, email: val })}
          />
        </View>

        {/* Section 2: Data Tambahan */}
        <View style={styles.section}>
          <View style={styles.sectionHeader}>
            <View style={styles.sectionNum}><Text style={styles.sectionNumText}>2</Text></View>
            <Text style={styles.sectionTitle}>Data Tambahan</Text>
          </View>

          <CustomInput
            label="Institusi / Pekerjaan"
            value={formData.institusi}
            icon="business-outline"
            onChange={(val) => setFormData({ ...formData, institusi: val })}
          />

          <CustomInput
            label="Username Sosmed (FB/IG)"
            value={formData.sosmed}
            icon="logo-instagram"
            onChange={(val) => setFormData({ ...formData, sosmed: val })}
          />

          <CustomInput
            label="Kota Domisili"
            value={formData.domisili}
            icon="map-outline"
            onChange={(val) => setFormData({ ...formData, domisili: val })}
          />

          <CustomInput
            label="Alamat Lengkap"
            value={formData.alamat}
            multiline
            icon="location-outline"
            onChange={(val) => setFormData({ ...formData, alamat: val })}
          />
        </View>

        {/* Section 3: Info Pembayaran & Upload Bukti */}
        <View style={styles.section}>
          <View style={styles.sectionHeader}>
            <View style={styles.sectionNum}><Text style={styles.sectionNumText}>3</Text></View>
            <Text style={styles.sectionTitle}>Info Pembayaran</Text>
          </View>

          {/* Biaya ditentukan dari params is_luar_subang */}
          <View style={styles.biayaBox}>
            <Ionicons name="cash-outline" size={18} color="#00AA5B" />
            <Text style={styles.biayaText}>
              Total Biaya:{' '}
              <Text style={styles.biayaNominal}>
                {is_luar_subang === '1' ? 'Rp 650.000' : 'Rp 550.000'}
              </Text>
            </Text>
          </View>

          <View style={styles.rekeningBox}>
            <Text style={styles.rekeningTitle}>Transfer DP ke salah satu rekening:</Text>
            <Text style={styles.rekeningItem}>• BCA: 1234567890 a.n Calvin</Text>
            <Text style={styles.rekeningItem}>• BRI: 0987654321 a.n Calvin</Text>
            <Text style={styles.rekeningItem}>• Danamon: 1122334455 a.n Calvin</Text>
          </View>

          {/* Preview foto jika sudah dipilih */}
          {bukti && (
            <View style={styles.previewBox}>
              <Image source={{ uri: bukti.uri }} style={styles.previewImg} resizeMode="cover" />
              <Text style={styles.previewLabel}>Bukti terpilih</Text>
            </View>
          )}

          <TouchableOpacity style={styles.uploadBtn} onPress={pilihFoto} activeOpacity={0.8}>
            <Ionicons
              name={bukti ? 'checkmark-circle-outline' : 'cloud-upload-outline'}
              size={18}
              color={bukti ? '#00AA5B' : '#546e7a'}
            />
            <Text style={[styles.uploadBtnText, bukti && { color: '#00AA5B' }]}>
              {bukti ? 'Ganti Bukti Transfer' : 'Upload Bukti Transfer'}
            </Text>
          </TouchableOpacity>
        </View>

        {/* Submit Button */}
        <View style={styles.footer}>
          <TouchableOpacity
            style={[styles.btnSubmit, loading && { opacity: 0.7 }]}
            onPress={handleKirim}
            disabled={loading}
            activeOpacity={0.85}
          >
            {loading ? (
              <ActivityIndicator color="#fff" />
            ) : (
              <View style={styles.btnInner}>
                <Ionicons name="send-outline" size={18} color="#fff" />
                <Text style={styles.btnText}>Konfirmasi & Daftar</Text>
              </View>
            )}
          </TouchableOpacity>
        </View>

      </ScrollView>
    </SafeAreaView>
  );
}

const CustomInput = ({
  label,
  value,
  onChange,
  keyboardType = 'default',
  multiline = false,
  placeholder,
  icon,
}: CustomInputProps) => (
  <View style={styles.inputGroup}>
    <Text style={styles.fieldLabel}>{label}</Text>
    <View style={styles.inputRow}>
      {icon && (
        <Ionicons name={icon} size={16} color="#00AA5B" style={styles.inputIcon} />
      )}
      <TextInput
        style={[styles.input, multiline && styles.textArea]}
        value={value}
        onChangeText={onChange}
        keyboardType={keyboardType}
        multiline={multiline}
        placeholder={placeholder || `Masukkan ${label.toLowerCase()}`}
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
    paddingTop: Platform.OS === 'ios' ? 0 : 10,
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

  schedBanner: {
    backgroundColor: '#00AA5B',
    borderRadius: 16,
    padding: 16,
    flexDirection: 'row',
    alignItems: 'center',
    gap: 12,
    marginBottom: 16,
    shadowColor: '#00AA5B',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.25,
    shadowRadius: 8,
    elevation: 5,
  },
  schedIconWrap: {
    width: 44,
    height: 44,
    borderRadius: 12,
    backgroundColor: 'rgba(255,255,255,0.2)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  schedInfo: { flex: 1 },
  schedLabel: { fontSize: 11, color: 'rgba(255,255,255,0.8)', fontWeight: '600', marginBottom: 3 },
  schedValue: { fontSize: 15, fontWeight: '800', color: '#fff' },
  schedCheck: {},

  section: {
    backgroundColor: '#fff',
    borderRadius: 18,
    padding: 18,
    marginBottom: 14,
    borderWidth: 1,
    borderColor: '#e8f5e9',
    shadowColor: '#00AA5B',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 6,
    elevation: 2,
  },
  sectionHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 18,
    gap: 10,
  },
  sectionNum: {
    width: 26,
    height: 26,
    borderRadius: 13,
    backgroundColor: '#00AA5B',
    justifyContent: 'center',
    alignItems: 'center',
  },
  sectionNumText: { color: '#fff', fontSize: 12, fontWeight: '800' },
  sectionTitle: { fontSize: 15, fontWeight: '800', color: '#1a1a2e' },

  inputGroup: { marginBottom: 14 },
  fieldLabel: {
    fontSize: 11,
    fontWeight: '700',
    color: '#546e7a',
    marginBottom: 6,
    textTransform: 'uppercase',
    letterSpacing: 0.6,
  },
  inputRow: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#f5faf7',
    borderWidth: 1.5,
    borderColor: '#e0f2ec',
    borderRadius: 12,
    paddingHorizontal: 12,
  },
  inputIcon: { marginRight: 8 },
  input: { flex: 1, color: '#1a1a2e', paddingVertical: 12, fontSize: 14 },
  textArea: { height: 80, textAlignVertical: 'top' },

  row: { flexDirection: 'row', alignItems: 'flex-start', marginBottom: 14 },

  bloodRow: { flexDirection: 'row', flexWrap: 'wrap', gap: 6, marginTop: 6 },
  bloodBtn: {
    paddingVertical: 8,
    paddingHorizontal: 12,
    borderRadius: 10,
    backgroundColor: '#f5faf7',
    borderWidth: 1.5,
    borderColor: '#e0f2ec',
  },
  bloodBtnActive: { backgroundColor: '#00AA5B', borderColor: '#00AA5B' },
  bloodText: { color: '#546e7a', fontWeight: '700', fontSize: 12 },
  bloodTextActive: { color: '#fff' },

  genderBtn: {
    flex: 1,
    paddingVertical: 10,
    paddingHorizontal: 12,
    borderRadius: 10,
    backgroundColor: '#f5faf7',
    borderWidth: 1.5,
    borderColor: '#e0f2ec',
    alignItems: 'center',
  },

  // Section 3: Pembayaran
  biayaBox: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    backgroundColor: '#e8f5e9',
    borderRadius: 10,
    padding: 12,
    marginBottom: 12,
  },
  biayaText: { fontSize: 14, color: '#37474f', fontWeight: '600' },
  biayaNominal: { fontWeight: '800', color: '#00AA5B', fontSize: 15 },

  rekeningBox: {
    backgroundColor: '#f5faf7',
    borderRadius: 10,
    padding: 12,
    marginBottom: 14,
    borderWidth: 1,
    borderColor: '#e0f2ec',
  },
  rekeningTitle: { fontSize: 12, fontWeight: '700', color: '#546e7a', marginBottom: 6 },
  rekeningItem: { fontSize: 13, color: '#37474f', marginBottom: 4, lineHeight: 20 },

  previewBox: { alignItems: 'center', marginBottom: 10 },
  previewImg: { width: '100%', height: 160, borderRadius: 10, marginBottom: 6 },
  previewLabel: { fontSize: 11, color: '#00AA5B', fontWeight: '700' },

  uploadBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 8,
    borderWidth: 1.5,
    borderColor: '#e0f2ec',
    borderStyle: 'dashed',
    borderRadius: 12,
    paddingVertical: 14,
    backgroundColor: '#f5faf7',
  },
  uploadBtnText: { fontSize: 14, fontWeight: '700', color: '#546e7a' },

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
});