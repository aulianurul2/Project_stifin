import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  SafeAreaView,
  TouchableOpacity,
  Platform,
  ScrollView,
  Modal,
  FlatList,
  ActivityIndicator
} from 'react-native';
import { useLocalSearchParams, useRouter } from 'expo-router';
import * as FileSystem from 'expo-file-system/legacy';
import * as Sharing from 'expo-sharing';
import Ionicons from '@expo/vector-icons/Ionicons';
import Toast from 'react-native-toast-message'; 
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

const fetchJadwalTersedia = async () => {
    setLoadingJadwal(true);
    try {
      const BASE_URL = process.env.EXPO_PUBLIC_API_URL;
      const response = await fetch(`${BASE_URL}/jadwal-tersedia`);
      const data = await response.json();
      setListJadwal(data);
    } catch (error) {
      console.log("Error fetch jadwal:", error);
      Toast.show({
        type: 'error',
        text1: 'Gagal Memuat',
        text2: 'Gagal memuat slot jadwal baru.',
        position: 'top',
      });
    } finally {
      setLoadingJadwal(false);
    }
  };

  useEffect(() => {
    if (modalRescheduleVisible) {
      fetchJadwalTersedia();
    }
  }, [modalRescheduleVisible]);

const downloadFile = async (fileName: string | undefined, titleText: string) => {
    try {
      if (!fileName || fileName.trim() === '' || fileName === 'null') {
        Toast.show({
          type: 'error',
          text1: 'File Tidak Tersedia',
          text2: `File ${titleText} belum diunggah oleh admin.`,
          position: 'top',
        });
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
        Toast.show({
          type: 'error',
          text1: 'Unduh Gagal',
          text2: 'Gagal mendownload berkas.',
          position: 'top',
        });
        return;
      }
      await Sharing.shareAsync(result.uri);
    } catch (error) {
      Toast.show({
        type: 'error',
        text1: 'Terjadi Kesalahan',
        text2: `Tidak dapat mengunduh berkas ${titleText}.`,
        position: 'top',
      });
    }
  };



  const handleConfirmReschedule = async (idJadwalBaru: number) => {
    if (!id_jadwal || id_jadwal === 'undefined' || id_jadwal === 'null') {
      Toast.show({
        type: 'error',
        text1: 'Data Tidak Valid',
        text2: 'ID Jadwal asal tidak valid.',
        position: 'top',
      });
      return;
    }

    setModalRescheduleVisible(false);
    setProsesLoading(true);

    try {
      const response = await axiosInstance.put(`/pendaftaran/${id_jadwal}/reschedule`, {
        id_jadwal_baru: idJadwalBaru,
      });

      if (response.status === 200) {
        Toast.show({
          type: 'success',
          text1: 'Reschedule Berhasil',
          text2: 'Pengajuan perubahan jadwal berhasil terkirim. Menunggu konfirmasi admin.',
          position: 'top',
          visibilityTime: 2500,
        });

        setTimeout(() => {
          router.replace('/riwayat');
        }, 1000);
      }
    } catch (error: any) {
      const pesanError = error?.response?.data?.message || 'Gagal memproses pengubahan jadwal.';
      Toast.show({
        type: 'error',
        text1: 'Reschedule Gagal',
        text2: pesanError,
        position: 'top',
      });
    } finally {
      setProsesLoading(false);
    }
  };

  interface StatusCardConfig {
    icon: keyof typeof Ionicons.glyphMap;
    color: string;
    bgColor: string;
    title: string;
    sub: string;
  }

  const renderStatusCardInfo = (): StatusCardConfig => {
    if (belumMengajukan) {
      return {
        icon: "alert-circle-outline",
        color: "#546e7a",
        bgColor: "#f5f5f5",
        title: "Belum Ada Pendaftaran",
        sub: "Anda belum mengajukan atau memilih jadwal pendaftaran tes pemeriksaan genetik STIFIn saat ini."
      };
    }
    if (berkasTersedia) {
      return {
        icon: "checkmark-circle",
        color: "#00AA5B",
        bgColor: "#e8f5e9",
        title: "Selamat! Tes Anda Selesai",
        sub: "Silakan unduh dokumen berkas resmi hasil tes pemeriksaan genetik STIFIn Anda."
      };
    }
    if (isDitolak) {
      return {
        icon: "close-circle",
        color: "#e53935",
        bgColor: "#ffebee",
        title: "Maaf, Tes Anda Ditolak",
        sub: "Pendaftaran jadwal tes Anda ditolak oleh pihak admin. Silakan periksa Catatan Promotor di bawah."
      };
    }
    if (isDibatalkan) {
      return {
        icon: "close-circle-outline",
        color: "#78909c",
        bgColor: "#f5f5f5",
        title: "Pendaftaran Dibatalkan",
        sub: "Jadwal pendaftaran ini telah Anda batalkan."
      };
    }
    return {
      icon: "time-outline",
      color: "#f57c00",
      bgColor: "#fff3e0",
      title: "Tes Sedang Diproses",
      sub: "Menunggu verifikasi admin atau pengerjaan dokumen berkas sertifikat genetik Anda selesai di-upload."
    };
  };

  const cardInfo = renderStatusCardInfo();

  return (
    <SafeAreaView style={styles.container}>

      {/* Green Top Bar */}
      <View style={styles.topBar}>
        <TouchableOpacity style={styles.backBtn} onPress={() => router.back()}>
          <Ionicons name="arrow-back-outline" size={22} color="#fff" />
        </TouchableOpacity>
        <View style={styles.topBarCenter}>
          <Text style={styles.topBarTitle}>Detail Hasil Tes</Text>
          <Text style={styles.topBarSub}>STIFIn Genetic Test</Text>
        </View>
        <View style={{ width: 38 }} />
      </View>

      {prosesLoading && (
        <View style={styles.loadingOverlay}>
          <View style={styles.loadingBox}>
            <ActivityIndicator size="large" color="#00AA5B" />
            <Text style={styles.loadingText}>Memproses Permintaan...</Text>
          </View>
        </View>
      )}

      <ScrollView contentContainerStyle={styles.content} showsVerticalScrollIndicator={false}>

        {/* Status Card */}
        <View style={[styles.statusCard, { borderLeftColor: cardInfo.color }]}>
          <View style={[styles.statusIconWrap, { backgroundColor: cardInfo.bgColor }]}>
            <Ionicons name={cardInfo.icon} size={36} color={cardInfo.color} />
          </View>
          <View style={styles.statusCardRight}>
            <Text style={[styles.statusTitle, { color: cardInfo.color }]}>{cardInfo.title}</Text>
            <Text style={styles.statusSub}>{cardInfo.sub}</Text>
          </View>
        </View>

        {/* Meta Info */}
        <View style={styles.metaCard}>
          <View style={styles.metaRow}>
            <View style={styles.metaIconWrap}>
              <Ionicons name="calendar-outline" size={14} color="#00AA5B" />
            </View>
            <Text style={styles.metaLabel}>Tanggal Pelaksanaan</Text>
            <Text style={styles.metaValue}>{belumMengajukan ? '—' : (tanggal || '—')}</Text>
          </View>

          <View style={styles.metaDivider} />
          
          <View style={styles.metaRow}>
            <View style={styles.metaIconWrap}>
              <Ionicons name="time-outline" size={14} color="#00AA5B" />
            </View>
            <Text style={styles.metaLabel}>Waktu Pemeriksaan</Text>
            <Text style={styles.metaValue}>
              {belumMengajukan ? '—' : (jam && jam.trim() !== '' && jam !== 'null' ? `${jam} WIB` : '—')}
            </Text>
          </View>

          <View style={styles.metaDivider} />

          <View style={styles.metaRow}>
            <View style={styles.metaIconWrap}>
              <Ionicons name="pulse-outline" size={14} color="#00AA5B" />
            </View>
            <Text style={styles.metaLabel}>Status Tes</Text>
            <View style={[styles.statusPill, { backgroundColor: cardInfo.bgColor }]}>
              <Text style={[styles.statusPillText, { color: cardInfo.color }]}>
                {belumMengajukan ? 'Belum Mendaftar' : (status || 'Menunggu')}
              </Text>
            </View>
          </View>
        </View>

        {/* Komentar */}
        {isKomentarAda && !belumMengajukan && (
          <View style={[styles.commentCard, isDitolak && styles.commentCardDitolak]}>
            <View style={styles.commentHeader}>
              <Ionicons
                name="chatbubble-ellipses-outline"
                size={16}
                color={isDitolak ? "#e53935" : "#00AA5B"}
              />
              <Text style={[styles.commentTitle, isDitolak && { color: '#c62828' }]}>
                {isDitolak ? "Alasan Penolakan" : "Catatan Promotor"}
              </Text>
            </View>
            <Text style={styles.commentText}>{komentar}</Text>
          </View>
        )}

       {/* Action Buttons — hanya Reschedule */}
{!belumMengajukan && !berkasTersedia && !isDibatalkan && !isDitolak && (
  <View style={styles.actionSection}>
    <Text style={styles.sectionLabel}>Kelola Jadwal</Text>
    <TouchableOpacity
      style={styles.btnReschedule}
      onPress={() => setModalRescheduleVisible(true)}
      activeOpacity={0.7}
    >
      <Ionicons name="calendar-outline" size={17} color="#fff" />
      <Text style={styles.btnRescheduleText}>Ajukan Reschedule</Text>
    </TouchableOpacity>
  </View>
)}

        {/* Document Section */}
        <Text style={styles.sectionLabel}>Berkas Dokumen STIFIn</Text>

        {belumMengajukan ? (
          <View style={styles.emptyDoc}>
            <View style={styles.emptyDocIcon}>
              <Ionicons name="document-outline" size={36} color="#90a4ae" />
            </View>
            <Text style={styles.emptyDocTitle}>Belum Ada Dokumen</Text>
            <Text style={styles.emptyDocSub}>
              Daftarkan diri Anda terlebih dahulu untuk mendapatkan hasil tes genetik STIFIn.
            </Text>
            <TouchableOpacity style={styles.btnDaftar} onPress={() => router.push('/pendaftaran')} activeOpacity={0.85}>
              <Ionicons name="add-circle-outline" size={16} color="#fff" />
              <Text style={styles.btnDaftarText}>Daftar Tes Sekarang</Text>
            </TouchableOpacity>
          </View>
        ) : !berkasTersedia ? (
          <View style={styles.emptyDoc}>
            <View style={styles.emptyDocIcon}>
              <Ionicons name="document-lock-outline" size={36} color="#90a4ae" />
            </View>
            <Text style={styles.emptyDocTitle}>
              {isDitolak || isDibatalkan ? "Berkas Tidak Tersedia" : "Berkas Belum Di-upload"}
            </Text>
            <Text style={styles.emptyDocSub}>
              {isDitolak || isDibatalkan 
                ? "Jadwal tidak aktif, tidak ada dokumen yang diterbitkan."
                : "Admin belum mengunggah dokumen hasil tes untuk jadwal ini."}
            </Text>
          </View>
        ) : (
          <View style={styles.docButtons}>
            {isFileHasilAda && (
              <TouchableOpacity
                style={styles.btnDownloadGreen}
                onPress={() => downloadFile(file_hasil, "Sertifikat")}
                activeOpacity={0.85}
              >
                <View style={styles.btnDownloadIcon}>
                  <Ionicons name="document-text" size={22} color="#00AA5B" />
                </View>
                <View style={styles.btnDownloadInfo}>
                  <Text style={styles.btnDownloadTitle}>Sertifikat Resmi</Text>
                  <Text style={styles.btnDownloadSub}>Unduh dokumen sertifikat</Text>
                </View>
                <Ionicons name="download-outline" size={20} color="#00AA5B" />
              </TouchableOpacity>
            )}

            {isFileDetailAda && (
              <TouchableOpacity
                style={styles.btnDownloadBlue}
                onPress={() => downloadFile(file_detail, "Detail Tes")}
                activeOpacity={0.85}
              >
                <View style={styles.btnDownloadIconBlue}>
                  <Ionicons name="analytics" size={22} color="#0288d1" />
                </View>
                <View style={styles.btnDownloadInfo}>
                  <Text style={[styles.btnDownloadTitle, { color: '#01579b' }]}>Hasil Detail Analisis</Text>
                  <Text style={styles.btnDownloadSub}>Unduh hasil analisis lengkap</Text>
                </View>
                <Ionicons name="download-outline" size={20} color="#0288d1" />
              </TouchableOpacity>
            )}
          </View>
        )}

      </ScrollView>

      {/* Reschedule Modal */}
      <Modal animationType="slide" transparent={true} visible={modalRescheduleVisible} onRequestClose={() => setModalRescheduleVisible(false)}>
        <View style={styles.modalOverlay}>
          <View style={styles.modalSheet}>
            <View style={styles.modalHandle} />
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>Pilih Slot Jadwal Baru</Text>
              <TouchableOpacity style={styles.modalClose} onPress={() => setModalRescheduleVisible(false)}>
                <Ionicons name="close" size={20} color="#90a4ae" />
              </TouchableOpacity>
            </View>

            {loadingJadwal ? (
              <View style={{ padding: 40, alignItems: 'center', gap: 10 }}>
                <ActivityIndicator size="large" color="#00AA5B" />
                <Text style={{ color: '#90a4ae', fontSize: 13 }}>Memuat jadwal...</Text>
              </View>
            ) : (
              <FlatList
                data={listJadwal}
                keyExtractor={(item) => item.id_jadwal.toString()}
                renderItem={({ item }) => (
                  <TouchableOpacity
                    style={styles.jadwalItem}
                    onPress={() => handleConfirmReschedule(item.id_jadwal)}
                    activeOpacity={0.7}
                  >
                    <View style={styles.jadwalItemLeft}>
                      <View style={styles.jadwalIconWrap}>
                        <Ionicons name="calendar-outline" size={16} color="#00AA5B" />
                      </View>
                      <View>
                        <Text style={styles.jadwalTanggal}>{item.tanggal}</Text>
                        <Text style={styles.jadwalDetail}>{item.waktu} WIB • {item.lokasi}</Text>
                      </View>
                    </View>
                    <View style={styles.kuotaBadge}>
                      <Text style={styles.kuotaText}>Kuota: {item.kuota}</Text>
                    </View>
                  </TouchableOpacity>
                )}
                ListEmptyComponent={
                  <Text style={styles.emptyModal}>
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

  loadingOverlay: {
    ...StyleSheet.absoluteFillObject,
    backgroundColor: 'rgba(0,0,0,0.4)',
    justifyContent: 'center',
    alignItems: 'center',
    zIndex: 999,
  },
  loadingBox: {
    backgroundColor: '#fff',
    borderRadius: 16,
    padding: 24,
    alignItems: 'center',
    gap: 12,
  },
  loadingText: { fontSize: 14, fontWeight: '600', color: '#546e7a' },

  statusCard: {
    backgroundColor: '#fff',
    borderRadius: 16,
    padding: 16,
    flexDirection: 'row',
    gap: 14,
    marginBottom: 14,
    borderWidth: 1,
    borderColor: '#e8f5e9',
    borderLeftWidth: 4,
    shadowColor: '#00AA5B',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 6,
    elevation: 2,
  },
  statusIconWrap: {
    width: 64,
    height: 64,
    borderRadius: 16,
    justifyContent: 'center',
    alignItems: 'center',
  },
  statusCardRight: { flex: 1 },
  statusTitle: { fontSize: 15, fontWeight: '800', marginBottom: 4 },
  statusSub: { fontSize: 12, color: '#78909c', lineHeight: 17 },

  metaCard: {
    backgroundColor: '#fff',
    borderRadius: 16,
    padding: 16,
    marginBottom: 14,
    borderWidth: 1,
    borderColor: '#e8f5e9',
    shadowColor: '#00AA5B',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 6,
    elevation: 2,
  },
  metaRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
  },
  metaIconWrap: {
    width: 26,
    height: 26,
    borderRadius: 8,
    backgroundColor: '#e8f5e9',
    justifyContent: 'center',
    alignItems: 'center',
  },
  metaLabel: { flex: 1, fontSize: 13, color: '#78909c', fontWeight: '600' },
  metaValue: { fontSize: 13, fontWeight: '700', color: '#1a1a2e' },
  metaDivider: { height: 1, backgroundColor: '#f5faf7', marginVertical: 12 },
  statusPill: {
    borderRadius: 12,
    paddingHorizontal: 10,
    paddingVertical: 3,
  },
  statusPillText: { fontSize: 12, fontWeight: '800' },

  commentCard: {
    backgroundColor: '#e8f5e9',
    borderRadius: 14,
    padding: 14,
    borderLeftWidth: 3,
    borderLeftColor: '#00AA5B',
    marginBottom: 14,
  },
  commentCardDitolak: {
    backgroundColor: '#ffebee',
    borderLeftColor: '#e53935',
  },
  commentHeader: { flexDirection: 'row', alignItems: 'center', gap: 6, marginBottom: 8 },
  commentTitle: { fontSize: 13, fontWeight: '800', color: '#00AA5B' },
  commentText: { fontSize: 13, color: '#37474f', lineHeight: 19, fontStyle: 'italic' },

  sectionLabel: { fontSize: 13, fontWeight: '800', color: '#546e7a', marginBottom: 10, paddingLeft: 2 },

  actionSection: { marginBottom: 16 },
btnReschedule: {
  flexDirection: 'row',
  alignItems: 'center',
  justifyContent: 'center',
  gap: 6,
  paddingVertical: 13,
  borderRadius: 12,
  backgroundColor: '#f57c00',
  shadowColor: '#f57c00',
  shadowOffset: { width: 0, height: 3 },
  shadowOpacity: 0.25,
  shadowRadius: 6,
  elevation: 3,
},
  btnRescheduleText: { color: '#fff', fontWeight: '700', fontSize: 13 },

  emptyDoc: {
    backgroundColor: '#fff',
    borderRadius: 16,
    padding: 28,
    alignItems: 'center',
    borderWidth: 1.5,
    borderColor: '#e0f2ec',
    borderStyle: 'dashed',
  },
  emptyDocIcon: {
    width: 72,
    height: 72,
    borderRadius: 36,
    backgroundColor: '#f5faf7',
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 12,
  },
  emptyDocTitle: { fontSize: 15, fontWeight: '800', color: '#546e7a', marginBottom: 6 },
  emptyDocSub: { fontSize: 12, color: '#90a4ae', textAlign: 'center', lineHeight: 17, marginBottom: 16 },
  btnDaftar: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    backgroundColor: '#00AA5B',
    paddingVertical: 10,
    paddingHorizontal: 18,
    borderRadius: 10,
    shadowColor: '#00AA5B',
    shadowOffset: { width: 0, height: 3 },
    shadowOpacity: 0.25,
    shadowRadius: 6,
    elevation: 3,
  },
  btnDaftarText: { color: '#fff', fontWeight: '700', fontSize: 13 },

  docButtons: { gap: 10 },
  btnDownloadGreen: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 12,
    backgroundColor: '#fff',
    borderRadius: 14,
    padding: 14,
    borderWidth: 1.5,
    borderColor: '#a5d6a7',
    shadowColor: '#00AA5B',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.08,
    shadowRadius: 6,
    elevation: 2,
  },
  btnDownloadBlue: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 12,
    backgroundColor: '#fff',
    borderRadius: 14,
    padding: 14,
    borderWidth: 1.5,
    borderColor: '#81d4fa',
    shadowColor: '#0288d1',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.08,
    shadowRadius: 6,
    elevation: 2,
  },
  btnDownloadIcon: {
    width: 44,
    height: 44,
    borderRadius: 12,
    backgroundColor: '#e8f5e9',
    justifyContent: 'center',
    alignItems: 'center',
  },
  btnDownloadIconBlue: {
    width: 44,
    height: 44,
    borderRadius: 12,
    backgroundColor: '#e1f5fe',
    justifyContent: 'center',
    alignItems: 'center',
  },
  btnDownloadInfo: { flex: 1 },
  btnDownloadTitle: { fontSize: 14, fontWeight: '800', color: '#1a1a2e', marginBottom: 2 },
  btnDownloadSub: { fontSize: 11, color: '#90a4ae' },

  modalOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.4)', justifyContent: 'flex-end' },
  modalSheet: {
    backgroundColor: '#fff',
    borderTopLeftRadius: 24,
    borderTopRightRadius: 24,
    padding: 20,
    maxHeight: '60%',
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
  modalTitle: { fontSize: 16, fontWeight: '800', color: '#1a1a2e' },
  modalClose: {
    width: 32,
    height: 32,
    borderRadius: 16,
    backgroundColor: '#f5faf7',
    justifyContent: 'center',
    alignItems: 'center',
  },
  jadwalItem: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: 14,
    backgroundColor: '#f5faf7',
    borderRadius: 12,
    marginBottom: 8,
    borderWidth: 1,
    borderColor: '#e8f5e9',
  },
  jadwalItemLeft: { flexDirection: 'row', alignItems: 'center', gap: 10 },
  jadwalIconWrap: {
    width: 34,
    height: 34,
    borderRadius: 10,
    backgroundColor: '#e8f5e9',
    justifyContent: 'center',
    alignItems: 'center',
  },
  jadwalTanggal: { fontSize: 14, fontWeight: '700', color: '#1a1a2e' },
  jadwalDetail: { fontSize: 11, color: '#90a4ae', marginTop: 2 },
  kuotaBadge: {
    backgroundColor: '#e8f5e9',
    paddingVertical: 4,
    paddingHorizontal: 10,
    borderRadius: 10,
  },
  kuotaText: { fontSize: 11, color: '#00AA5B', fontWeight: '800' },
  emptyModal: { textAlign: 'center', color: '#90a4ae', marginVertical: 30, fontSize: 13, fontStyle: 'italic' },
});