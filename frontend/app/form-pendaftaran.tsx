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
  ActivityIndicator 
} from 'react-native';
import { useRouter, useLocalSearchParams } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

export default function FormPendaftaran() {
  const router = useRouter();
  const params = useLocalSearchParams();
  
  // Mengambil data jadwal yang dipilih dari halaman sebelumnya
  const { id_jadwal, tanggal, waktu } = params;

  // State untuk data user dan form
  const [userName, setUserName] = useState("User");
  const [namaLengkap, setNamaLengkap] = useState('');
  const [nik, setNik] = useState('');
  const [noHp, setNoHp] = useState(''); // State baru untuk Nomor HP
  const [email, setEmail] = useState('');
  const [alamat, setAlamat] = useState('');
  const [loading, setLoading] = useState(false);

  // Ambil nama user yang login hanya untuk sapaan di header
  useEffect(() => {
    const loadUser = async () => {
      const savedName = await AsyncStorage.getItem('user_name');
      if (savedName) setUserName(savedName);
    };
    loadUser();
  }, []);

  const handleKirim = async () => {
    // Validasi input (Semua kolom wajib diisi)
    if (!namaLengkap || !nik || !noHp || !email || !alamat) {
      Alert.alert("Perhatian", "Harap lengkapi semua kolom formulir termasuk Nomor HP.");
      return;
    }

    setLoading(true);
    try {
      // Mengirim data pendaftaran lengkap ke API Laravel
      const response = await axios.post('http://192.168.100.117:8000/api/pendaftaran/submit', {
        id_jadwal: id_jadwal,
        nama_lengkap: namaLengkap,
        nik: nik,
        no_hp: noHp, // Data no_hp sekarang diambil dari input user
        email: email,
        alamat: alamat
      });

      if (response.status === 200) {
        Alert.alert("Berhasil", "Pendaftaran Anda telah berhasil dikirim!", [
          { text: "Selesai", onPress: () => router.replace('/(tabs)') }
        ]);
      }
    } catch (error: any) {
      console.log("Error Detail:", error.response?.data || error.message);
      Alert.alert("Gagal", "Terjadi kesalahan saat mengirim data. Pastikan server Laravel menyala.");
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => router.back()}>
          <Ionicons name="arrow-back-outline" size={28} color="#000" />
        </TouchableOpacity>
        <View style={styles.profileSection}>
          <View style={styles.placeholderImg} />
          <View>
            <Text style={styles.welcomeText}>Halo, {userName}!</Text>
            <Text style={styles.subText}>Lengkapi data pendaftaran Anda</Text>
          </View>
        </View>
      </View>

      <ScrollView contentContainerStyle={styles.content} showsVerticalScrollIndicator={false}>
        {/* Ringkasan Jadwal Terpilih */}
        <View style={styles.infoJadwal}>
          <Ionicons name="calendar" size={18} color="#1e40af" />
          <Text style={styles.infoJadwalText}>
             Jadwal: {tanggal} | {waktu} WIB
          </Text>
        </View>

        {/* Input Nama Lengkap */}
        <View style={styles.inputGroup}>
          <View style={styles.labelBox}><Text style={styles.labelText}>Nama Lengkap</Text></View>
          <TextInput 
            style={styles.input} 
            value={namaLengkap}
            onChangeText={setNamaLengkap}
            placeholder="Masukkan nama lengkap sesuai KTP"
          />
        </View>

        {/* Input NIK */}
        <View style={styles.inputGroup}>
          <View style={styles.labelBox}><Text style={styles.labelText}>Nomor NIK</Text></View>
          <TextInput 
            style={styles.input} 
            value={nik}
            onChangeText={setNik}
            placeholder="Masukkan 16 digit NIK"
            keyboardType="numeric"
          />
        </View>

        {/* BARU: Input Nomor HP */}
        <View style={styles.inputGroup}>
          <View style={styles.labelBox}><Text style={styles.labelText}>Nomor HP</Text></View>
          <TextInput 
            style={styles.input} 
            value={noHp}
            onChangeText={setNoHp}
            placeholder="Contoh: 08123456789"
            keyboardType="phone-pad"
          />
        </View>

        {/* Input Email */}
        <View style={styles.inputGroup}>
          <View style={styles.labelBox}><Text style={styles.labelText}>Email</Text></View>
          <TextInput 
            style={styles.input} 
            value={email}
            onChangeText={setEmail}
            placeholder="contoh@email.com"
            keyboardType="email-address"
            autoCapitalize="none"
          />
        </View>

        {/* Input Alamat */}
        <View style={styles.inputGroup}>
          <View style={styles.labelBox}><Text style={styles.labelText}>Alamat Lengkap</Text></View>
          <TextInput 
            style={[styles.input, styles.textArea]} 
            value={alamat}
            onChangeText={setAlamat}
            placeholder="Alamat domisili saat ini"
            multiline={true}
            numberOfLines={4}
            textAlignVertical="top"
          />
        </View>

        {/* Action Buttons */}
        <View style={styles.footerBtns}>
          <TouchableOpacity style={styles.btnBack} onPress={() => router.back()}>
            <Text style={styles.btnText}>Kembali</Text>
          </TouchableOpacity>
          <TouchableOpacity 
            style={[styles.btnSubmit, loading && { opacity: 0.7 }]} 
            onPress={handleKirim}
            disabled={loading}
          >
            {loading ? <ActivityIndicator color="#000" /> : <Text style={styles.btnText}>Kirim</Text>}
          </TouchableOpacity>
        </View>
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#fff' },
  header: { padding: 20, borderBottomWidth: 1, borderColor: '#e2e8f0' },
  profileSection: { flexDirection: 'row', alignItems: 'center', marginTop: 15 },
  placeholderImg: { width: 60, height: 60, backgroundColor: '#cbd5e1', borderRadius: 10, marginRight: 15 },
  welcomeText: { fontSize: 20, fontWeight: 'bold', color: '#0f172a' },
  subText: { fontSize: 13, color: '#64748b' },
  content: { padding: 20 },
  infoJadwal: { 
    flexDirection: 'row', 
    alignItems: 'center', 
    backgroundColor: '#eff6ff', 
    padding: 12, 
    borderRadius: 8, 
    marginBottom: 20,
    borderWidth: 1,
    borderColor: '#bfdbfe'
  },
  infoJadwalText: { marginLeft: 8, fontSize: 13, fontWeight: '700', color: '#1e40af' },
  inputGroup: { marginBottom: 18 },
  labelBox: { backgroundColor: '#94a3b8', padding: 10, borderTopLeftRadius: 5, borderTopRightRadius: 5 },
  labelText: { fontWeight: 'bold', fontSize: 14, color: '#fff' },
  input: { 
    borderWidth: 1, 
    borderColor: '#94a3b8', 
    padding: 12, 
    fontSize: 15, 
    backgroundColor: '#fff',
    borderBottomLeftRadius: 5,
    borderBottomRightRadius: 5
  },
  textArea: { height: 100 },
  footerBtns: { flexDirection: 'row', justifyContent: 'center', gap: 20, marginTop: 10, marginBottom: 30 },
  btnBack: { backgroundColor: '#e2e8f0', paddingVertical: 12, paddingHorizontal: 35, borderRadius: 25 },
  btnSubmit: { backgroundColor: '#cbd5e1', paddingVertical: 12, paddingHorizontal: 45, borderRadius: 25 },
  btnText: { fontWeight: '700', color: '#1e293b' }
});