import React, { useState, useEffect, useRef } from 'react';
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
  StatusBar,
  Modal,
  FlatList,
} from 'react-native';

import { useRouter, useLocalSearchParams } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';
import * as ImagePicker from 'expo-image-picker';
import Toast from 'react-native-toast-message';
import axiosInstance from '@/src/api/axiosConfig';

// ─── Helper format Rupiah ────────────────────────────────────────────────────
function formatRupiah(nominal: number): string {
  return 'Rp ' + nominal.toLocaleString('id-ID');
}
function formatTanggalIndo(tanggal: string): string {
  if (!tanggal) return '—';
  const bulan = ['Januari','Februari','Maret','April','Mei','Juni',
                 'Juli','Agustus','September','Oktober','November','Desember'];
  const [tahun, bln, tgl] = String(tanggal).split('-');
  if (!tahun || !bln || !tgl) return String(tanggal);
  return `${parseInt(tgl)} ${bulan[parseInt(bln) - 1]} ${tahun}`;
}

// ─── Types ───────────────────────────────────────────────────────────────────
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

// Tipe fleksibel: File (web) atau object uri/name/type (mobile)
type BuktiFile = File | { uri: string; name: string; type: string };

// ─── Komponen utama ───────────────────────────────────────────────────────────
export default function FormPendaftaran() {
  const router = useRouter();
  const params = useLocalSearchParams();

  // Params dari pendaftaran.tsx — termasuk biaya & nama_kota
  const {
    id_jadwal,
    tanggal,
    waktu,
    is_luar_subang,
    nama_kota,
    biaya: biayaParam,
  } = params;

  // Konversi biaya ke number (fallback ke 550.000 jika tidak ada)
  const biayaNominal = biayaParam ? parseInt(String(biayaParam), 10) : 550000;
  const isLuarSubang = is_luar_subang === '1';
  const namaKota     = nama_kota ? String(nama_kota) : '';

  const [loading, setLoading]           = useState(false);
  const [fetchingData, setFetchingData] = useState(true);
  const [activePicker, setActivePicker] = useState<'jk' | 'golDarah' | null>(null);

  // State bukti: simpan file asli + URI preview terpisah
  const [bukti, setBukti]                     = useState<BuktiFile | null>(null);
  const [buktiPreviewUri, setBuktiPreviewUri] = useState<string | null>(null);

  // Ref ke input[type=file] tersembunyi (hanya web)
  const fileInputRef = useRef<HTMLInputElement | null>(null);

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

  const [formData, setFormData] = useState<KlienFormData>({
    id_klien:       '',
    nama:           '',
    no_hp:          '',
    tanggal_lahir:  '',
    jenis_kelamin:  'L',
    golongan_darah: '-',
    email:          '',
    alamat:         '',
    institusi:      '',
    sosmed:         '',
    domisili:       '',
  });

  useEffect(() => {
    loadProfileData();

    // Buat input[type=file] tersembunyi khusus web
    if (Platform.OS === 'web') {
      const input = document.createElement('input');
      input.type    = 'file';
      input.accept  = 'image/jpeg,image/png,image/jpg';
      input.style.display = 'none';
      input.onchange = (e: Event) => {
        const target = e.target as HTMLInputElement;
        const file   = target.files?.[0];
        if (file) {
          setBukti(file);
          setBuktiPreviewUri(URL.createObjectURL(file));
        }
      };
      document.body.appendChild(input);
      fileInputRef.current = input;

      return () => {
        document.body.removeChild(input);
      };
    }
  }, []);

  const loadProfileData = async () => {
    try {
      const response = await axiosInstance.get('/profile');
      const u        = response.data;
      setFormData({
        id_klien:       u.id_user        || '',
        nama:           u.nama           || '',
        no_hp:          u.no_hp          || '',
        tanggal_lahir:  u.tanggal_lahir  || '',
        jenis_kelamin:  u.jenis_kelamin  || 'L',
        golongan_darah: u.golongan_darah || '-',
        email:          u.email          || '',
        alamat:         u.alamat         || '',
        institusi:      u.institusi      || '',
        sosmed:         u.sosmed         || '',
        domisili:       u.domisili       || '',
      });
    } catch (error) {
      console.log('Gagal memuat profil:', error);
    } finally {
      setFetchingData(false);
    }
  };

  const pilihFoto = async () => {
    if (Platform.OS === 'web') {
      fileInputRef.current?.click();
      return;
    }

    // Mobile
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
      const asset = result.assets[0];
      setBukti({
        uri:  asset.uri,
        name: asset.fileName || 'bukti_transfer.jpg',
        type: asset.mimeType || 'image/jpeg',
      });
      setBuktiPreviewUri(asset.uri);
    }
  };

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
        text2: 'Upload bukti transfer terlebih dahulu.',
        position: 'top',
      });
      return;
    }

    setLoading(true);
    try {
      const data = new FormData();

      if (Platform.OS === 'web') {
        data.append('file_bukti', bukti as File);
      } else {
        data.append('file_bukti', bukti as any);
      }

      data.append('id_jadwal',      String(id_jadwal));
      data.append('is_luar_subang', isLuarSubang ? '1' : '0');
      data.append('nama_kota',      namaKota);
      data.append('biaya',          String(biayaNominal));
      data.append('nama_klien',     formData.nama);
      data.append('no_hp',          formData.no_hp);
      data.append('email',          formData.email);
      data.append('alamat',         formData.alamat);
      data.append('tanggal_lahir',  formData.tanggal_lahir);
      data.append('jenis_kelamin',  formData.jenis_kelamin);
      data.append('golongan_darah', formData.golongan_darah);
      data.append('domisili',       formData.domisili);
      data.append('institusi',      formData.institusi);
      data.append('sosmed',         formData.sosmed);

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
        setTimeout(() => { router.replace('/riwayat'); }, 1000);
      }
    } catch (error: any) {
      console.log('=== ERROR SUBMIT ===');
      if (error.response) {
        console.log('Data Error:',   error.response.data);
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

  // ─── Loading screen ────────────────────────────────────────────────────────
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

  // ─── Render ────────────────────────────────────────────────────────────────
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
           <Text style={styles.schedValue}>{formatTanggalIndo(String(tanggal))} • {waktu} WIB</Text>
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

          <CustomInput label="Nama Lengkap" value={formData.nama} icon="person-outline"
            onChange={(val) => setFormData({ ...formData, nama: val })} />
          <CustomInput label="Nomor WhatsApp" value={formData.no_hp} keyboardType="phone-pad"
            icon="call-outline" onChange={(val) => setFormData({ ...formData, no_hp: val })} />

          <View style={styles.row}>
            <View style={{ flex: 1, marginRight: 10 }}>
              <CustomInput label="Tgl Lahir (Thn-Bln-Tgl)" value={formData.tanggal_lahir}
                icon="calendar-outline" placeholder="2000-01-31"
                onChange={(val) => setFormData({ ...formData, tanggal_lahir: val })} />
            </View>
            <View style={{ width: 145 }}>
              <Text style={styles.fieldLabel}>Gol. Darah</Text>
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

          <View style={styles.inputGroup}>
            <Text style={styles.fieldLabel}>Jenis Kelamin</Text>
            <TouchableOpacity
              style={styles.selectorField}
              activeOpacity={0.7}
              onPress={() => setActivePicker('jk')}
            >
              <Ionicons
                name={formData.jenis_kelamin === 'L' ? 'male-outline' : 'female-outline'}
                size={16}
                color="#00AA5B"
              />
              <Text style={styles.selectorValueText}>
                {formData.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'}
              </Text>
              <Ionicons name="chevron-down" size={14} color="#90a4ae" />
            </TouchableOpacity>
          </View>

          <CustomInput label="Email Aktif" value={formData.email} keyboardType="email-address"
            icon="mail-outline" onChange={(val) => setFormData({ ...formData, email: val })} />
        </View>

        {/* Section 2: Data Tambahan */}
        <View style={styles.section}>
          <View style={styles.sectionHeader}>
            <View style={styles.sectionNum}><Text style={styles.sectionNumText}>2</Text></View>
            <Text style={styles.sectionTitle}>Data Tambahan</Text>
          </View>

          <CustomInput label="Institusi / Pekerjaan" value={formData.institusi} icon="business-outline"
            onChange={(val) => setFormData({ ...formData, institusi: val })} />
          <CustomInput label="Username Sosmed (FB/IG)" value={formData.sosmed} icon="logo-instagram"
            onChange={(val) => setFormData({ ...formData, sosmed: val })} />
          <CustomInput label="Kota Domisili" value={formData.domisili} icon="map-outline"
            onChange={(val) => setFormData({ ...formData, domisili: val })} />
          <CustomInput label="Alamat Lengkap" value={formData.alamat} multiline icon="location-outline"
            onChange={(val) => setFormData({ ...formData, alamat: val })} />
        </View>

        {/* Section 3: Info Pembayaran & Upload Bukti */}
        <View style={styles.section}>
          <View style={styles.sectionHeader}>
            <View style={styles.sectionNum}><Text style={styles.sectionNumText}>3</Text></View>
            <Text style={styles.sectionTitle}>Info Pembayaran</Text>
          </View>

          {/* Ringkasan biaya — dinamis dari params */}
          <View style={styles.biayaBox}>
            <Ionicons name="cash-outline" size={18} color="#00AA5B" />
            <View style={{ flex: 1 }}>
              <Text style={styles.biayaText}>
                Total Biaya:{' '}
                <Text style={styles.biayaNominal}>{formatRupiah(biayaNominal)}</Text>
              </Text>

              {isLuarSubang && namaKota !== '' && (
                <Text style={styles.biayaKota}>
                  <Ionicons name="location-outline" size={11} color="#546e7a" /> {namaKota}
                </Text>
              )}

              <View style={styles.biayaBadgeRow}>
                <View style={[
                  styles.biayaBadge,
                  { backgroundColor: isLuarSubang ? '#fff3e0' : '#e8f5e9' },
                ]}>
                  <Ionicons
                    name={isLuarSubang ? 'car-outline' : 'business-outline'}
                    size={11}
                    color={isLuarSubang ? '#e65100' : '#2e7d32'}
                  />
                  <Text style={[
                    styles.biayaBadgeText,
                    { color: isLuarSubang ? '#e65100' : '#2e7d32' },
                  ]}>
                    {isLuarSubang ? 'Luar Subang' : 'Dalam Subang'}
                  </Text>
                </View>
              </View>

              <Text style={styles.biayaNote}>* Belum termasuk biaya admin antar bank</Text>
            </View>
          </View>

          {/* Info rekening DP */}
          <View style={styles.rekeningBox}>
            <Text style={styles.rekeningTitle}>
              Transfer DP sebesar{' '}
              <Text style={{ color: '#00AA5B', fontWeight: '800' }}>Rp 100.000</Text>{' '}
              ke rekening berikut:
            </Text>
            <Text style={styles.rekeningItem}>• BSI: 7331440196 An. Ayik Yulia Rn</Text>
          </View>

          {/* Preview gambar setelah dipilih */}
          {buktiPreviewUri && (
            <View style={styles.previewBox}>
              <Image source={{ uri: buktiPreviewUri }} style={styles.previewImg} resizeMode="cover" />
              <Text style={styles.previewLabel}>✓ Bukti terpilih</Text>
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

      {/* Custom Picker Modal */}
      <Modal
        visible={activePicker !== null}
        transparent
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
                const isSelected =
                  (activePicker === 'jk'
                    ? formData.jenis_kelamin
                    : formData.golongan_darah) === item.value;
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

    </SafeAreaView>
  );
}

// ─── CustomInput ──────────────────────────────────────────────────────────────
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

// ─── Styles ───────────────────────────────────────────────────────────────────
const styles = StyleSheet.create({
  container:   { flex: 1, backgroundColor: '#f5faf7' },
  center:      { flex: 1, justifyContent: 'center', alignItems: 'center', backgroundColor: '#f5faf7' },
  loadingCard: { alignItems: 'center', gap: 12 },
  loadingText: { fontSize: 14, color: '#546e7a', fontWeight: '600' },

  topBar: {
    backgroundColor: '#00AA5B',
    paddingTop: Platform.OS === 'android'
      ? (StatusBar.currentHeight ? StatusBar.currentHeight + 12 : 30)
      : 16,
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
  topBarTitle:  { fontSize: 18, fontWeight: '800', color: '#fff' },
  topBarSub:    { fontSize: 11, color: 'rgba(255,255,255,0.8)', marginTop: 2 },

  content: { padding: 16, paddingBottom: 48 },

  schedBanner: {
    backgroundColor: '#00AA5B', borderRadius: 16, padding: 16,
    flexDirection: 'row', alignItems: 'center', gap: 12, marginBottom: 16,
    shadowColor: '#00AA5B', shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.25, shadowRadius: 8, elevation: 5,
  },
  schedIconWrap: {
    width: 44, height: 44, borderRadius: 12,
    backgroundColor: 'rgba(255,255,255,0.2)',
    justifyContent: 'center', alignItems: 'center',
  },
  schedInfo:  { flex: 1 },
  schedLabel: { fontSize: 11, color: 'rgba(255,255,255,0.8)', fontWeight: '600', marginBottom: 3 },
  schedValue: { fontSize: 15, fontWeight: '800', color: '#fff' },
  schedCheck: {},

  section: {
    backgroundColor: '#fff', borderRadius: 18, padding: 18, marginBottom: 14,
    borderWidth: 1, borderColor: '#e8f5e9',
    shadowColor: '#00AA5B', shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05, shadowRadius: 6, elevation: 2,
  },
  sectionHeader: { flexDirection: 'row', alignItems: 'center', marginBottom: 18, gap: 10 },
  sectionNum: {
    width: 26, height: 26, borderRadius: 13, backgroundColor: '#00AA5B',
    justifyContent: 'center', alignItems: 'center',
  },
  sectionNumText: { color: '#fff', fontSize: 12, fontWeight: '800' },
  sectionTitle:   { fontSize: 15, fontWeight: '800', color: '#1a1a2e' },

  inputGroup: { marginBottom: 14 },
  fieldLabel: {
    fontSize: 11, fontWeight: '700', color: '#546e7a', marginBottom: 6,
    textTransform: 'uppercase', letterSpacing: 0.6,
  },
  inputRow: {
    flexDirection: 'row', alignItems: 'center',
    backgroundColor: '#f5faf7', borderWidth: 1.5, borderColor: '#e0f2ec',
    borderRadius: 12, paddingHorizontal: 12,
  },
  inputIcon: { marginRight: 8 },
  input:     { flex: 1, color: '#1a1a2e', paddingVertical: 12, fontSize: 14 },
  textArea:  { height: 80, textAlignVertical: 'top' },

  row: { flexDirection: 'row', alignItems: 'flex-start', marginBottom: 14 },

  selectorField: {
    backgroundColor: '#f5faf7',
    borderWidth: 1.5,
    borderColor: '#e0f2ec',
    borderRadius: 12,
    height: 48,
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 12,
    gap: 6,
  },
  selectorValueText: { flex: 1, fontSize: 14, color: '#1a1a2e' },

  biayaBox: {
    flexDirection: 'row', alignItems: 'flex-start', gap: 10,
    backgroundColor: '#e8f5e9', borderRadius: 12, padding: 14, marginBottom: 12,
  },
  biayaText:     { fontSize: 14, color: '#37474f', fontWeight: '600' },
  biayaNominal:  { fontWeight: '800', color: '#00AA5B', fontSize: 15 },
  biayaKota:     { fontSize: 11, color: '#546e7a', marginTop: 3 },
  biayaBadgeRow: { flexDirection: 'row', marginTop: 6 },
  biayaBadge: {
    flexDirection: 'row', alignItems: 'center', gap: 4,
    paddingHorizontal: 8, paddingVertical: 3, borderRadius: 8,
  },
  biayaBadgeText: { fontSize: 10, fontWeight: '700' },
  biayaNote:      { fontSize: 10, color: '#90a4ae', fontStyle: 'italic', marginTop: 6 , fontWeight: '600'},

  rekeningBox: {
    backgroundColor: '#f5faf7', borderRadius: 10, padding: 12, marginBottom: 14,
    borderWidth: 1, borderColor: '#e0f2ec',
  },
  rekeningTitle: { fontSize: 12, fontWeight: '700', color: '#546e7a', marginBottom: 6 },
  rekeningItem:  { fontSize: 13, color: '#37474f', marginBottom: 4, lineHeight: 20, fontWeight: '600' },

  previewBox:   { alignItems: 'center', marginBottom: 10 },
  previewImg:   { width: '100%', height: 160, borderRadius: 10, marginBottom: 6 },
  previewLabel: { fontSize: 11, color: '#00AA5B', fontWeight: '700' },

  uploadBtn: {
    flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 8,
    borderWidth: 1.5, borderColor: '#e0f2ec', borderStyle: 'dashed',
    borderRadius: 12, paddingVertical: 14, backgroundColor: '#f5faf7',
  },
  uploadBtnText: { fontSize: 14, fontWeight: '700', color: '#546e7a' },

  footer: { marginTop: 6, marginBottom: 16 },
  btnSubmit: {
    backgroundColor: '#00AA5B', padding: 16, borderRadius: 14, alignItems: 'center',
    shadowColor: '#00AA5B', shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.3, shadowRadius: 10, elevation: 6,
  },
  btnInner: { flexDirection: 'row', alignItems: 'center', gap: 8 },
  btnText:  { color: '#fff', fontWeight: '800', fontSize: 16 },

  // ─── Modal styles ─────────────────────────────────────────────────────────
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.4)',
    justifyContent: 'flex-end',
  },
  modalSheet: {
    backgroundColor: '#fff',
    borderTopLeftRadius: 24,
    borderTopRightRadius: 24,
    padding: 20,
    maxHeight: '50%',
  },
  modalHandle: {
    width: 40, height: 4, borderRadius: 2,
    backgroundColor: '#e0f2ec', alignSelf: 'center', marginBottom: 16,
  },
  modalHeader: {
    flexDirection: 'row', justifyContent: 'space-between',
    alignItems: 'center', marginBottom: 16,
  },
  modalClose: {
    width: 32, height: 32, borderRadius: 16,
    backgroundColor: '#f5faf7', justifyContent: 'center', alignItems: 'center',
  },
  modalTitle:            { fontSize: 16, fontWeight: '800', color: '#1a1a2e' },
  modalItem: {
    flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center',
    paddingVertical: 14, paddingHorizontal: 12, borderRadius: 10, marginBottom: 4,
  },
  modalItemSelected:     { backgroundColor: '#e8f5e9' },
  modalItemText:         { fontSize: 15, color: '#37474f', fontWeight: '600' },
  modalItemTextSelected: { color: '#00AA5B', fontWeight: '700' },
});