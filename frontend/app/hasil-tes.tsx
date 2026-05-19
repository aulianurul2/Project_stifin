import React from 'react';
import {
  View,
  Text,
  StyleSheet,
  SafeAreaView,
  TouchableOpacity,
  Alert,
  Platform,
  ViewStyle,
  ScrollView
} from 'react-native';
import { useLocalSearchParams, useRouter } from 'expo-router';
import * as FileSystem from 'expo-file-system/legacy';
import * as Sharing from 'expo-sharing';
import Ionicons from '@expo/vector-icons/Ionicons';

export default function HasilTes() {
  const router = useRouter();
  
  const { tanggal, jam, file_hasil, file_detail, status, komentar } = useLocalSearchParams<{
    tanggal: string;
    jam: string;
    file_hasil: string;
    file_detail: string;
    status: string;
    komentar: string;
  }>();

  const isFileHasilAda = file_hasil && file_hasil.trim() !== '' && file_hasil !== 'null';
  const isFileDetailAda = file_detail && file_detail.trim() !== '' && file_detail !== 'null';
  const berkasTersedia = isFileHasilAda || isFileDetailAda;
  
  // Validasi status ditolak
  const isDitolak = status?.toLowerCase() === 'ditolak';
  
  // Validasi apakah komentar ada atau kosong/null
  const isKomentarAda = komentar && komentar.trim() !== '' && komentar !== 'null';

  // Validasi apakah pengguna belum pernah mendaftar/mengajukan tes sama sekali
  const belumMengajukan = (!tanggal || tanggal.trim() === '' || tanggal === 'null') && 
                          (!jam || jam.trim() === '' || jam === 'null');

  const downloadFile = async (fileName: string | undefined, titleText: string) => {
    try {
      if (!fileName || fileName.trim() === '' || fileName === 'null') {
        Alert.alert("Gagal", `File ${titleText} belum diunggah oleh admin untuk jadwal ini`);
        return;
      }

      const BASE_URL = process.env.EXPO_PUBLIC_API_URL?.replace('/api', '');
      const url = `${BASE_URL}/uploads/hasil/${encodeURIComponent(fileName)}`;

      if (Platform.OS === 'web') {
        window.open(url, '_blank');
        return;
      }

      const fileUri = FileSystem.cacheDirectory + fileName;
      const downloadResumable = FileSystem.createDownloadResumable(url, fileUri);
      const result = await downloadResumable.downloadAsync();

      if (!result) {
        Alert.alert("Gagal", "Gagal mendownload berkas");
        return;
      }

      await Sharing.shareAsync(result.uri);

    } catch (error) {
      console.log("Error download:", error);
      Alert.alert("Error", `Tidak dapat mengunduh berkas ${titleText}`);
    }
  };

  // Menentukan tipe data objek agar sesuai dengan kebutuhan Ionicons
  interface StatusCardConfig {
    icon: keyof typeof Ionicons.glyphMap;
    color: string;
    title: string;
    sub: string;
  }

  // Fungsi dinamis untuk menentukan konten Card Status
  const renderStatusCardInfo = (): StatusCardConfig => {
    // 1. Kondisi Belum Mengajukan Tes Sama Sekali
    if (belumMengajukan) {
      return {
        icon: "alert-circle-outline",
        color: "#64748b",
        title: "Belum Ada Pendaftaran",
        sub: "Anda belum mengajukan atau memilih jadwal pendaftaran tes pemeriksaan genetik STIFIn saat ini."
      };
    }

    // 2. Kondisi Berkas Sudah Di-upload / Selesai
    if (berkasTersedia) {
      return {
        icon: "checkmark-circle",
        color: "#16a34a",
        title: "Selamat! Tes Anda Selesai",
        sub: "Silakan unduh dokumen berkas resmi hasil tes pemeriksaan genetik STIFIn Anda."
      };
    }
    
    // 3. Kondisi Ditolak
    if (isDitolak) {
      return {
        icon: "close-circle",
        color: "#dc2626",
        title: "Maaf, Tes Anda Ditolak",
        sub: "Pendaftaran jadwal tes Anda ditolak oleh pihak admin. Silakan periksa Catatan Promotor di bawah untuk informasi lebih lanjut."
      };
    }

    // 4. Kondisi Sedang Diproses (Jika sudah mendaftar tapi berkas belum ada)
    return {
      icon: "time-outline",
      color: "#eab308",
      title: "Tes Sedang Diproses",
      sub: "Proses sinkronisasi data sedang berjalan. Berkas sertifikat hasil tes Anda akan muncul di bawah ini jika sudah di-upload oleh admin."
    };
  };

  const cardInfo = renderStatusCardInfo();

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <TouchableOpacity style={styles.backButton} onPress={() => router.back()}>
          <Ionicons name="arrow-back-outline" size={26} color="#1e293b" />
          <Text style={styles.headerTitle}>Detail Hasil Tes</Text>
        </TouchableOpacity>
      </View>

      <ScrollView contentContainerStyle={styles.content} showsVerticalScrollIndicator={false}>
        {/* Card Ringkasan Kelulusan / Progress / Ditolak / Belum Daftar */}
        <View style={styles.infoCard}>
          <Ionicons 
            name={cardInfo.icon} 
            size={48} 
            color={cardInfo.color} 
            style={{ marginBottom: 10 }} 
          />
          <Text style={styles.successTitle}>{cardInfo.title}</Text>
          <Text style={styles.successSub}>{cardInfo.sub}</Text>
          
          <View style={styles.divider} />
          
          <View style={styles.metaRow}>
            <Text style={styles.metaLabel}>Tanggal Pelaksanaan</Text>
            <Text style={styles.metaValue}>{belumMengajukan ? '—' : (tanggal || '—')}</Text>
          </View>
          
          <View style={styles.metaRow}>
            <Text style={styles.metaLabel}>Waktu Pemeriksaan</Text>
            <Text style={styles.metaValue}>
              {belumMengajukan ? '—' : (jam && jam.trim() !== '' && jam !== 'null' ? `${jam} WIB` : '—')}
            </Text>
          </View>
          
          <View style={styles.metaRow}>
            <Text style={styles.metaLabel}>Status Tes</Text>
            <Text style={[styles.metaValue, { color: cardInfo.color, fontWeight: '700' }]}>
              {belumMengajukan ? 'Belum Mendaftar' : (status || 'Menunggu')}
            </Text>
          </View>
        </View>

        {/* Tampilan Komentar / Catatan dari Backend Promotor */}
        {isKomentarAda && !belumMengajukan && (
          <View style={[styles.commentContainer, isDitolak && styles.commentContainerDitolak]}>
            <View style={styles.commentHeader}>
              <Ionicons 
                name="chatbubble-ellipses-outline" 
                size={20} 
                color={isDitolak ? "#dc2626" : "#2563eb"} 
              />
              <Text style={[styles.commentHeaderTitle, isDitolak && { color: '#991b1b' }]}>
                {isDitolak ? "Alasan Penolakan / Catatan" : "Catatan & Komentar Promotor"}
              </Text>
            </View>
            <Text style={styles.commentText}>{komentar}</Text>
          </View>
        )}

        {/* Bagian Daftar Unduhan / Jikalau Belum Mengajukan Tes */}
        <Text style={styles.sectionLabel}>Berkas Dokumen STIFIn</Text>

        {belumMengajukan ? (
          /* Tampilan Tombol Aksi Jika Belum Mengajukan */
          <View style={styles.emptyFileBox}>
            <Ionicons name="add-circle-outline" size={36} color="#64748b" />
            <Text style={styles.emptyFileText}>Silakan Daftarkan Diri Anda</Text>
            <Text style={[styles.emptyFileSub, { marginBottom: 15 }]}>
              Anda harus memilih jadwal dan lokasi pelaksanaan tes terlebih dahulu agar berkas analisis Anda dapat diproses oleh promotor.
            </Text>
            <TouchableOpacity 
              style={styles.actionButton} 
              onPress={() => router.push('/pendaftaran')}
            >
              <Text style={styles.actionButtonText}>Daftar Tes Sekarang</Text>
            </TouchableOpacity>
          </View>
        ) : !berkasTersedia ? (
          <View style={styles.emptyFileBox}>
            <Ionicons name="document-lock-outline" size={32} color="#94a3b8" />
            <Text style={styles.emptyFileText}>
              {isDitolak ? "Berkas Tidak Tersedia" : "Belum Ada Berkas yang Di-upload"}
            </Text>
            <Text style={styles.emptyFileSub}>
              {isDitolak 
                ? "Jadwal tes ditolak, tidak ada dokumen hasil tes yang diterbitkan untuk pendaftaran ini."
                : "Admin belum mengunggah dokumen hasil tes untuk jadwal ini. Silakan hubungi admin atau cek kembali nanti secara berkala."}
            </Text>
          </View>
        ) : (
          <View>
            {isFileHasilAda && (
              <TouchableOpacity
                style={styles.btnBlue}
                onPress={() => downloadFile(file_hasil, "Sertifikat")}
              >
                <Ionicons name="document-text" size={22} color="#fff" />
                <Text style={styles.btnText}>Download Sertifikat Resmi</Text>
              </TouchableOpacity>
            )}

            {isFileDetailAda && (
              <TouchableOpacity
                style={styles.btnGreen}
                onPress={() => downloadFile(file_detail, "Detail Tes")}
              >
                <Ionicons name="analytics" size={22} color="#fff" />
                <Text style={styles.btnText}>Download Hasil Detail Analisis</Text>
              </TouchableOpacity>
            )}
          </View>
        )}
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f8fafc' },
  header: { paddingHorizontal: 20, paddingVertical: 16, backgroundColor: '#fff', borderBottomWidth: 1, borderColor: '#e2e8f0', paddingTop: 40 },
  backButton: { flexDirection: 'row', alignItems: 'center', gap: 12 },
  headerTitle: { fontSize: 18, fontWeight: '700', color: '#1e293b' },
  content: { padding: 20 },
  infoCard: { backgroundColor: '#fff', borderRadius: 16, padding: 24, alignItems: 'center', elevation: 3, shadowColor: '#0f172a', shadowOffset: { width: 0, height: 4 }, shadowOpacity: 0.05, shadowRadius: 12, marginBottom: 20 },
  successTitle: { fontSize: 18, fontWeight: '700', color: '#0f172a', marginBottom: 6, textAlign: 'center' },
  successSub: { fontSize: 13, color: '#64748b', textAlign: 'center', lineHeight: 18, paddingHorizontal: 10 },
  divider: { width: '100%', height: 1, backgroundColor: '#f1f5f9', marginVertical: 18 },
  metaRow: { flexDirection: 'row', justifyContent: 'space-between', width: '100%', marginBottom: 10 } as ViewStyle,
  metaLabel: { fontSize: 13, color: '#64748b' },
  metaValue: { fontSize: 13, fontWeight: '600', color: '#1e293b' },
  commentContainer: { backgroundColor: '#eff6ff', borderRadius: 16, padding: 16, borderLeftWidth: 4, borderLeftColor: '#2563eb', marginBottom: 25, elevation: 1, shadowColor: '#2563eb', shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.05, shadowRadius: 4 },
  commentContainerDitolak: { backgroundColor: '#fef2f2', borderLeftColor: '#dc2626', shadowColor: '#dc2626' },
  commentHeader: { flexDirection: 'row', alignItems: 'center', gap: 8, marginBottom: 8 },
  commentHeaderTitle: { fontSize: 14, fontWeight: '700', color: '#1e40af' },
  commentText: { fontSize: 13, color: '#1e293b', lineHeight: 20, fontStyle: 'italic' },
  sectionLabel: { fontSize: 14, fontWeight: '700', color: '#475569', marginBottom: 12, paddingLeft: 4 },
  emptyFileBox: { backgroundColor: '#f1f5f9', borderRadius: 14, padding: 24, alignItems: 'center', justifyContent: 'center', borderWidth: 1, borderColor: '#e2e8f0', borderStyle: 'dashed', marginTop: 5 },
  emptyFileText: { fontSize: 15, fontWeight: '700', color: '#475569', marginTop: 10, marginBottom: 4 },
  emptyFileSub: { fontSize: 12, color: '#94a3b8', textAlign: 'center', lineHeight: 16, paddingHorizontal: 15 },
  actionButton: { backgroundColor: '#2563eb', paddingVertical: 10, paddingHorizontal: 20, borderRadius: 10, elevation: 1 },
  actionButtonText: { color: '#fff', fontSize: 13, fontWeight: '700' },
  btnBlue: { backgroundColor: '#2563eb', padding: 16, borderRadius: 12, flexDirection: 'row', justifyContent: 'center', alignItems: 'center', gap: 10, marginBottom: 14, elevation: 2 } as ViewStyle,
  btnGreen: { backgroundColor: '#16a34a', padding: 16, borderRadius: 12, flexDirection: 'row', justifyContent: 'center', alignItems: 'center', gap: 10, elevation: 2 } as ViewStyle,
  btnText: { color: '#fff', fontWeight: '700', fontSize: 15 },
});