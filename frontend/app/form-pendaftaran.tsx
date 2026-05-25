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
  Platform
} from 'react-native';

import { useRouter, useLocalSearchParams } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';
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

  const { id_jadwal, tanggal, waktu } = params;

  const [loading, setLoading] = useState(false);
  const [fetchingData, setFetchingData] = useState(true);

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
    domisili: ''
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
        domisili: u.domisili || ''
      });
    } catch (error) {
      console.log(error);
    } finally {
      setFetchingData(false);
    }
  };

  const handleKirim = async () => {
    if (
      !formData.nama ||
      !formData.no_hp ||
      !formData.email ||
      !formData.alamat
    ) {
      Alert.alert('Perhatian', 'Mohon isi Nama, Nomor HP, Email, dan Alamat.');
      return;
    }

    setLoading(true);

    try {
      const jadwalIdParsed = id_jadwal ? parseInt(id_jadwal as string, 10) : null;
      const response = await axiosInstance.post('/pendaftaran/submit', {
        id_jadwal: jadwalIdParsed,
        nama_klien: formData.nama,
        no_hp: formData.no_hp,
        email: formData.email,
        alamat: formData.alamat,
        tanggal_lahir: formData.tanggal_lahir,
        jenis_kelamin: formData.jenis_kelamin,
        golongan_darah: formData.golongan_darah,
        domisili: formData.domisili,
        institusi: formData.institusi,
        sosmed: formData.sosmed
      });

      if (response.status === 200 || response.status === 201) {
        Alert.alert('Berhasil', 'Pendaftaran berhasil dikirim!');
        setTimeout(() => { router.replace('/riwayat'); }, 300);
      }
    } catch (error: any) {
      console.log("=== ERROR SUBMIT ===");
      if (error.response) {
        console.log("Data Error:", error.response.data);
        console.log("Status Error:", error.response.status);
        Alert.alert('Gagal', error.response.data.message || 'Terjadi kesalahan pada validasi data.');
      } else {
        console.log("Pesan Error:", error.message);
        Alert.alert('Gagal', 'Tidak dapat terhubung ke server.');
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
                label="Tgl Lahir (YYYY-MM-DD)"
                value={formData.tanggal_lahir}
                icon="calendar-outline"
                onChange={(val) => setFormData({ ...formData, tanggal_lahir: val })}
              />
            </View>

            <View style={{ width: 145 }}>
              <Text style={styles.fieldLabel}>Gol. Darah</Text>
              <View style={styles.bloodRow}>
                {['A', 'B', 'AB', 'O'].map((item) => (
                  <TouchableOpacity
                    key={item}
                    style={[styles.bloodBtn, formData.golongan_darah === item && styles.bloodBtnActive]}
                    onPress={() => setFormData({ ...formData, golongan_darah: item })}
                    activeOpacity={0.7}
                  >
                    <Text style={[styles.bloodText, formData.golongan_darah === item && styles.bloodTextActive]}>
                      {item}
                    </Text>
                  </TouchableOpacity>
                ))}
              </View>
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

const CustomInput = ({ label, value, onChange, keyboardType = 'default', multiline = false, placeholder, icon }: CustomInputProps) => (
  <View style={styles.inputGroup}>
    <Text style={styles.fieldLabel}>{label}</Text>
    <View style={styles.inputRow}>
      {icon && <Ionicons name={icon} size={16} color="#00AA5B" style={styles.inputIcon} />}
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
  fieldLabel: { fontSize: 11, fontWeight: '700', color: '#546e7a', marginBottom: 6, textTransform: 'uppercase', letterSpacing: 0.6 },
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