import React, { Component } from 'react';
import {
  View,
  Text,
  StyleSheet,
  FlatList,
  SafeAreaView,
  Platform,
  TouchableOpacity,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { router } from 'expo-router';

interface PanduanItem {
  id: number;
  title: string;
  content: string;
  category: string;
  icon: keyof typeof Ionicons.glyphMap;
}

const DATA: PanduanItem[] = [
  {
    id: 1,
    title: 'Mengawali Pendaftaran',
    content: 'Setelah berhasil masuk ke akun Anda, klik spanduk banner utama "Daftarkan diri Anda sekarang" pada halaman Beranda, atau pilih menu "Daftar Tes".',
    category: 'Pendaftaran',
    icon: 'log-in-outline',
  },
  {
    id: 2,
    title: 'Menentukan Lokasi & Wilayah',
    content: 'Pilih cakupan Wilayah & Biaya (Dalam/Luar Subang), serta tentukan Metode Lokasi Pelaksanaan Tes yang diinginkan melalui opsi "Home Visit" atau "Kantor Cabang".',
    category: 'Lokasi',
    icon: 'location-outline',
  },
  {
    id: 3,
    title: 'Memilih Jadwal Pelaksanaan',
    content: 'Silakan klik pada baris slot jadwal berstatus "TERSEDIA" yang Anda kehendaki, pastikan indikator pilihan telah aktif, kemudian klik tombol "Berikutnya".',
    category: 'Jadwal',
    icon: 'calendar-outline',
  },
  {
    id: 4,
    title: 'Verifikasi Data Personal',
    content: 'Periksa kembali akurasi Data Personal Anda (Nama, Nomor WhatsApp, Tanggal Lahir, Golongan Darah). Lakukan perbaikan langsung pada kolom isian jika terdapat kesalahan.',
    category: 'Data Diri',
    icon: 'person-outline',
  },
  {
    id: 5,
    title: 'Penyelesaian Administrasi',
    content: 'Kirimkan dana transfer sebagai DP sebesar 100 Ribu menuju nomor rekening yang tertera di modul Info Pembayaran, unggah lampiran tanda terima lewat tombol "Upload Bukti Transfer", lalu klik "Konfirmasi & Daftar".',
    category: 'Pembayaran',
    icon: 'wallet-outline',
  },
  {
    id: 6,
    title: 'Validasi Berkas Berkala',
    content: 'Pendaftaran Anda akan berstatus "Menunggu" verifikasi dokumen oleh pihak manajemen. Perkembangan keabsahan berkas dapat dipantau di dalam menu "Riwayat Tes".',
    category: 'Verifikasi',
    icon: 'time-outline',
  },
  {
    id: 7,
    title: 'Proses Pelaksanaan Tes Fisik',
    content: 'Ketika verifikasi usai, status beralih menjadi "Diproses". Pengambilan sidik jari dan analisis kecerdasan STIFIn akan dilangsungkan secara tatap muka di luar sistem bersama tim ahli.',
    category: 'Pelaksanaan',
    icon: 'sync-outline',
  },
  {
    id: 8,
    title: 'Unduh Hasil & Sertifikat',
    content: 'Saat status dinyatakan "Selesai", Anda dapat mengakses menu "Hasil Tes" untuk meninjau lembar analisis kecerdasan sekaligus mengunduh Dokumen Sertifikat Resmi Anda.',
    category: 'Hasil',
    icon: 'cloud-download-outline',
  },
];

const renderItem = ({ item, index }: { item: PanduanItem; index: number }) => (
  <View style={styles.card}>
    <View style={styles.leftCol}>
      <View style={styles.badgeNumber}>
        <Text style={styles.badgeText}>{index + 1}</Text>
      </View>
      {index !== DATA.length - 1 && <View style={styles.connectorLine} />}
    </View>
    <View style={styles.cardBody}>
      <View style={styles.cardHeader}>
        <View style={styles.cardIconWrap}>
          <Ionicons name={item.icon} size={18} color="#00AA5B" />
        </View>
        <View style={{ flex: 1 }}>
          <Text style={styles.categoryText}>{item.category.toUpperCase()}</Text>
          <Text style={styles.titleText}>{item.title}</Text>
        </View>
      </View>
      <Text style={styles.contentText}>{item.content}</Text>
    </View>
  </View>
);

export default class PanduanScreen extends Component {
  render() {
    return (
      <SafeAreaView style={styles.container}>

        <View style={styles.topBar}>
          <TouchableOpacity style={styles.backBtn} onPress={() => router.replace('/home')}>
            <Ionicons name="arrow-back" size={22} color="#fff" />
          </TouchableOpacity>
          <View style={styles.topBarCenter}>
            <Text style={styles.topBarTitle}>Panduan Aplikasi</Text>
            <Text style={styles.topBarSub}>Pelajari cara menggunakan aplikasi</Text>
          </View>
          <View style={{ width: 38 }} />
        </View>

        <FlatList
          data={DATA}
          keyExtractor={(item) => item.id.toString()}
          renderItem={renderItem}
          contentContainerStyle={styles.listContainer}
          showsVerticalScrollIndicator={false}
          ListHeaderComponent={
            <View style={styles.introCard}>
              <View style={styles.introIconWrap}>
                <Ionicons name="book-outline" size={22} color="#00AA5B" />
              </View>
              <View style={{ flex: 1 }}>
                <Text style={styles.introTitle}>Panduan Singkat & Edukatif</Text>
                <Text style={styles.introDesc}>
                  Ikuti petunjuk sistematis di bawah ini untuk mendaftarkan diri Anda maupun kerabat dalam pelaksanaan uji kecerdasan genetik STIFIn secara resmi.
                </Text>
              </View>
            </View>
          }
        />

      </SafeAreaView>
    );
  }
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f5faf7' },

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
    width: 38, height: 38, borderRadius: 19,
    backgroundColor: 'rgba(255,255,255,0.2)',
    justifyContent: 'center', alignItems: 'center',
  },
  topBarCenter: { flex: 1, alignItems: 'center' },
  topBarTitle: { fontSize: 18, fontWeight: '800', color: '#fff' },
  topBarSub: { fontSize: 11, color: 'rgba(255,255,255,0.8)', marginTop: 2 },

  listContainer: { padding: 16, paddingBottom: 32 },

  introCard: {
    backgroundColor: '#fff', borderRadius: 16, padding: 16,
    marginBottom: 20, borderWidth: 1, borderColor: '#e8f5e9',
    flexDirection: 'row', alignItems: 'flex-start', gap: 12,
    shadowColor: '#00AA5B', shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05, shadowRadius: 6, elevation: 2,
  },
  introIconWrap: {
    width: 42, height: 42, borderRadius: 10,
    backgroundColor: '#e8f5e9',
    justifyContent: 'center', alignItems: 'center',
  },
  introTitle: { fontSize: 14, fontWeight: '800', color: '#1a1a2e', marginBottom: 5 },
  introDesc: { fontSize: 12, color: '#607d8b', lineHeight: 18 },

  card: {
    flexDirection: 'row',
    marginBottom: 0,
  },
  leftCol: {
    alignItems: 'center',
    width: 28,
    marginRight: 12,
  },
  badgeNumber: {
    width: 26, height: 26, borderRadius: 13,
    backgroundColor: '#00AA5B',
    justifyContent: 'center', alignItems: 'center',
    zIndex: 2,
  },
  badgeText: { fontSize: 12, fontWeight: '800', color: '#fff' },
  connectorLine: {
    width: 2, flex: 1,
    backgroundColor: '#c8e6c9',
    marginVertical: 4,
  },
  cardBody: {
    flex: 1,
    backgroundColor: '#fff',
    borderRadius: 14,
    padding: 14,
    marginBottom: 12,
    borderWidth: 1,
    borderColor: '#e8f5e9',
    shadowColor: '#00AA5B',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 6,
    elevation: 2,
  },
  cardHeader: {
    flexDirection: 'row', alignItems: 'flex-start',
    gap: 10, marginBottom: 8,
  },
  cardIconWrap: {
    width: 36, height: 36, borderRadius: 10,
    backgroundColor: '#e8f5e9',
    justifyContent: 'center', alignItems: 'center',
  },
  categoryText: { fontSize: 10, fontWeight: '700', color: '#00AA5B', marginBottom: 2, letterSpacing: 0.6 },
  titleText: { fontSize: 13, fontWeight: '800', color: '#1a1a2e' },
  contentText: { fontSize: 12, color: '#607d8b', lineHeight: 18 },
});