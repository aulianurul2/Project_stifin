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
  ActivityIndicator,
  StatusBar,
  TouchableWithoutFeedback,
} from 'react-native';
import { useLocalSearchParams, useRouter } from 'expo-router';
import * as FileSystem from 'expo-file-system/legacy';
import * as Sharing from 'expo-sharing';
import Ionicons from '@expo/vector-icons/Ionicons';
import Toast from 'react-native-toast-message';
import axiosInstance from '@/src/api/axiosConfig';

// ─── Daftar kota Ciayumajakuning (sama persis dengan pendaftaran.tsx) ───────
interface KotaCiayuma {
  label: string;
  biaya: number;
}

const KOTA_CIAYUMAJAKUNING: KotaCiayuma[] = [
  { label: 'Kota Cirebon',         biaya: 600000 },
  { label: 'Kabupaten Cirebon',    biaya: 600000 },
  { label: 'Kabupaten Indramayu',  biaya: 610000 },
  { label: 'Kabupaten Majalengka', biaya: 620000 },
  { label: 'Kabupaten Kuningan',   biaya: 630000 },
];

function formatRupiah(nominal: number): string {
  return 'Rp ' + nominal.toLocaleString('id-ID');
}

interface SlotJadwal {
  id_jadwal: number;
  tanggal: string;
  waktu: string;
  lokasi: string;
  kuota: number;
}

