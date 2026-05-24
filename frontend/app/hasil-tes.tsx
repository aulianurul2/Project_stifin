import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  SafeAreaView,
  TouchableOpacity,
  Alert,
  Platform,
  ViewStyle,
  ScrollView,
  Modal,
  FlatList,
  ActivityIndicator
} from 'react-native';
import { useLocalSearchParams, useRouter } from 'expo-router';
import * as FileSystem from 'expo-file-system/legacy';
import * as Sharing from 'expo-sharing';
import Ionicons from '@expo/vector-icons/Ionicons';
import axiosInstance from '@/src/api/axiosConfig';

interface SlotJadwal {
  id_jadwal: number;
  tanggal: string;
  waktu: string;
  lokasi: string;
  kuota: number;
}

export default function HasilTes() {
  const router = useRouter();
  
  const { id_jadwal, tanggal, jam, file_hasil, file_detail, status, komentar } = useLocalSearchParams<{
    id_jadwal: string;
    tanggal: string;
    jam: string;
    file_hasil: string;
    file_detail: string;
    status: string;
    komentar: string;
  }>();

  // State Management
  const [modalRescheduleVisible, setModalRescheduleVisible] = useState(false);
  const [listJadwal, setListJadwal] = useState<SlotJadwal[]>([]);
  const [loadingJadwal, setLoadingJadwal] = useState(false);
  const [prosesLoading, setProsesLoading] = useState(false);

  const isFileHasilAda = file_hasil && file_hasil.trim() !== '' && file_hasil !== 'null';
  const isFileDetailAda = file_detail && file_detail.trim() !== '' && file_detail !== 'null';
  const berkasTersedia = isFileHasilAda || isFileDetailAda;
  
  const isDitolak = status?.toLowerCase() === 'ditolak';
  const isDibatalkan = status?.toLowerCase() === 'dibatalkan';
  const isKomentarAda = komentar && komentar.trim() !== '' && komentar !== 'null';
  const belumMengajukan = (!tanggal || tanggal.trim() === '' || tanggal === 'null') && 
                          (!jam || jam.trim() === '' || jam === 'null');

  // Ambal Jadwal Kosong yang Tersedia dari API
  const fetchJadwalTersedia = async () => {
    setLoadingJadwal(true);
    try {
      const BASE_URL = process.env.EXPO_PUBLIC_API_URL;
      const response = await fetch(`${BASE_URL}/jadwal-tersedia`);
      const data = await response.json();
      setListJadwal(data);
    } catch (error) {
      console.log("Error fetch jadwal:", error);
      if (Platform.OS === 'web') {
        window.alert("Gagal memuat slot jadwal baru.");
      } else {
        Alert.alert("Error", "Gagal memuat slot jadwal baru.");
      }
    } finally {
      setLoadingJadwal(false);
    }
  };

  useEffect(() => {
    if (modalRescheduleVisible) {
      fetchJadwalTersedia();
    }
  }, [modalRescheduleVisible]);

  // Fungsi Download Dokumen Hasil
  const downloadFile = async (fileName: string | undefined, titleText: string) => {
    try {
      if (!fileName || fileName.trim() === '' || fileName === 'null') {
        if (Platform.OS === 'web') {
          window.alert(`File ${titleText} belum diunggah oleh admin`);
        } else {
          Alert.alert("Gagal", `File ${titleText} belum diunggah oleh admin`);
        }
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
      if (Platform.OS === 'web') {
        window.alert(`Tidak dapat mengunduh berkas ${titleText}`);
      } else {
        Alert.alert("Error", `Tidak dapat mengunduh berkas ${titleText}`);
      }
    }
  };

  // ==========================================
  // FIX: BLOK AKSI PEMBATALAN JADWAL
  // ==========================================
  const handleBatalkanJadwal = () => {
    console.log("--> TOMBOL BATALKAN DIKLIK! ID JADWAL:", id_jadwal);

    if (!id_jadwal || id_jadwal === 'undefined' || id_jadwal === 'null') {
      if (Platform.OS === 'web') window.alert("ID Jadwal tidak valid");
      else Alert.alert("Error", "ID Jadwal tidak valid");
      return;
    }

    if (Platform.OS === 'web') {
      const konfirmasiWeb = window.confirm("Apakah Anda yakin ingin membatalkan pendaftaran jadwal pemeriksaan ini?");
      if (konfirmasiWeb) {
        eksekusiPembatalanKeBackend();
      }
      return;
    }

    Alert.alert(
      "Batalkan Pendaftaran",
      "Apakah Anda yakin ingin membatalkan pendaftaran jadwal pemeriksaan ini?",
      [
        { text: "Kembali", style: "cancel" },
        { text: "Ya, Batalkan", style: "destructive", onPress: () => eksekusiPembatalanKeBackend() }
      ]
    );
  };

  const eksekusiPembatalanKeBackend = async () => {
    setProsesLoading(true);
    try {
      const response = await axiosInstance.put(`/pendaftaran/${id_jadwal}/batalkan`);
      console.log("--> RESPONSE SUCCESS API BATAL:", response.data);

      if (response.status === 200) {
        if (Platform.OS === 'web') {
          window.alert("Jadwal pemeriksaan berhasil dibatalkan.");
          router.replace('/riwayat');
        } else {
          Alert.alert("Berhasil", "Jadwal pemeriksaan berhasil dibatalkan.", [
            { text: "OK", onPress: () => router.replace('/riwayat') }
          ]);
        }
      }
    } catch (error: any) {
      console.log("--> ERROR API BATALKAN:", error?.response?.data || error);
      const pesanError = error?.response?.data?.message || "Terjadi kesalahan saat membatalkan jadwal.";
      if (Platform.OS === 'web') {
        window.alert(pesanError);
      } else {
        Alert.alert("Error", pesanError);
      }
    } finally {
      setProsesLoading(false);
    }
  };

  // ==========================================
  // FIX: BLOK AKSI RESCHEDULE JADWAL
  // ==========================================
  const handleConfirmReschedule = async (idJadwalBaru: number) => {
    if (!id_jadwal || id_jadwal === 'undefined' || id_jadwal === 'null') {
      if (Platform.OS === 'web') window.alert("ID Jadwal asal tidak valid");
      else Alert.alert("Error", "ID Jadwal asal tidak valid");
      return;
    }

    setModalRescheduleVisible(false);
    setProsesLoading(true);

    try {
      console.log("--> ID LAMA =", id_jadwal, " | ID BARU =", idJadwalBaru);

      const response = await axiosInstance.put(`/pendaftaran/${id_jadwal}/reschedule`, {
        id_jadwal_baru: idJadwalBaru
      });

      console.log("--> RESPONSE SUCCESS RESCHEDULE:", response.data);

      if (response.status === 200) {
        if (Platform.OS === 'web') {
          window.alert("Pengajuan perubahan jadwal berhasil terkirim. Menunggu konfirmasi admin.");
          router.replace('/riwayat');
        } else {
          Alert.alert(
            "Sukses Reschedule",
            "Pengajuan perubahan jadwal berhasil terkirim. Menunggu konfirmasi admin.",
            [{ text: "Selesai", onPress: () => router.replace('/riwayat') }]
          );
        }
      }
    } catch (error: any) {
      console.log("--> ERROR RESCHEDULE =", error?.response?.data || error);
      const pesanError = error?.response?.data?.message || "Gagal memproses pengubahan jadwal.";
      
      if (Platform.OS === 'web') {
        window.alert(pesanError);
      } else {
        Alert.alert("Error", pesanError);
      }
    } finally {
      setProsesLoading(false);
    }
  };

  interface StatusCardConfig {
    icon: keyof typeof Ionicons.glyphMap;
    color: string;
    title: string;
    sub: string;
  }

  const renderStatusCardInfo = (): StatusCardConfig => {
    if (belumMengajukan) {
      return {
        icon: "alert-circle-outline",
        color: "#64748b",
        title: "Belum Ada Pendaftaran",
        sub: "Anda belum mengajukan atau memilih jadwal pendaftaran tes pemeriksaan genetik STIFIn saat ini."
      };
    }
    if (berkasTersedia) {
      return {
        icon: "checkmark-circle",
        color: "#16a34a",
        title: "Selamat! Tes Anda Selesai",
        sub: "Silakan unduh dokumen berkas resmi hasil tes pemeriksaan genetik STIFIn Anda."
      };
    }
    if (isDitolak) {
      return {
        icon: "close-circle",
        color: "#dc2626",
        title: "Maaf, Tes Anda Ditolak",
        sub: "Pendaftaran jadwal tes Anda ditolak oleh pihak admin. Silakan periksa Catatan Promotor di bawah."
      };
    }
    if (isDibatalkan) {
      return {
        icon: "close-circle-outline",
        color: "#64748b",
        title: "Pendaftaran Dibatalkan",
        sub: "Jadwal pendaftaran ini telah Anda batalkan."
      };
    }
    return {
      icon: "time-outline",
      color: "#eab308",
      title: "Tes Sedang Diproses",
      sub: "Menunggu verifikasi admin atau pengerjaan dokumen berkas sertifikat genetik Anda selesai di-upload."
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

      {prosesLoading && (
        <View style={styles.loadingOverlay}>
          <ActivityIndicator size="large" color="#2563eb" />
          <Text style={{ marginTop: 10, color: '#fff', fontWeight: '600' }}>Memproses Permintaan...</Text>
        </View>
      )}

      <ScrollView contentContainerStyle={styles.content} showsVerticalScrollIndicator={false}>
        <View style={styles.infoCard}>
          <Ionicons name={cardInfo.icon} size={48} color={cardInfo.color} style={{ marginBottom: 10 }} />
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

        {isKomentarAda && !belumMengajukan && (
          <View style={[styles.commentContainer, isDitolak && styles.commentContainerDitolak]}>
            <View style={styles.commentHeader}>
              <Ionicons name="chatbubble-ellipses-outline" size={20} color={isDitolak ? "#dc2626" : "#2563eb"} />
              <Text style={[styles.commentHeaderTitle, isDitolak && { color: '#991b1b' }]}>
                {isDitolak ? "Alasan Penolakan / Catatan" : "Catatan & Komentar Promotor"}
              </Text>
            </View>
            <Text style={styles.commentText}>{komentar}</Text>
          </View>
        )}

        {/* TOMBOL AKSI PEMBATALAN & RESCHEDULE UNTUK KLIEN */}
        {!belumMengajukan && !berkasTersedia && !isDibatalkan && !isDitolak && (
          <View style={styles.clientActionSection} pointerEvents="box-none">
            <Text style={styles.sectionLabel}>Kelola Agenda Jadwal</Text>
            <View style={styles.actionRowContainer}>
              <TouchableOpacity 
                style={[styles.actionBtnOutline, { borderColor: '#dc2626' }]} 
                onPress={handleBatalkanJadwal}
                activeOpacity={0.6}
              >
                <Ionicons name="close-circle-outline" size={18} color="#dc2626" />
                <Text style={[styles.actionBtnText, { color: '#dc2626' }]}>Batalkan Tes</Text>
              </TouchableOpacity>

              <TouchableOpacity 
                style={[styles.actionBtnSolid, { backgroundColor: '#eab308' }]} 
                onPress={() => setModalRescheduleVisible(true)}
                activeOpacity={0.6}
              >
                <Ionicons name="calendar-outline" size={18} color="#fff" />
                <Text style={styles.actionBtnText}>Reschedule</Text>
              </TouchableOpacity>
            </View>
          </View>
        )}

        <Text style={styles.sectionLabel}>Berkas Dokumen STIFIn</Text>

        {belumMengajukan ? (
          <View style={styles.emptyFileBox}>
            <Ionicons name="add-circle-outline" size={36} color="#64748b" />
            <Text style={styles.emptyFileText}>Silakan Daftarkan Diri Anda</Text>
            <Text style={[styles.emptyFileSub, { marginBottom: 15 }]}>
              Anda harus memilih jadwal dan lokasi pelaksanaan tes terlebih dahulu.
            </Text>
            <TouchableOpacity style={styles.actionButton} onPress={() => router.push('/pendaftaran')}>
              <Text style={styles.actionButtonText}>Daftar Tes Sekarang</Text>
            </TouchableOpacity>
          </View>
        ) : !berkasTersedia ? (
          <View style={styles.emptyFileBox}>
            <Ionicons name="document-lock-outline" size={32} color="#94a3b8" />
            <Text style={styles.emptyFileText}>
              {isDitolak || isDibatalkan ? "Berkas Tidak Tersedia" : "Belum Ada Berkas yang Di-upload"}
            </Text>
            <Text style={styles.emptyFileSub}>
              {isDitolak || isDibatalkan 
                ? "Jadwal tidak aktif, tidak ada dokumen hasil analisis genetik yang diterbitkan."
                : "Admin belum mengunggah dokumen hasil tes untuk jadwal ini."}
            </Text>
          </View>
        ) : (
          <View>
            {isFileHasilAda && (
              <TouchableOpacity style={styles.btnBlue} onPress={() => downloadFile(file_hasil, "Sertifikat")}>
                <Ionicons name="document-text" size={22} color="#fff" />
                <Text style={styles.btnText}>Download Sertifikat Resmi</Text>
              </TouchableOpacity>
            )}

            {isFileDetailAda && (
              <TouchableOpacity style={styles.btnGreen} onPress={() => downloadFile(file_detail, "Detail Tes")}>
                <Ionicons name="analytics" size={22} color="#fff" />
                <Text style={styles.btnText}>Download Hasil Detail Analisis</Text>
              </TouchableOpacity>
            )}
          </View>
        )}
      </ScrollView>

      {/* POPUP MODAL PILIH JADWAL RESCHEDULE BARU */}
      <Modal animationType="slide" transparent={true} visible={modalRescheduleVisible} onRequestClose={() => setModalRescheduleVisible(false)}>
        <View style={styles.modalOverlay}>
          <View style={styles.modalSheet}>
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>Pilih Slot Jadwal Baru</Text>
              <TouchableOpacity onPress={() => setModalRescheduleVisible(false)}>
                <Ionicons name="close-circle" size={24} color="#94a3b8" />
              </TouchableOpacity>
            </View>

            {loadingJadwal ? (
              <ActivityIndicator size="large" color="#2563eb" style={{ marginVertical: 40 }} />
            ) : (
              <FlatList
                data={listJadwal}
                keyExtractor={(item) => item.id_jadwal.toString()}
                renderItem={({ item }) => (
                  <TouchableOpacity style={styles.jadwalItemCard} onPress={() => handleConfirmReschedule(item.id_jadwal)}>
                    <View>
                      <Text style={{ fontWeight: '700', color: '#1e293b', fontSize: 14 }}>{item.tanggal}</Text>
                      <Text style={{ color: '#64748b', fontSize: 12, marginTop: 2 }}>{item.waktu} WIB • {item.lokasi}</Text>
                    </View>
                    <View style={styles.kuotaBadge}>
                      <Text style={{ color: '#1e40af', fontSize: 11, fontWeight: '700' }}>Kuota: {item.kuota}</Text>
                    </View>
                  </TouchableOpacity>
                )}
                ListEmptyComponent={
                  <Text style={{ textAlign: 'center', color: '#64748b', marginVertical: 30, fontStyle: 'italic' }}>
                    Tidak ada slot jadwal kosong lain yang tersedia saat ini.
                  </Text>
                }
              />
            )}
          </View>
        </View>
      </Modal>
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
  commentContainer: { backgroundColor: '#eff6ff', borderRadius: 16, padding: 16, borderLeftWidth: 4, borderLeftColor: '#2563eb', marginBottom: 20 },
  commentContainerDitolak: { backgroundColor: '#fef2f2', borderLeftColor: '#dc2626' },
  commentHeader: { flexDirection: 'row', alignItems: 'center', gap: 8, marginBottom: 8 },
  commentHeaderTitle: { fontSize: 14, fontWeight: '700', color: '#1e40af' },
  commentText: { fontSize: 13, color: '#1e293b', lineHeight: 20, fontStyle: 'italic' },
  sectionLabel: { fontSize: 14, fontWeight: '700', color: '#475569', marginBottom: 12, paddingLeft: 4 },
  clientActionSection: { 
    marginTop: 15,
    marginBottom: 15,
    zIndex: 10,
    elevation: 5,
  },
  actionRowContainer: {
    flexDirection: 'row', 
    gap: 12, 
    width: '100%',
    zIndex: 20,
    elevation: 6,
  },
  actionBtnOutline: { 
    flex: 1,
    borderWidth: 1, 
    borderRadius: 10, 
    padding: 14, 
    flexDirection: 'row', 
    justifyContent: 'center', 
    alignItems: 'center', 
    gap: 6,
    backgroundColor: '#ffffff'
  },
  actionBtnSolid: { 
    flex: 1,
    borderRadius: 10, 
    padding: 14, 
    flexDirection: 'row', 
    justifyContent: 'center', 
    alignItems: 'center', 
    gap: 6 
  },
  actionBtnText: { fontWeight: '700', fontSize: 13, color: '#fff' },
  emptyFileBox: { backgroundColor: '#f1f5f9', borderRadius: 14, padding: 24, alignItems: 'center', justifyContent: 'center', borderWidth: 1, borderColor: '#e2e8f0', borderStyle: 'dashed', marginTop: 5 },
  emptyFileText: { fontSize: 15, fontWeight: '700', color: '#475569', marginTop: 10, marginBottom: 4 },
  emptyFileSub: { fontSize: 12, color: '#94a3b8', textAlign: 'center', lineHeight: 16, paddingHorizontal: 15 },
  actionButton: { backgroundColor: '#2563eb', paddingVertical: 10, paddingHorizontal: 20, borderRadius: 10 },
  actionButtonText: { color: '#fff', fontSize: 13, fontWeight: '700' },
  btnBlue: { backgroundColor: '#2563eb', padding: 16, borderRadius: 12, flexDirection: 'row', justifyContent: 'center', alignItems: 'center', gap: 10, marginBottom: 14 } as ViewStyle,
  btnGreen: { backgroundColor: '#16a34a', padding: 16, borderRadius: 12, flexDirection: 'row', justifyContent: 'center', alignItems: 'center', gap: 10 } as ViewStyle,
  btnText: { color: '#fff', fontWeight: '700', fontSize: 15 },
  loadingOverlay: { ...StyleSheet.absoluteFillObject, backgroundColor: 'rgba(0,0,0,0.5)', justifyContent: 'center', alignItems: 'center', zIndex: 999 },
  modalOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.4)', justifyContent: 'flex-end' },
  modalSheet: { backgroundColor: '#fff', borderTopLeftRadius: 24, borderTopRightRadius: 24, padding: 24, maxHeight: '60%' },
  modalHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 20 },
  modalTitle: { fontSize: 16, fontWeight: '700', color: '#0f172a' },
  jadwalItemCard: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', padding: 14, backgroundColor: '#f8fafc', borderRadius: 12, marginBottom: 10, borderWidth: 1, borderColor: '#e2e8f0' },
  kuotaBadge: { backgroundColor: '#eff6ff', paddingVertical: 4, paddingHorizontal: 8, borderRadius: 6 }
});