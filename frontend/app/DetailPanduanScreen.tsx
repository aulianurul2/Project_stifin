import React, { Component } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  SafeAreaView
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { router } from 'expo-router';

interface Props {
  navigation: any;
}

interface State {
  steps: {
    id: number;
    title: string;
    desc: string;
    icon: string;
  }[];
}

class DetailPanduanScreen extends Component<Props, State> {
  constructor(props: Props) {
    super(props);
    this.state = {
      steps: [
        { id: 1, title: 'Mengawali Pendaftaran', desc: 'Setelah berhasil masuk ke akun Anda, klik spanduk banner utama "Daftarkan diri Anda sekarang" pada halaman Beranda, atau pilih menu "Daftar Tes".', icon: 'log-in-outline' },
        { id: 2, title: 'Menentukan Lokasi & Wilayah', desc: 'Pilih cakupan Wilayah & Biaya (Dalam/Luar Subang), serta tentukan Metode Lokasi Pelaksanaan Tes yang diinginkan melalui opsi "Home Visit" atau "Kantor Cabang".', icon: 'location-outline' },
        { id: 3, title: 'Memilih Jadwal Pelaksanaan', desc: 'Silakan klik pada baris slot jadwal berstatus "TERSEDIA" yang Anda kehendaki, pastikan indikator pilihan telah aktif, kemudian klik tombol "Berikutnya".', icon: 'calendar-outline' },
        { id: 4, title: 'Verifikasi Data Personal', desc: 'Periksa kembali akurasi Data Personal Anda (Nama, WhatsApp, Tanggal Lahir, Golongan Darah). Lakukan perbaikan langsung pada kolom isian jika terdapat kesalahan.', icon: 'person-outline' },
        { id: 5, title: 'Penyelesaian Administrasi', desc: 'Kirimkan dana transfer menuju nomor rekening yang tertera di modul Info Pembayaran, unggah lampiran tanda terima lewat tombol "Upload Bukti Transfer", lalu klik "Konfirmasi & Daftar".', icon: 'wallet-outline' },
        { id: 6, title: 'Validasi Berkas Berkala', desc: 'Pendaftaran Anda akan berstatus "Menunggu" verifikasi dokumen oleh pihak manajemen. Perkembangan keabsahan berkas dapat dipantau di dalam menu "Riwayat Tes".', icon: 'time-outline' },
        { id: 7, title: 'Proses Pelaksanaan Tes Fisik', desc: 'Ketika verifikasi usai, status beralih menjadi "Diproses". Pengambilan sidik jari dan analisis kecerdasan STIFIn akan dilangsungkan secara tatap muka di luar sistem bersama tim ahli.', icon: 'sync-outline' },
        { id: 8, title: 'Unduh Hasil & Sertifikat', desc: 'Saat status dinyatakan "Selesai", Anda dapat mengakses menu "Hasil Tes" untuk meninjau lembar analisis kecerdasan sekaligus mengunduh Dokumen Sertifikat Resmi Anda.', icon: 'cloud-download-outline' }
      ]
    };
  }

  render() {
    return (
      <SafeAreaView style={styles.container}>
        <View style={styles.header}>
          <TouchableOpacity onPress={() => router.back()} style={styles.backButton}>
            <Ionicons name="arrow-back" size={24} color="#1a1a2e" />
          </TouchableOpacity>
          <Text style={styles.headerTitle}>Alur Pendaftaran Tes</Text>
        </View>

        <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
          <View style={styles.introCard}>
            <Text style={styles.introTitle}>Panduan Singkat & Edukatif</Text>
            <Text style={styles.introDesc}>
              Ikuti petunjuk sistematis di bawah ini untuk mendaftarkan diri Anda maupun kerabat dalam pelaksanaan uji kecerdasan genetik STIFIn secara resmi.
            </Text>
          </View>

          {this.state.steps.map((item, index) => (
            <View key={item.id} style={styles.stepCard}>
              <View style={styles.stepHeaderRow}>
                <View style={styles.badgeNumber}>
                  <Text style={styles.badgeText}>{item.id}</Text>
                </View>
                <View style={styles.iconCircle}>
                  <Ionicons name={item.icon as any} size={20} color="#30b9d4" />
                </View>
                <Text style={styles.stepTitleText}>{item.title}</Text>
              </View>

              <View style={styles.bodyRow}>
                {index !== this.state.steps.length - 1 && <View style={styles.connectorLine} />}
                <Text style={styles.stepDescText}>{item.desc}</Text>
              </View>
            </View>
          ))}
        </ScrollView>
      </SafeAreaView>
    );
  }
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f5faf7' },
  header: { flexDirection: 'row', alignItems: 'center', paddingHorizontal: 16, paddingVertical: 14, backgroundColor: '#ffffff', borderBottomWidth: 1, borderBottomColor: '#e8f5e9' },
  backButton: { padding: 4, marginRight: 12 },
  headerTitle: { fontSize: 18, fontWeight: '800', color: '#1a1a2e' },
  scrollContent: { padding: 16, paddingBottom: 32 },
  introCard: { backgroundColor: '#ffffff', borderRadius: 16, padding: 18, marginBottom: 20, borderWidth: 1, borderColor: '#e8f5e9' },
  introTitle: { fontSize: 15, fontWeight: '800', color: '#1a1a2e', marginBottom: 6 },
  introDesc: { fontSize: 13, color: '#607d8b', lineHeight: 18 },
  stepCard: { backgroundColor: 'transparent', marginBottom: 2 },
  stepHeaderRow: { flexDirection: 'row', alignItems: 'center' },
  badgeNumber: { width: 24, height: 24, borderRadius: 12, backgroundColor: '#30b9d4', justifyContent: 'center', alignItems: 'center', zIndex: 2 },
  badgeText: { fontSize: 12, fontWeight: '800', color: '#ffffff' },
  iconCircle: { width: 36, height: 36, borderRadius: 10, backgroundColor: '#e6f7fa', justifyContent: 'center', alignItems: 'center', marginLeft: 12 },
  stepTitleText: { fontSize: 14, fontWeight: '800', color: '#1a1a2e', marginLeft: 12, flex: 1 },
  bodyRow: { flexDirection: 'row', paddingLeft: 11, paddingTop: 6, paddingBottom: 16 },
  connectorLine: { width: 2, backgroundColor: '#cbd5e0', position: 'absolute', top: 0, bottom: 0, left: 11 },
  stepDescText: { fontSize: 13, color: '#475569', lineHeight: 19, paddingLeft: 49, flex: 1 },
});

export default DetailPanduanScreen;