export default function HasilTes() {
  const router = useRouter();

  const { id_jadwal, tanggal, jam, file_hasil, file_detail, status, komentar } =
    useLocalSearchParams<{
      id_jadwal:   string;
      tanggal:     string;
      jam:         string;
      file_hasil:  string;
      file_detail: string;
      status:      string;
      komentar:    string;
    }>();

  const [modalRescheduleVisible, setModalRescheduleVisible] = useState(false);
  const [listJadwal, setListJadwal]     = useState<SlotJadwal[]>([]);
  const [loadingJadwal, setLoadingJadwal] = useState(false);
  const [prosesLoading, setProsesLoading] = useState(false);

  // Pilihan lokasi di modal reschedule
  const [rescheduleTempatTes, setRescheduleTempatTes] = useState<'Kantor Subang' | 'Home Visit'>('Kantor Subang');
  const [rescheduleWilayah, setRescheduleWilayah]     = useState<'dalam' | 'luar'>('dalam');

  // Kota Ciayumajakuning yang dipilih di modal reschedule
  const [rescheduleKota, setRescheduleKota]           = useState<KotaCiayuma>(KOTA_CIAYUMAJAKUNING[0]);
  const [rescheduleDropdownOpen, setRescheduleDropdownOpen] = useState(false);

  const isFileHasilAda   = file_hasil  && file_hasil.trim()  !== '' && file_hasil  !== 'null';
  const isFileDetailAda  = file_detail && file_detail.trim() !== '' && file_detail !== 'null';
  const berkasTersedia   = isFileHasilAda || isFileDetailAda;

  const statusLower      = status?.toLowerCase() ?? '';
  const isDitolak        = statusLower === 'ditolak';
  const isDibatalkan     = statusLower === 'dibatalkan';
  const isDiterima       = ['diterima', 'disetujui', 'diproses'].includes(statusLower);
  const belumMengajukan  =
    (!tanggal || tanggal.trim() === '' || tanggal === 'null') &&
    (!jam || jam.trim() === '' || jam === 'null');
  const isMenunggu       = !belumMengajukan && !berkasTersedia && !isDitolak && !isDibatalkan && !isDiterima;
  const isKomentarAda    = komentar && komentar.trim() !== '' && komentar !== 'null';

  // Derived: apakah reschedule ke luar subang
  const rescheduleIsLuar = rescheduleTempatTes === 'Home Visit' && rescheduleWilayah === 'luar';

  // Biaya reschedule
  const getRescheduleBiaya = (): number => {
    if (rescheduleTempatTes !== 'Home Visit') return 550000;
    if (rescheduleWilayah === 'luar') return rescheduleKota.biaya;
    return 550000;
  };

  // Filter jadwal sesuai lokasi
  const filteredRescheduleJadwal = listJadwal.filter((item) => {
    const lokasiDB      = String(item.lokasi || '').toLowerCase().trim();
    const lokasiPilihan = rescheduleTempatTes.toLowerCase().trim();
    return lokasiDB === lokasiPilihan;
  });

  const fetchJadwalTersedia = async () => {
    setLoadingJadwal(true);
    try {
      const BASE_URL = process.env.EXPO_PUBLIC_API_URL;
      const response = await fetch(`${BASE_URL}/jadwal-tersedia`);
      const data     = await response.json();
      setListJadwal(data);
    } catch (error) {
      console.log('Error fetch jadwal:', error);
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
      // Reset pilihan saat modal dibuka
      setRescheduleTempatTes('Kantor Subang');
      setRescheduleWilayah('dalam');
      setRescheduleKota(KOTA_CIAYUMAJAKUNING[0]);
      setRescheduleDropdownOpen(false);
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
      const url      = `${BASE_URL}/uploads/hasil/${encodeURIComponent(fileName)}`;

      if (Platform.OS === 'web') {
        window.open(url, '_blank');
        return;
      }

      const fileUri  = FileSystem.cacheDirectory + fileName;
      const downloadResumable = FileSystem.createDownloadResumable(url, fileUri);
      const result   = await downloadResumable.downloadAsync();

      if (!result) {
        Toast.show({ type: 'error', text1: 'Unduh Gagal', text2: 'Gagal mendownload berkas.', position: 'top' });
        return;
      }
      await Sharing.shareAsync(result.uri);
    } catch (error) {
      Toast.show({ type: 'error', text1: 'Terjadi Kesalahan', text2: `Tidak dapat mengunduh berkas ${titleText}.`, position: 'top' });
    }
  };

// ✅ KODE BARU (Silakan timpa fungsi lama Anda dengan ini):
const handleConfirmReschedule = async (idJadwalBaru: number) => {
  if (!id_jadwal || id_jadwal === 'undefined' || id_jadwal === 'null') {
    Toast.show({ type: 'error', text1: 'Data Tidak Valid', text2: 'ID Jadwal asal tidak valid.', position: 'top' });
    return;
  }

  setModalRescheduleVisible(false);
  setProsesLoading(true);

  try {
    // Siapkan payload data yang aman dan konsisten dengan validasi backend
    const payload = {
      id_jadwal_baru: idJadwalBaru,
      is_luar_subang: rescheduleIsLuar ? 1 : 0, 
      nama_kota:      rescheduleIsLuar ? rescheduleKota.label : null, // Gunakan null alih-alih string kosong
      biaya:          parseInt(String(getRescheduleBiaya()), 10), // Memastikan tipe data berupa integer murni
    };

    const response = await axiosInstance.put(`/pendaftaran/${id_jadwal}/reschedule`, payload);

    if (response.status === 200) {
      Toast.show({
        type: 'success',
        text1: 'Reschedule Berhasil',
        text2: 'Pengajuan perubahan jadwal berhasil terkirim. Menunggu konfirmasi admin.',
        position: 'top',
        visibilityTime: 2500,
      });
      setTimeout(() => { router.replace('/riwayat'); }, 1000);
    }
  } catch (error: any) {
    // Log detail error ke terminal console Anda untuk mempermudah tracking jika terjadi sesuatu
    console.log("Detail Error Reschedule:", error?.response?.data);
    
    const pesanError = error?.response?.data?.message || 'Gagal memproses pengubahan jadwal.';
    Toast.show({ type: 'error', text1: 'Reschedule Gagal', text2: pesanError, position: 'top' });
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
      return { icon: 'alert-circle-outline', color: '#546e7a', bgColor: '#f5f5f5', title: 'Belum Ada Pendaftaran', sub: 'Anda belum mengajukan atau memilih jadwal pendaftaran tes pemeriksaan genetik STIFIn saat ini.' };
    }
    if (berkasTersedia) {
      return { icon: 'checkmark-circle', color: '#00AA5B', bgColor: '#e8f5e9', title: 'Selamat! Tes Anda Selesai', sub: 'Silakan unduh dokumen berkas resmi hasil tes pemeriksaan genetik STIFIn Anda.' };
    }
    if (isDitolak) {
      return { icon: 'close-circle', color: '#e53935', bgColor: '#ffebee', title: 'Maaf, Tes Anda Ditolak', sub: 'Pendaftaran jadwal tes Anda ditolak oleh pihak admin. Silakan periksa Catatan Promotor di bawah.' };
    }
    if (isDibatalkan) {
      return { icon: 'close-circle-outline', color: '#78909c', bgColor: '#f5f5f5', title: 'Pendaftaran Dibatalkan', sub: 'Jadwal pendaftaran ini telah Anda batalkan.' };
    }
    if (isDiterima) {
      return { icon: 'checkmark-circle-outline', color: '#0288d1', bgColor: '#e1f5fe', title: 'Bukti Transfer Terverifikasi', sub: 'Bukti transfer Anda telah diverifikasi oleh admin. Harap menunggu proses tes dan pengerjaan dokumen berkas sertifikat genetik Anda selesai di-upload.' };
    }
    return { icon: 'time-outline', color: '#f57c00', bgColor: '#fff3e0', title: 'Menunggu Verifikasi', sub: 'Bukti transfer Anda sedang menunggu verifikasi oleh admin.' };
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

        {/* Catatan Waktu Verifikasi */}
        {isMenunggu && (
          <View style={styles.verifikasiNote}>
            <View style={styles.verifikasiNoteHeader}>
              <Ionicons name="information-circle-outline" size={16} color="#f57c00" />
              <Text style={styles.verifikasiNoteTitle}>Estimasi Waktu Verifikasi</Text>
            </View>
            <View style={styles.verifikasiNoteRow}>
              <Ionicons name="sunny-outline" size={13} color="#f57c00" />
              <Text style={styles.verifikasiNoteText}>
                <Text style={styles.verifikasiNoteBold}>Jam kerja (08.00–17.00): </Text>
                Maksimal <Text style={styles.verifikasiNoteBold}>30 menit</Text>
              </Text>
            </View>
            <View style={styles.verifikasiNoteRow}>
              <Ionicons name="moon-outline" size={13} color="#f57c00" />
              <Text style={styles.verifikasiNoteText}>
                <Text style={styles.verifikasiNoteBold}>Di luar jam kerja: </Text>
                Maksimal <Text style={styles.verifikasiNoteBold}>1×24 jam</Text>
              </Text>
            </View>
          </View>
        )}

        {/* Meta Info */}
        <View style={styles.metaCard}>
          <View style={styles.metaRow}>
            <View style={styles.metaIconWrap}><Ionicons name="calendar-outline" size={14} color="#00AA5B" /></View>
            <Text style={styles.metaLabel}>Tanggal Pelaksanaan</Text>
            <Text style={styles.metaValue}>{belumMengajukan ? '—' : (tanggal || '—')}</Text>
          </View>
          <View style={styles.metaDivider} />
          <View style={styles.metaRow}>
            <View style={styles.metaIconWrap}><Ionicons name="time-outline" size={14} color="#00AA5B" /></View>
            <Text style={styles.metaLabel}>Waktu Pemeriksaan</Text>
            <Text style={styles.metaValue}>
              {belumMengajukan ? '—' : (jam && jam.trim() !== '' && jam !== 'null' ? `${jam} WIB` : '—')}
            </Text>
          </View>
          <View style={styles.metaDivider} />
          <View style={styles.metaRow}>
            <View style={styles.metaIconWrap}><Ionicons name="pulse-outline" size={14} color="#00AA5B" /></View>
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
                color={isDitolak ? '#e53935' : '#00AA5B'}
              />
              <Text style={[styles.commentTitle, isDitolak && { color: '#c62828' }]}>
                {isDitolak ? 'Alasan Penolakan' : 'Catatan Promotor'}
              </Text>
            </View>
            <Text style={styles.commentText}>{komentar}</Text>
          </View>
        )}

        {/* Action Buttons */}
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
            <TouchableOpacity
              style={styles.btnDaftar}
              onPress={() => router.push('/pendaftaran')}
              activeOpacity={0.85}
            >
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
              {isDitolak || isDibatalkan ? 'Berkas Tidak Tersedia' : 'Berkas Belum Di-upload'}
            </Text>
            <Text style={styles.emptyDocSub}>
              {isDitolak || isDibatalkan
                ? 'Jadwal tidak aktif, tidak ada dokumen yang diterbitkan.'
                : 'Admin belum mengunggah dokumen hasil tes untuk jadwal ini.'}
            </Text>
          </View>
        ) : (
          <View style={styles.docButtons}>
            {isFileHasilAda && (
              <TouchableOpacity
                style={styles.btnDownloadGreen}
                onPress={() => downloadFile(file_hasil, 'Sertifikat')}
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
                onPress={() => downloadFile(file_detail, 'Detail Tes')}
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

      {/* ═══════════════════════════════════════════════════
          RESCHEDULE MODAL
      ═══════════════════════════════════════════════════ */}
      <Modal
        animationType="slide"
        transparent={true}
        visible={modalRescheduleVisible}
        onRequestClose={() => setModalRescheduleVisible(false)}
      >
        <TouchableWithoutFeedback onPress={() => setModalRescheduleVisible(false)}>
          <View style={styles.modalOverlay}>
            <TouchableWithoutFeedback onPress={(e) => e.stopPropagation()}>
              <View style={styles.modalSheet}>

                <View style={styles.modalHandle} />

                <View style={styles.modalHeader}>
                  <View>
                    <Text style={styles.modalTitle}>Ajukan Reschedule</Text>
                    <Text style={styles.modalSub}>Pilih lokasi dan jadwal pengganti</Text>
                  </View>
                  <TouchableOpacity style={styles.modalClose} onPress={() => setModalRescheduleVisible(false)}>
                    <Ionicons name="close" size={20} color="#90a4ae" />
                  </TouchableOpacity>
                </View>

                {/* ScrollView bisa di-scroll termasuk dropdown */}
                <ScrollView showsVerticalScrollIndicator={false} nestedScrollEnabled>

                  {/* Pilihan Lokasi */}
                  <View style={styles.modalSection}>
                    <Text style={styles.modalSectionLabel}>
                      <Ionicons name="location-outline" size={12} color="#546e7a" /> Lokasi Tes
                    </Text>
                    <View style={styles.chipRow}>
                      {(['Kantor Subang', 'Home Visit'] as const).map((val) => (
                        <TouchableOpacity
                          key={val}
                          style={[styles.chip, rescheduleTempatTes === val && styles.chipActive]}
                          onPress={() => {
                            setRescheduleTempatTes(val);
                            setRescheduleWilayah('dalam');
                            setRescheduleDropdownOpen(false);
                          }}
                          activeOpacity={0.7}
                        >
                          <Ionicons
                            name={val === 'Home Visit' ? 'home-outline' : 'business-outline'}
                            size={13}
                            color={rescheduleTempatTes === val ? '#fff' : '#546e7a'}
                          />
                          <Text style={[styles.chipText, rescheduleTempatTes === val && styles.chipTextActive]}>
                            {val}
                          </Text>
                        </TouchableOpacity>
                      ))}
                    </View>
                  </View>

                  {/* Pilihan Wilayah — hanya muncul jika Home Visit */}
                  {rescheduleTempatTes === 'Home Visit' && (
                    <View style={styles.modalSection}>
                      <Text style={styles.modalSectionLabel}>
                        <Ionicons name="cash-outline" size={12} color="#546e7a" /> Wilayah
                      </Text>
                      <View style={styles.chipRow}>
                        <TouchableOpacity
                          style={[styles.wilayahChip, rescheduleWilayah === 'dalam' && styles.chipActive]}
                          onPress={() => { setRescheduleWilayah('dalam'); setRescheduleDropdownOpen(false); }}
                          activeOpacity={0.7}
                        >
                          <Text style={[styles.wilayahChipText, rescheduleWilayah === 'dalam' && styles.chipTextActive]}>
                            Dalam Subang
                          </Text>
                          <Text style={[styles.wilayahHarga, rescheduleWilayah === 'dalam' && styles.wilayahHargaActive]}>
                            Rp 550.000
                          </Text>
                        </TouchableOpacity>

                        <TouchableOpacity
                          style={[styles.wilayahChip, rescheduleWilayah === 'luar' && styles.chipActive]}
                          onPress={() => setRescheduleWilayah('luar')}
                          activeOpacity={0.7}
                        >
                          <Text style={[styles.wilayahChipText, rescheduleWilayah === 'luar' && styles.chipTextActive]}>
                            Ciayumajakuning
                          </Text>
                          <Text style={[styles.wilayahHarga, rescheduleWilayah === 'luar' && styles.wilayahHargaActive]}>
                            ab Rp 600.000
                          </Text>
                        </TouchableOpacity>
                      </View>

                      {/* Dropdown kota Ciayumajakuning */}
                      {rescheduleWilayah === 'luar' && (
                        <View style={styles.kotaBox}>
                          <Text style={styles.kotaLabel}>
                            <Ionicons name="business-outline" size={12} color="#546e7a" /> Pilih Kota / Kabupaten
                          </Text>

                          <TouchableOpacity
                            style={styles.dropdownTrigger}
                            onPress={() => setRescheduleDropdownOpen(!rescheduleDropdownOpen)}
                            activeOpacity={0.8}
                          >
                            <View style={styles.dropdownTriggerLeft}>
                              <Ionicons name="location-outline" size={15} color="#00AA5B" />
                              <Text style={styles.dropdownTriggerText}>{rescheduleKota.label}</Text>
                            </View>
                            <View style={styles.dropdownTriggerRight}>
                              <Text style={styles.dropdownTriggerHarga}>
                                {formatRupiah(rescheduleKota.biaya)}
                              </Text>
                              <Ionicons
                                name={rescheduleDropdownOpen ? 'chevron-up' : 'chevron-down'}
                                size={16}
                                color="#546e7a"
                              />
                            </View>
                          </TouchableOpacity>

                          {rescheduleDropdownOpen && (
                            <View style={styles.dropdownList}>
                              {KOTA_CIAYUMAJAKUNING.map((kota, index) => {
                                const isActive = rescheduleKota.label === kota.label;
                                return (
                                  <TouchableOpacity
                                    key={kota.label}
                                    style={[
                                      styles.dropdownItem,
                                      isActive && styles.dropdownItemActive,
                                      index < KOTA_CIAYUMAJAKUNING.length - 1 && styles.dropdownItemBorder,
                                    ]}
                                    onPress={() => {
                                      setRescheduleKota(kota);
                                      setRescheduleDropdownOpen(false);
                                    }}
                                    activeOpacity={0.7}
                                  >
                                    <View style={styles.dropdownItemLeft}>
                                      <Ionicons
                                        name={isActive ? 'checkmark-circle' : 'radio-button-off-outline'}
                                        size={16}
                                        color={isActive ? '#00AA5B' : '#b0bec5'}
                                      />
                                      <Text style={[styles.dropdownItemText, isActive && styles.dropdownItemTextActive]}>
                                        {kota.label}
                                      </Text>
                                    </View>
                                    <Text style={[styles.dropdownItemHarga, isActive && styles.dropdownItemHargaActive]}>
                                      {formatRupiah(kota.biaya)}
                                    </Text>
                                  </TouchableOpacity>
                                );
                              })}
                            </View>
                          )}
                        </View>
                      )}
                    </View>
                  )}

                  {/* Info Biaya */}
                  <View style={styles.biayaInfoRow}>
                    <Ionicons name="pricetag-outline" size={13} color="#00AA5B" />
                    <View style={{ flex: 1 }}>
                      <Text style={styles.biayaInfoText}>
                        Biaya:{' '}
                        <Text style={styles.biayaInfoNominal}>
                          {formatRupiah(getRescheduleBiaya())}
                        </Text>
                        {rescheduleIsLuar && (
                          <Text style={styles.biayaInfoKota}> ({rescheduleKota.label})</Text>
                        )}
                      </Text>
                      <Text style={styles.biayaInfoNote}>* Belum termasuk biaya admin antar bank</Text>
                    </View>
                  </View>

                  <View style={styles.modalDivider} />

                  <Text style={[styles.modalSectionLabel, { marginBottom: 10 }]}>
                    <Ionicons name="calendar-outline" size={12} color="#546e7a" /> Pilih Slot Jadwal
                  </Text>

                  {loadingJadwal ? (
                    <View style={styles.modalLoading}>
                      <ActivityIndicator size="large" color="#00AA5B" />
                      <Text style={styles.modalLoadingText}>Memuat jadwal tersedia...</Text>
                    </View>
                  ) : filteredRescheduleJadwal.length === 0 ? (
                    <View style={styles.modalEmpty}>
                      <View style={styles.modalEmptyIcon}>
                        <Ionicons name="calendar-outline" size={32} color="#90a4ae" />
                      </View>
                      <Text style={styles.modalEmptyTitle}>Tidak Ada Jadwal</Text>
                      <Text style={styles.emptyModal}>
                        Tidak ada slot tersedia untuk lokasi ini saat ini.
                      </Text>
                    </View>
                  ) : (
                    filteredRescheduleJadwal.map((item) => (
                      <TouchableOpacity
                        key={item.id_jadwal.toString()}
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
                        <View style={styles.jadwalRight}>
                          <View style={styles.kuotaBadge}>
                            <Text style={styles.kuotaText}>Kuota: {item.kuota}</Text>
                          </View>
                          <Ionicons name="chevron-forward" size={16} color="#00AA5B" />
                        </View>
                      </TouchableOpacity>
                    ))
                  )}

                </ScrollView>

                <TouchableOpacity
                  style={styles.modalBtnBatal}
                  onPress={() => setModalRescheduleVisible(false)}
                  activeOpacity={0.8}
                >
                  <Text style={styles.modalBtnBatalText}>Batal</Text>
                </TouchableOpacity>

              </View>
            </TouchableWithoutFeedback>
          </View>
        </TouchableWithoutFeedback>
      </Modal>

    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f5faf7' },

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
    width: 38, height: 38, borderRadius: 19,
    backgroundColor: 'rgba(255,255,255,0.2)',
    justifyContent: 'center', alignItems: 'center',
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
    backgroundColor: '#fff', borderRadius: 16,
    padding: 24, alignItems: 'center', gap: 12,
  },
  loadingText: { fontSize: 14, fontWeight: '600', color: '#546e7a' },

  statusCard: {
    backgroundColor: '#fff', borderRadius: 16,
    padding: 16, flexDirection: 'row', gap: 14,
    marginBottom: 14, borderWidth: 1, borderColor: '#e8f5e9',
    borderLeftWidth: 4,
    shadowColor: '#00AA5B', shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05, shadowRadius: 6, elevation: 2,
  },
  statusIconWrap: { width: 64, height: 64, borderRadius: 16, justifyContent: 'center', alignItems: 'center' },
  statusCardRight: { flex: 1 },
  statusTitle: { fontSize: 15, fontWeight: '800', marginBottom: 4 },
  statusSub: { fontSize: 12, color: '#78909c', lineHeight: 17 },

  verifikasiNote: {
    backgroundColor: '#fff8f0', borderRadius: 14,
    padding: 14, marginBottom: 14,
    borderWidth: 1, borderColor: '#ffe0b2',
    borderLeftWidth: 3, borderLeftColor: '#f57c00', gap: 8,
  },
  verifikasiNoteHeader: { flexDirection: 'row', alignItems: 'center', gap: 6, marginBottom: 4 },
  verifikasiNoteTitle: { fontSize: 13, fontWeight: '800', color: '#e65100' },
  verifikasiNoteRow: { flexDirection: 'row', alignItems: 'flex-start', gap: 6 },
  verifikasiNoteText: { fontSize: 12, color: '#78909c', lineHeight: 18, flex: 1 },
  verifikasiNoteBold: { fontWeight: '700', color: '#546e7a' },

  metaCard: {
    backgroundColor: '#fff', borderRadius: 16,
    padding: 16, marginBottom: 14,
    borderWidth: 1, borderColor: '#e8f5e9',
    shadowColor: '#00AA5B', shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05, shadowRadius: 6, elevation: 2,
  },
  metaRow: { flexDirection: 'row', alignItems: 'center', gap: 8 },
  metaIconWrap: { width: 26, height: 26, borderRadius: 8, backgroundColor: '#e8f5e9', justifyContent: 'center', alignItems: 'center' },
  metaLabel: { flex: 1, fontSize: 13, color: '#78909c', fontWeight: '600' },
  metaValue: { fontSize: 13, fontWeight: '700', color: '#1a1a2e' },
  metaDivider: { height: 1, backgroundColor: '#f5faf7', marginVertical: 12 },
  statusPill: { borderRadius: 12, paddingHorizontal: 10, paddingVertical: 3 },
  statusPillText: { fontSize: 12, fontWeight: '800' },

  commentCard: { backgroundColor: '#e8f5e9', borderRadius: 14, padding: 14, borderLeftWidth: 3, borderLeftColor: '#00AA5B', marginBottom: 14 },
  commentCardDitolak: { backgroundColor: '#ffebee', borderLeftColor: '#e53935' },
  commentHeader: { flexDirection: 'row', alignItems: 'center', gap: 6, marginBottom: 8 },
  commentTitle: { fontSize: 13, fontWeight: '800', color: '#00AA5B' },
  commentText: { fontSize: 13, color: '#37474f', lineHeight: 19, fontStyle: 'italic' },

  sectionLabel: { fontSize: 13, fontWeight: '800', color: '#546e7a', marginBottom: 10, paddingLeft: 2 },

  actionSection: { marginBottom: 16 },
  btnReschedule: {
    flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 6,
    paddingVertical: 13, borderRadius: 12, backgroundColor: '#f57c00',
    shadowColor: '#f57c00', shadowOffset: { width: 0, height: 3 }, shadowOpacity: 0.25, shadowRadius: 6, elevation: 3,
  },
  btnRescheduleText: { color: '#fff', fontWeight: '700', fontSize: 13 },

  emptyDoc: {
    backgroundColor: '#fff', borderRadius: 16, padding: 28, alignItems: 'center',
    borderWidth: 1.5, borderColor: '#e0f2ec', borderStyle: 'dashed',
  },
  emptyDocIcon: { width: 72, height: 72, borderRadius: 36, backgroundColor: '#f5faf7', justifyContent: 'center', alignItems: 'center', marginBottom: 12 },
  emptyDocTitle: { fontSize: 15, fontWeight: '800', color: '#546e7a', marginBottom: 6 },
  emptyDocSub: { fontSize: 12, color: '#90a4ae', textAlign: 'center', lineHeight: 17, marginBottom: 16 },
  btnDaftar: {
    flexDirection: 'row', alignItems: 'center', gap: 6,
    backgroundColor: '#00AA5B', paddingVertical: 10, paddingHorizontal: 18, borderRadius: 10,
    shadowColor: '#00AA5B', shadowOffset: { width: 0, height: 3 }, shadowOpacity: 0.25, shadowRadius: 6, elevation: 3,
  },
  btnDaftarText: { color: '#fff', fontWeight: '700', fontSize: 13 },

  docButtons: { gap: 10 },
  btnDownloadGreen: {
    flexDirection: 'row', alignItems: 'center', gap: 12, backgroundColor: '#fff', borderRadius: 14, padding: 14,
    borderWidth: 1.5, borderColor: '#a5d6a7',
    shadowColor: '#00AA5B', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.08, shadowRadius: 6, elevation: 2,
  },
  btnDownloadBlue: {
    flexDirection: 'row', alignItems: 'center', gap: 12, backgroundColor: '#fff', borderRadius: 14, padding: 14,
    borderWidth: 1.5, borderColor: '#81d4fa',
    shadowColor: '#0288d1', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.08, shadowRadius: 6, elevation: 2,
  },
  btnDownloadIcon: { width: 44, height: 44, borderRadius: 12, backgroundColor: '#e8f5e9', justifyContent: 'center', alignItems: 'center' },
  btnDownloadIconBlue: { width: 44, height: 44, borderRadius: 12, backgroundColor: '#e1f5fe', justifyContent: 'center', alignItems: 'center' },
  btnDownloadInfo: { flex: 1 },
  btnDownloadTitle: { fontSize: 14, fontWeight: '800', color: '#1a1a2e', marginBottom: 2 },
  btnDownloadSub: { fontSize: 11, color: '#90a4ae' },

  // Modal
  modalOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.5)', justifyContent: 'flex-end' },
  modalSheet: {
    backgroundColor: '#fff', borderTopLeftRadius: 28, borderTopRightRadius: 28,
    paddingTop: 12, paddingHorizontal: 20, paddingBottom: 32, height: '88%',
  },
  modalHandle: { width: 40, height: 4, borderRadius: 2, backgroundColor: '#e0f2ec', alignSelf: 'center', marginBottom: 16 },
  modalHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 16 },
  modalTitle: { fontSize: 16, fontWeight: '800', color: '#1a1a2e' },
  modalSub: { fontSize: 11, color: '#90a4ae', marginTop: 3 },
  modalClose: { width: 32, height: 32, borderRadius: 16, backgroundColor: '#f5faf7', justifyContent: 'center', alignItems: 'center' },

  modalSection: { marginBottom: 14 },
  modalSectionLabel: { fontSize: 11, fontWeight: '700', color: '#546e7a', textTransform: 'uppercase', letterSpacing: 0.6, marginBottom: 10 },
  chipRow: { flexDirection: 'row', gap: 10 },
  chip: {
    flex: 1, flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 6,
    paddingVertical: 10, borderRadius: 12, backgroundColor: '#f5faf7', borderWidth: 1.5, borderColor: '#e0f2ec',
  },
  chipActive: { backgroundColor: '#00AA5B', borderColor: '#00AA5B' },
  chipText: { fontSize: 13, fontWeight: '700', color: '#546e7a' },
  chipTextActive: { color: '#fff' },

  wilayahChip: {
    flex: 1, alignItems: 'center', paddingVertical: 10, borderRadius: 12,
    backgroundColor: '#f5faf7', borderWidth: 1.5, borderColor: '#e0f2ec',
  },
  wilayahChipText: { fontSize: 12, fontWeight: '700', color: '#546e7a', marginBottom: 2 },
  wilayahHarga: { fontSize: 11, fontWeight: '600', color: '#90a4ae' },
  wilayahHargaActive: { color: 'rgba(255,255,255,0.85)' },

  // Kota dropdown (dipakai di halaman pendaftaran dan modal reschedule)
  kotaBox: { marginTop: 12, paddingTop: 12, borderTopWidth: 1, borderTopColor: '#e8f5e9' },
  kotaLabel: { fontSize: 11, fontWeight: '700', color: '#546e7a', textTransform: 'uppercase', letterSpacing: 0.6, marginBottom: 8 },
  dropdownTrigger: {
    flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between',
    backgroundColor: '#f5faf7', borderRadius: 12, borderWidth: 1.5, borderColor: '#a5d6a7',
    paddingHorizontal: 14, paddingVertical: 12,
  },
  dropdownTriggerLeft: { flexDirection: 'row', alignItems: 'center', gap: 8 },
  dropdownTriggerText: { fontSize: 14, fontWeight: '700', color: '#1a1a2e' },
  dropdownTriggerRight: { flexDirection: 'row', alignItems: 'center', gap: 8 },
  dropdownTriggerHarga: { fontSize: 13, fontWeight: '800', color: '#00AA5B' },
  dropdownList: {
    marginTop: 6, backgroundColor: '#fff', borderRadius: 12, borderWidth: 1.5, borderColor: '#e0f2ec', overflow: 'hidden',
    shadowColor: '#00AA5B', shadowOffset: { width: 0, height: 4 }, shadowOpacity: 0.08, shadowRadius: 8, elevation: 3,
  },
  dropdownItem: {
    flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between',
    paddingHorizontal: 14, paddingVertical: 13, backgroundColor: '#fff',
  },
  dropdownItemActive: { backgroundColor: '#f0faf5' },
  dropdownItemBorder: { borderBottomWidth: 1, borderBottomColor: '#f0f4f0' },
  dropdownItemLeft: { flexDirection: 'row', alignItems: 'center', gap: 10 },
  dropdownItemText: { fontSize: 13, fontWeight: '600', color: '#37474f' },
  dropdownItemTextActive: { color: '#00AA5B', fontWeight: '800' },
  dropdownItemHarga: { fontSize: 12, fontWeight: '700', color: '#90a4ae' },
  dropdownItemHargaActive: { color: '#00AA5B' },

  biayaInfoRow: {
    flexDirection: 'row', alignItems: 'flex-start', flexWrap: 'wrap', gap: 6,
    paddingVertical: 10, paddingHorizontal: 12,
    backgroundColor: '#f5faf7', borderRadius: 10, borderWidth: 1, borderColor: '#e8f5e9', marginBottom: 14,
  },
  biayaInfoText: { fontSize: 13, color: '#546e7a', fontWeight: '600' },
  biayaInfoNominal: { color: '#00AA5B', fontWeight: '800' },
  biayaInfoKota: { color: '#78909c', fontWeight: '600', fontSize: 12 },
  biayaInfoNote: { fontSize: 10, color: '#90a4ae', fontStyle: 'italic', flex: 1 },

  modalDivider: { height: 1, backgroundColor: '#e8f5e9', marginBottom: 14 },

  modalLoading: { paddingVertical: 40, alignItems: 'center', gap: 12 },
  modalLoadingText: { color: '#90a4ae', fontSize: 13 },
  modalEmpty: { alignItems: 'center', paddingVertical: 32, gap: 8 },
  modalEmptyIcon: { width: 64, height: 64, borderRadius: 32, backgroundColor: '#f5faf7', justifyContent: 'center', alignItems: 'center', marginBottom: 8 },
  modalEmptyTitle: { fontSize: 15, fontWeight: '700', color: '#546e7a' },

  jadwalItem: {
    flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center',
    padding: 14, backgroundColor: '#f5faf7', borderRadius: 14, marginBottom: 10,
    borderWidth: 1, borderColor: '#e8f5e9',
  },
  jadwalItemLeft: { flexDirection: 'row', alignItems: 'center', gap: 12 },
  jadwalRight: { flexDirection: 'row', alignItems: 'center', gap: 6 },
  jadwalIconWrap: { width: 38, height: 38, borderRadius: 10, backgroundColor: '#e8f5e9', justifyContent: 'center', alignItems: 'center' },
  jadwalTanggal: { fontSize: 14, fontWeight: '700', color: '#1a1a2e' },
  jadwalDetail: { fontSize: 11, color: '#90a4ae', marginTop: 2 },
  kuotaBadge: { backgroundColor: '#e8f5e9', paddingVertical: 4, paddingHorizontal: 10, borderRadius: 10 },
  kuotaText: { fontSize: 11, color: '#00AA5B', fontWeight: '800' },
  emptyModal: { textAlign: 'center', color: '#90a4ae', fontSize: 13, fontStyle: 'italic' },
  modalScrollContent: { paddingBottom: 8 },

  modalBtnBatal: {
    marginTop: 12, paddingVertical: 14, borderRadius: 14, alignItems: 'center',
    backgroundColor: '#f5faf7', borderWidth: 1.5, borderColor: '#e0f2ec',
  },
  modalBtnBatalText: { fontSize: 14, fontWeight: '700', color: '#546e7a' },
});