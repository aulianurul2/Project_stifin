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
  Linking,
} from 'react-native';
import { useLocalSearchParams, useRouter } from 'expo-router';
import * as FileSystem from 'expo-file-system/legacy';
import * as Sharing from 'expo-sharing';
import Ionicons from '@expo/vector-icons/Ionicons';
import Toast from 'react-native-toast-message';
import axiosInstance from '@/src/api/axiosConfig';

// ─── Types ───────────────────────────────────────────────────────────────────

interface TransportOption {
  label: string;
  biaya: number;
}

interface SlotJadwal {
  id_jadwal: number;
  tanggal: string;
  waktu: string;
  lokasi: string;
  kuota: number;
}

interface StatusCardConfig {
  icon: keyof typeof Ionicons.glyphMap;
  color: string;
  bgColor: string;
  title: string;
  sub: string;
}

// ─── Constants ───────────────────────────────────────────────────────────────

const TRANSPORT_DALAM_SUBANG: TransportOption[] = [
  { label: 'Kota Subang', biaya: 25000 },
  { label: 'Kab. Subang', biaya: 50000 },
];

const BIAYA_TES          = 550000;
const BIAYA_TRANSPORT_LUAR = 75000;

// ─── Helpers ─────────────────────────────────────────────────────────────────

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

function hariSejakJadwal(tanggalStr: string): number {
  if (!tanggalStr || tanggalStr.trim() === '' || tanggalStr === 'null') return 0;

  let jadwalDate: Date | null = null;
  const dmyMatch = tanggalStr.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/);

  if (dmyMatch) {
    jadwalDate = new Date(
      parseInt(dmyMatch[3]),
      parseInt(dmyMatch[2]) - 1,
      parseInt(dmyMatch[1])
    );
  } else {
    jadwalDate = new Date(tanggalStr);
  }

  if (!jadwalDate || isNaN(jadwalDate.getTime())) return 0;

  const today = new Date();
  today.setHours(0, 0, 0, 0);
  jadwalDate.setHours(0, 0, 0, 0);

  return Math.floor((today.getTime() - jadwalDate.getTime()) / (1000 * 60 * 60 * 24));
}

// ─── Component ───────────────────────────────────────────────────────────────

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

  // ── State ──
  const [modalRescheduleVisible, setModalRescheduleVisible] = useState(false);
  const [listJadwal, setListJadwal]       = useState<SlotJadwal[]>([]);
  const [loadingJadwal, setLoadingJadwal] = useState(false);
  const [prosesLoading, setProsesLoading] = useState(false);

  const [adminWa1, setAdminWa1] = useState('6282127747105');
  const [adminWa2, setAdminWa2] = useState('6281224595556');

  const [rescheduleTempatTes, setRescheduleTempatTes] =
    useState<'Kantor Subang' | 'Home Visit'>('Kantor Subang');
  const [rescheduleWilayah, setRescheduleWilayah] =
    useState<'dalam' | 'luar'>('dalam');
  const [rescheduleTransport, setRescheduleTransport] =
    useState<TransportOption>(TRANSPORT_DALAM_SUBANG[0]);
  const [rescheduleDropdownOpen, setRescheduleDropdownOpen] = useState(false);

  // ── Derived ──
  const isFileHasilAda  = file_hasil  && file_hasil.trim()  !== '' && file_hasil  !== 'null';
  const isFileDetailAda = file_detail && file_detail.trim() !== '' && file_detail !== 'null';
  const berkasTersedia  = isFileHasilAda || isFileDetailAda;

  const statusLower  = status?.toLowerCase() ?? '';
  const isDitolak    = statusLower === 'ditolak';
  const isDibatalkan = statusLower === 'dibatalkan';
  const isDiterima   = ['diterima', 'disetujui', 'diproses', 'terjadwal'].includes(statusLower);

  const belumMengajukan =
    (!tanggal || tanggal.trim() === '' || tanggal === 'null') &&
    (!jam     || jam.trim()     === '' || jam     === 'null');

  const isMenunggu    = !belumMengajukan && !berkasTersedia && !isDitolak && !isDibatalkan && !isDiterima;
  const isKomentarAda = komentar && komentar.trim() !== '' && komentar !== 'null';

  const selisihHari             = hariSejakJadwal(tanggal ?? '');
  const melewatiBatasReschedule = selisihHari > 14;
  const sisaHariReschedule      = Math.max(0, 14 - selisihHari);
  const rescheduleIsLuar        = rescheduleTempatTes === 'Home Visit' && rescheduleWilayah === 'luar';

  const filteredRescheduleJadwal = listJadwal.filter((item) =>
    String(item.lokasi || '').toLowerCase().trim() ===
    rescheduleTempatTes.toLowerCase().trim()
  );

  // ── Biaya helpers ──
  const getRescheduleBiaya = (): number => {
    if (rescheduleTempatTes !== 'Home Visit') return BIAYA_TES;
    if (rescheduleWilayah === 'luar') return BIAYA_TES + BIAYA_TRANSPORT_LUAR;
    return BIAYA_TES + rescheduleTransport.biaya;
  };

  const getRescheduleTransportBiaya = (): number => {
    if (rescheduleTempatTes !== 'Home Visit') return 0;
    if (rescheduleWilayah === 'luar') return BIAYA_TRANSPORT_LUAR;
    return rescheduleTransport.biaya;
  };

  // ── Effects ──
  useEffect(() => {
    const fetchAdminContact = async () => {
      try {
        const BASE_URL = process.env.EXPO_PUBLIC_API_URL;
        const res  = await fetch(`${BASE_URL}/admin-contact`);
        const data = await res.json();
        if (data.wa1) setAdminWa1(data.wa1);
        if (data.wa2) setAdminWa2(data.wa2);
      } catch (e) {
        console.log('Gagal fetch admin contact:', e);
      }
    };
    fetchAdminContact();
  }, []);

  useEffect(() => {
    if (!modalRescheduleVisible) return;
    fetchJadwalTersedia();
    setRescheduleTempatTes('Kantor Subang');
    setRescheduleWilayah('dalam');
    setRescheduleTransport(TRANSPORT_DALAM_SUBANG[0]);
    setRescheduleDropdownOpen(false);
  }, [modalRescheduleVisible]);

  // ── Handlers ──
  const fetchJadwalTersedia = async () => {
    setLoadingJadwal(true);
    try {
      const BASE_URL = process.env.EXPO_PUBLIC_API_URL;
      const response = await fetch(`${BASE_URL}/jadwal-tersedia`);
      setListJadwal(await response.json());
    } catch {
      Toast.show({ type: 'error', text1: 'Gagal Memuat', text2: 'Gagal memuat slot jadwal baru.', position: 'top' });
    } finally {
      setLoadingJadwal(false);
    }
  };

  const handleOpenReschedule = () => {
    if (melewatiBatasReschedule) {
      Toast.show({
        type: 'error',
        text1: 'Batas Reschedule Terlewat',
        text2: 'Silakan hubungi admin.',
        position: 'top',
        visibilityTime: 3500,
      });
      return;
    }
    setModalRescheduleVisible(true);
  };

  const handleConfirmReschedule = async (idJadwalBaru: number) => {
    if (!id_jadwal || id_jadwal === 'undefined' || id_jadwal === 'null') {
      Toast.show({ type: 'error', text1: 'Data Tidak Valid', text2: 'ID Jadwal asal tidak valid.', position: 'top' });
      return;
    }
    if (melewatiBatasReschedule) {
      Toast.show({ type: 'error', text1: 'Batas Reschedule Terlewat', text2: 'Tidak dapat mengajukan reschedule.', position: 'top' });
      return;
    }

    setModalRescheduleVisible(false);
    setProsesLoading(true);

    try {
      const payload = {
        id_jadwal_baru: idJadwalBaru,
        is_luar_subang: rescheduleIsLuar ? 1 : 0,
        nama_kota: !rescheduleIsLuar && rescheduleTempatTes === 'Home Visit'
          ? rescheduleTransport.label
          : null,
        biaya: getRescheduleBiaya(),
      };

      const response = await axiosInstance.put(`/pendaftaran/${id_jadwal}/reschedule`, payload);
      if (response.status === 200) {
        Toast.show({
          type: 'success',
          text1: 'Reschedule Berhasil',
          text2: 'Menunggu konfirmasi dari admin.',
          position: 'top',
          visibilityTime: 3000,
        });
        setTimeout(() => router.replace('/riwayat'), 1000);
      }
    } catch (error: any) {
      const pesanError = error?.response?.data?.message || 'Gagal memproses pengubahan jadwal.';
      Toast.show({ type: 'error', text1: 'Reschedule Gagal', text2: pesanError, position: 'top' });
    } finally {
      setProsesLoading(false);
    }
  };

  const downloadFile = async (fileName: string | undefined, titleText: string) => {
    try {
      if (!fileName || fileName.trim() === '' || fileName === 'null') {
        Toast.show({ type: 'error', text1: 'File Tidak Tersedia', text2: `File ${titleText} belum diunggah oleh admin.`, position: 'top' });
        return;
      }

      const BASE_URL = process.env.EXPO_PUBLIC_API_URL?.replace('/api', '');
      const url      = `${BASE_URL}/uploads/hasil/${encodeURIComponent(fileName)}`;

      if (Platform.OS === 'web') { window.open(url, '_blank'); return; }

      const fileUri  = FileSystem.cacheDirectory + fileName;
      const result   = await FileSystem.createDownloadResumable(url, fileUri).downloadAsync();

      if (!result) {
        Toast.show({ type: 'error', text1: 'Unduh Gagal', text2: 'Gagal mendownload berkas.', position: 'top' });
        return;
      }
      await Sharing.shareAsync(result.uri);
    } catch {
      Toast.show({ type: 'error', text1: 'Terjadi Kesalahan', text2: `Tidak dapat mengunduh berkas ${titleText}.`, position: 'top' });
    }
  };

  const toLokalFormat = (nomor: string) =>
    nomor.startsWith('62') ? '0' + nomor.slice(2) : nomor;

  const buildWaPesan = (topik: string) =>
    encodeURIComponent(`Halo Admin, saya ingin menanyakan terkait ${topik} tes STIFIn saya. Mohon bantuannya, terima kasih.`);

  const hubungiAdmin = (nomor: string, topik: string) =>
    Linking.openURL(`https://wa.me/${nomor}?text=${buildWaPesan(topik)}`);

  // ── Status card config ──
  const getStatusCardConfig = (): StatusCardConfig => {
    if (belumMengajukan)  return { icon: 'alert-circle-outline', color: '#546e7a', bgColor: '#f5f5f5',  title: 'Belum Ada Pendaftaran',        sub: 'Anda belum mengajukan atau memilih jadwal pendaftaran tes pemeriksaan genetik STIFIn saat ini.' };
    if (berkasTersedia)   return { icon: 'checkmark-circle',      color: '#00AA5B', bgColor: '#e8f5e9',  title: 'Selamat! Tes Anda Selesai',     sub: 'Silakan unduh dokumen berkas resmi hasil tes pemeriksaan genetik STIFIn Anda.' };
    if (isDitolak)        return { icon: 'close-circle',           color: '#e53935', bgColor: '#ffebee',  title: 'Maaf, Tes Anda Ditolak',        sub: 'Pendaftaran jadwal tes Anda ditolak oleh pihak admin. Silakan periksa Catatan Promotor di bawah.' };
    if (isDibatalkan)     return { icon: 'close-circle-outline',   color: '#78909c', bgColor: '#f5f5f5',  title: 'Pendaftaran Dibatalkan',        sub: 'Jadwal pendaftaran ini telah Anda batalkan.' };
    if (isDiterima)       return { icon: 'checkmark-circle-outline', color: '#0288d1', bgColor: '#e1f5fe', title: 'Pembayaran Terverifikasi', sub: 'Bukti transfer Anda telah diverifikasi. Harap hadir sesuai jadwal yang telah ditentukan.' };
    return                       { icon: 'time-outline',            color: '#f57c00', bgColor: '#fff3e0',  title: 'Menunggu Verifikasi',           sub: 'Bukti transfer Anda sedang menunggu verifikasi oleh admin.' };
  };

  const cardInfo = getStatusCardConfig();

  // ─────────────────────────────────────────────────────────────────────────
  // Render
  // ─────────────────────────────────────────────────────────────────────────

  return (
    <SafeAreaView style={styles.container}>

      {/* Top Bar */}
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

      {/* Loading Overlay */}
      {prosesLoading && (
        <View style={styles.loadingOverlay}>
          <View style={styles.loadingBox}>
            <ActivityIndicator size="large" color="#00AA5B" />
            <Text style={styles.loadingText}>Memproses Permintaan...</Text>
          </View>
        </View>
      )}

      <ScrollView contentContainerStyle={styles.content} showsVerticalScrollIndicator={false}>

        {/* ── Status Card ── */}
        <View style={[styles.statusCard, { borderLeftColor: cardInfo.color }]}>
          <View style={[styles.statusIconWrap, { backgroundColor: cardInfo.bgColor }]}>
            <Ionicons name={cardInfo.icon} size={36} color={cardInfo.color} />
          </View>
          <View style={styles.statusCardRight}>
            <Text style={[styles.statusTitle, { color: cardInfo.color }]}>{cardInfo.title}</Text>
            <Text style={styles.statusSub}>{cardInfo.sub}</Text>
          </View>
        </View>

        {/* ── Estimasi Verifikasi ── */}
        {isMenunggu && (
          <View style={styles.verifikasiNote}>
            <View style={styles.rowGap6}>
              <Ionicons name="information-circle-outline" size={16} color="#f57c00" />
              <Text style={styles.verifikasiNoteTitle}>Estimasi Waktu Verifikasi</Text>
            </View>
            <View style={styles.rowGap6}>
              <Text style={styles.verifikasiNoteText}>
                Bukti transfer Anda akan diverifikasi maksimal{' '}
                <Text style={styles.bold546}>1×24 jam</Text> setelah pengiriman.
              </Text>
            </View>
          </View>
        )}

        {/* ── Meta Info ── */}
        <View style={styles.metaCard}>
          <View style={styles.metaRow}>
            <View style={styles.metaIconWrap}>
              <Ionicons name="calendar-outline" size={14} color="#00AA5B" />
            </View>
            <Text style={styles.metaLabel}>Tanggal Pelaksanaan</Text>
          <Text style={styles.metaValue}>{belumMengajukan ? '—' : (tanggal ? formatTanggalIndo(tanggal) : '—')}</Text>
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
                {belumMengajukan
  ? 'Belum Mendaftar'
  : isDiterima
    ? 'Terjadwal'
    : isDitolak
      ? 'Ditolak'
      : isDibatalkan
        ? 'Dibatalkan'
        : (status || 'Menunggu')}
              </Text>
            </View>
          </View>
        </View>

        {/* ── Komentar / Catatan ── */}
        {isKomentarAda && !belumMengajukan && (
          <View style={[styles.commentCard, isDitolak && styles.commentCardDitolak]}>
            <View style={styles.rowGap6}>
              <Ionicons
                name="chatbubble-ellipses-outline"
                size={16}
                color={isDitolak ? '#e53935' : '#00AA5B'}
              />
              <Text style={[styles.commentTitle, isDitolak && { color: '#c62828' }]}>
                {isDitolak ? 'Alasan Penolakan' : 'Catatan'}
              </Text>
            </View>
            <Text style={styles.commentText}>{komentar}</Text>
          </View>
        )}

        {/* ── Berkas Dokumen ── */}
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

        {/* ── Kelola Jadwal / Reschedule ── */}
        {!belumMengajukan && !berkasTersedia && !isDibatalkan && !isDitolak && (
          <View style={styles.actionSection}>
            <Text style={styles.sectionLabel}>Kelola Jadwal</Text>

            {!melewatiBatasReschedule && selisihHari >= 0 && selisihHari <= 14 && (
              <View style={styles.rescheduleWarning}>
                <Ionicons name="hourglass-outline" size={14} color="#f57c00" />
                <Text style={styles.rescheduleWarningText}>
                  Sisa waktu reschedule:{' '}
                  <Text style={styles.rescheduleWarningBold}>{sisaHariReschedule} hari lagi</Text>
                  {' '}(maks. 14 hari setelah jadwal)
                </Text>
              </View>
            )}

            {melewatiBatasReschedule ? (
              <View style={styles.rescheduleBatasInfo}>
                <View style={styles.rowGap6}>
                  <Ionicons name="lock-closed-outline" size={15} color="#e53935" />
                  <Text style={styles.rescheduleBatasTitle}>Reschedule Hangus</Text>
                </View>
                <Text style={styles.rescheduleBatasSub}>
                  Batas pengajuan reschedule adalah <Text style={styles.boldText}>14 hari</Text> setelah tanggal jadwal.
                  Jadwal Anda telah lewat <Text style={styles.boldText}>{selisihHari} hari</Text>, sehingga hak reschedule sudah tidak berlaku.
                </Text>
              </View>
            ) : (
              <View style={{ gap: 10 }}>
                <TouchableOpacity
                  style={styles.btnReschedule}
                  onPress={handleOpenReschedule}
                  activeOpacity={0.7}
                >
                  <Ionicons name="calendar-outline" size={17} color="#fff" />
                  <Text style={styles.btnRescheduleText}>Ajukan Reschedule</Text>
                </TouchableOpacity>

                <View style={styles.rescheduleNote}>
                  <Ionicons name="alert-circle-outline" size={13} color="#78909c" />
                  <Text style={styles.rescheduleNoteText}>
                    Reschedule hanya dapat diajukan maksimal{' '}
                    <Text style={styles.rescheduleNoteBold}>14 hari</Text> setelah tanggal jadwal.
                    Jika melebihi batas tersebut, hak reschedule akan{' '}
                    <Text style={styles.rescheduleNoteBold}>hangus</Text>.
                  </Text>
                </View>
              </View>
            )}
          </View>
        )}

        {/* ── Butuh Bantuan? (samakan dengan profile) ── */}
        {!belumMengajukan && (
          <View style={styles.section}>
            <View style={styles.sectionHeader}>
              <View style={styles.sectionIconWrap}>
                <Ionicons name="headset-outline" size={14} color="#00AA5B" />
              </View>
              <View>
                <Text style={styles.sectionTitle}>Butuh Bantuan?</Text>
                <Text style={styles.sectionSub}>Hubungi kami via WhatsApp</Text>
              </View>
            </View>

            <Text style={styles.waDesc}>
              Tanya seputar jadwal, reschedule, atau hasil tes STIFIn langsung ke tim kami.
            </Text>

            <TouchableOpacity
              style={styles.waBtn}
              onPress={() => hubungiAdmin(adminWa1, 'reschedule untuk jadwal')}
              activeOpacity={0.82}
            >
              <View style={styles.waBtnIconWrap}>
                <Ionicons name="logo-whatsapp" size={20} color="#22C55E" />
              </View>
              <View style={{ flex: 1 }}>
                <Text style={styles.waBtnRole}>Promotor STIFIn</Text>
                <Text style={styles.waBtnNomor}>{toLokalFormat(adminWa1)}</Text>
              </View>
              <View style={styles.waBtnArrow}>
                <Ionicons name="arrow-forward" size={13} color="#22C55E" />
              </View>
            </TouchableOpacity>

            <TouchableOpacity
              style={[styles.waBtn, { marginTop: 10 }]}
              onPress={() => hubungiAdmin(adminWa2, 'reschedule untuk jadwal')}
              activeOpacity={0.82}
            >
              <View style={styles.waBtnIconWrap}>
                <Ionicons name="logo-whatsapp" size={20} color="#22C55E" />
              </View>
              <View style={{ flex: 1 }}>
                <Text style={styles.waBtnRole}>Admin STIFIn</Text>
                <Text style={styles.waBtnNomor}>{toLokalFormat(adminWa2)}</Text>
              </View>
              <View style={styles.waBtnArrow}>
                <Ionicons name="arrow-forward" size={13} color="#22C55E" />
              </View>
            </TouchableOpacity>
          </View>
        )}

      </ScrollView>

      {/* ══════════════════════════════════════════
          Modal Reschedule
      ══════════════════════════════════════════ */}
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

                {selisihHari >= 0 && (
                  <View style={styles.modalBatasInfo}>
                    <Ionicons name="hourglass-outline" size={13} color="#f57c00" />
                    <Text style={styles.modalBatasText}>
                      Sisa waktu:{' '}
                      <Text style={{ fontWeight: '800', color: '#e65100' }}>{sisaHariReschedule} hari</Text>
                      {' '}untuk mengajukan reschedule
                    </Text>
                  </View>
                )}

                <ScrollView showsVerticalScrollIndicator={false} nestedScrollEnabled>

                  {/* Pilihan Lokasi */}
                  <View style={styles.modalSection}>
                    <Text style={styles.modalSectionLabel}>Lokasi Tes</Text>
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

                  {/* Pilihan Wilayah Home Visit */}
                  {rescheduleTempatTes === 'Home Visit' && (
                    <View style={styles.modalSection}>
                      <Text style={styles.modalSectionLabel}>Wilayah Home Visit</Text>

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
                            + {formatRupiah(rescheduleTransport.biaya)} transport
                          </Text>
                        </TouchableOpacity>

                        <TouchableOpacity
                          style={[styles.wilayahChip, rescheduleWilayah === 'luar' && styles.chipActive]}
                          onPress={() => { setRescheduleWilayah('luar'); setRescheduleDropdownOpen(false); }}
                          activeOpacity={0.7}
                        >
                          <Text style={[styles.wilayahChipText, rescheduleWilayah === 'luar' && styles.chipTextActive]}>
                            Luar Subang
                          </Text>
                          <Text style={[styles.wilayahHarga, rescheduleWilayah === 'luar' && styles.wilayahHargaActive]}>
                            + Rp 75.000 transport
                          </Text>
                        </TouchableOpacity>
                      </View>

                      {rescheduleWilayah === 'dalam' && (
                        <View style={styles.kotaBox}>
                          <Text style={styles.kotaLabel}>Pilih Area Transport</Text>

                          <TouchableOpacity
                            style={styles.dropdownTrigger}
                            onPress={() => setRescheduleDropdownOpen(!rescheduleDropdownOpen)}
                            activeOpacity={0.8}
                          >
                            <View style={styles.dropdownTriggerLeft}>
                              <Ionicons name="location-outline" size={15} color="#00AA5B" />
                              <Text style={styles.dropdownTriggerText}>{rescheduleTransport.label}</Text>
                            </View>
                            <View style={styles.dropdownTriggerRight}>
                              <Text style={styles.dropdownTriggerHarga}>
                                + {formatRupiah(rescheduleTransport.biaya)}
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
                              {TRANSPORT_DALAM_SUBANG.map((item, index) => {
                                const isActive = rescheduleTransport.label === item.label;
                                return (
                                  <TouchableOpacity
                                    key={item.label}
                                    style={[
                                      styles.dropdownItem,
                                      isActive && styles.dropdownItemActive,
                                      index < TRANSPORT_DALAM_SUBANG.length - 1 && styles.dropdownItemBorder,
                                    ]}
                                    onPress={() => { setRescheduleTransport(item); setRescheduleDropdownOpen(false); }}
                                    activeOpacity={0.7}
                                  >
                                    <View style={styles.dropdownItemLeft}>
                                      <Ionicons
                                        name={isActive ? 'checkmark-circle' : 'radio-button-off-outline'}
                                        size={16}
                                        color={isActive ? '#00AA5B' : '#b0bec5'}
                                      />
                                      <Text style={[styles.dropdownItemText, isActive && styles.dropdownItemTextActive]}>
                                        {item.label}
                                      </Text>
                                    </View>
                                    <Text style={[styles.dropdownItemHarga, isActive && styles.dropdownItemHargaActive]}>
                                      + {formatRupiah(item.biaya)}
                                    </Text>
                                  </TouchableOpacity>
                                );
                              })}
                            </View>
                          )}
                        </View>
                      )}

                      {rescheduleWilayah === 'luar' && (
                        <View style={styles.luarSubangInfo}>
                          <Ionicons name="information-circle-outline" size={15} color="#0288d1" />
                          <Text style={styles.luarSubangInfoText}>
                            Biaya transport luar Subang sebesar{' '}
                            <Text style={{ fontWeight: '800', color: '#01579b' }}>Rp 75.000</Text>{' '}
                            berlaku untuk semua wilayah di luar Kabupaten Subang.
                          </Text>
                        </View>
                      )}
                    </View>
                  )}

                  {/* Info Biaya */}
                  <View style={styles.biayaInfoRow}>
                    <Ionicons name="pricetag-outline" size={13} color="#00AA5B" />
                    <View style={{ flex: 1 }}>
                      {rescheduleTempatTes === 'Home Visit' ? (
                        <>
                          <Text style={styles.biayaInfoText}>
                            Biaya Tes: <Text style={styles.bold546}>{formatRupiah(BIAYA_TES)}</Text>
                            {'  +  '}
                            Transport: <Text style={styles.bold546}>{formatRupiah(getRescheduleTransportBiaya())}</Text>
                          </Text>
                          <Text style={styles.biayaInfoTotal}>Total: {formatRupiah(getRescheduleBiaya())}</Text>
                        </>
                      ) : (
                        <Text style={styles.biayaInfoText}>
                          Biaya: <Text style={styles.biayaInfoNominal}>{formatRupiah(getRescheduleBiaya())}</Text>
                        </Text>
                      )}
                      <Text style={styles.biayaInfoNote}>* Belum termasuk biaya admin antar bank</Text>
                    </View>
                  </View>

                  <View style={styles.modalDivider} />

                  <Text style={[styles.modalSectionLabel, { marginBottom: 10 }]}>Pilih Slot Jadwal</Text>

                  {loadingJadwal ? (
                    <View style={styles.modalLoading}>
                      <ActivityIndicator size="large" color="#00AA5B" />
                      <Text style={styles.modalLoadingText}>Memuat jadwal tersedia...</Text>
                    </View>
                  ) : filteredRescheduleJadwal.length === 0 ? (
                    <View style={styles.modalEmpty}>
                      <View style={styles.emptyDocIcon}>
                        <Ionicons name="calendar-outline" size={32} color="#90a4ae" />
                      </View>
                      <Text style={styles.emptyDocTitle}>Tidak Ada Jadwal</Text>
                      <Text style={styles.emptyDocSub}>Tidak ada slot tersedia untuk lokasi ini saat ini.</Text>
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
                            <Text style={styles.jadwalTanggal}>{formatTanggalIndo(item.tanggal)}</Text>
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

// ─── Styles ──────────────────────────────────────────────────────────────────

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f5faf7' },

  // ── Top Bar ──
  topBar: {
    backgroundColor: '#00AA5B',
    paddingTop: Platform.OS === 'android'
      ? (StatusBar.currentHeight ? StatusBar.currentHeight + 12 : 30)
      : 16,
    paddingBottom: 18,
    paddingHorizontal: 16,
    flexDirection: 'row',
    alignItems: 'center',
    elevation: 4,
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

  // ── Loading Overlay ──
  loadingOverlay: {
    ...StyleSheet.absoluteFillObject,
    backgroundColor: 'rgba(0,0,0,0.4)',
    justifyContent: 'center', alignItems: 'center', zIndex: 999,
  },
  loadingBox: {
    backgroundColor: '#fff', borderRadius: 16,
    padding: 24, alignItems: 'center', gap: 12,
  },
  loadingText: { fontSize: 14, fontWeight: '600', color: '#546e7a' },

  // ── Status Card ──
  statusCard: {
    backgroundColor: '#fff', borderRadius: 16,
    padding: 16, flexDirection: 'row', gap: 14,
    marginBottom: 14, borderWidth: 1, borderColor: '#e8f5e9',
    borderLeftWidth: 4,
  },
  statusIconWrap:  { width: 64, height: 64, borderRadius: 16, justifyContent: 'center', alignItems: 'center' },
  statusCardRight: { flex: 1 },
  statusTitle:     { fontSize: 15, fontWeight: '800', marginBottom: 4 },
  statusSub:       { fontSize: 12, color: '#78909c', lineHeight: 17 },

  // ── Verifikasi Note ──
  verifikasiNote: {
    backgroundColor: '#fff8f0', borderRadius: 14,
    padding: 14, marginBottom: 14,
    borderWidth: 1, borderColor: '#ffe0b2',
    borderLeftWidth: 3, borderLeftColor: '#f57c00', gap: 8,
  },
  verifikasiNoteTitle: { fontSize: 13, fontWeight: '800', color: '#e65100' },
  verifikasiNoteText:  { fontSize: 12, color: '#78909c', lineHeight: 18, flex: 1 },

  // ── Meta Card ──
  metaCard: {
    backgroundColor: '#fff', borderRadius: 16,
    padding: 16, marginBottom: 14,
    borderWidth: 1, borderColor: '#e8f5e9',
  },
  metaRow: { flexDirection: 'row', alignItems: 'center', gap: 8 },
  metaIconWrap: {
    width: 26, height: 26, borderRadius: 8,
    backgroundColor: '#e8f5e9',
    justifyContent: 'center', alignItems: 'center',
  },
  metaLabel:   { flex: 1, fontSize: 13, color: '#78909c', fontWeight: '600' },
  metaValue:   { fontSize: 13, fontWeight: '700', color: '#1a1a2e' },
  metaDivider: { height: 1, backgroundColor: '#f5faf7', marginVertical: 12 },
  statusPill:     { borderRadius: 12, paddingHorizontal: 10, paddingVertical: 3 },
  statusPillText: { fontSize: 12, fontWeight: '800' },

  // ── Comment Card ──
  commentCard: {
    backgroundColor: '#e8f5e9', borderRadius: 14, padding: 14,
    borderLeftWidth: 3, borderLeftColor: '#00AA5B',
    marginBottom: 14, gap: 8,
  },
  commentCardDitolak: { backgroundColor: '#ffebee', borderLeftColor: '#e53935' },
  commentTitle: { fontSize: 13, fontWeight: '800', color: '#00AA5B' },
  commentText:  { fontSize: 13, color: '#37474f', lineHeight: 19, fontStyle: 'italic' },

  // ── Section Label ──
  sectionLabel: { fontSize: 13, fontWeight: '800', color: '#546e7a', marginBottom: 10, paddingLeft: 2 },

  // ── Document Buttons ──
  docButtons: { gap: 10, marginBottom: 14 },
  btnDownloadGreen: {
    flexDirection: 'row', alignItems: 'center', gap: 12,
    backgroundColor: '#fff', borderRadius: 14, padding: 14,
    borderWidth: 1.5, borderColor: '#a5d6a7',
  },
  btnDownloadBlue: {
    flexDirection: 'row', alignItems: 'center', gap: 12,
    backgroundColor: '#fff', borderRadius: 14, padding: 14,
    borderWidth: 1.5, borderColor: '#81d4fa',
  },
  btnDownloadIcon:     { width: 44, height: 44, borderRadius: 12, backgroundColor: '#e8f5e9', justifyContent: 'center', alignItems: 'center' },
  btnDownloadIconBlue: { width: 44, height: 44, borderRadius: 12, backgroundColor: '#e1f5fe', justifyContent: 'center', alignItems: 'center' },
  btnDownloadInfo:     { flex: 1 },
  btnDownloadTitle:    { fontSize: 14, fontWeight: '800', color: '#1a1a2e', marginBottom: 2 },
  btnDownloadSub:      { fontSize: 11, color: '#90a4ae' },

  // ── Empty Doc ──
  emptyDoc: {
    backgroundColor: '#fff', borderRadius: 16, padding: 28, alignItems: 'center',
    borderWidth: 1.5, borderColor: '#e0f2ec', borderStyle: 'dashed', marginBottom: 14,
  },
  emptyDocIcon:  { width: 72, height: 72, borderRadius: 36, backgroundColor: '#f5faf7', justifyContent: 'center', alignItems: 'center', marginBottom: 12 },
  emptyDocTitle: { fontSize: 15, fontWeight: '800', color: '#546e7a', marginBottom: 6 },
  emptyDocSub:   { fontSize: 12, color: '#90a4ae', textAlign: 'center', lineHeight: 17, marginBottom: 16 },
  btnDaftar: {
    flexDirection: 'row', alignItems: 'center', gap: 6,
    backgroundColor: '#00AA5B', paddingVertical: 10, paddingHorizontal: 18, borderRadius: 10,
  },
  btnDaftarText: { color: '#fff', fontWeight: '700', fontSize: 13 },

  // ── Action Section / Reschedule ──
  actionSection: { marginBottom: 16 },

  rescheduleWarning: {
    flexDirection: 'row', alignItems: 'center', gap: 6,
    backgroundColor: '#fff8f0', borderRadius: 10, padding: 10,
    borderWidth: 1, borderColor: '#ffe0b2', marginBottom: 10,
  },
  rescheduleWarningText: { fontSize: 12, color: '#78909c', flex: 1 },
  rescheduleWarningBold: { fontWeight: '800', color: '#e65100' },

  rescheduleBatasInfo: {
    backgroundColor: '#ffebee', borderRadius: 12, padding: 14,
    borderWidth: 1, borderColor: '#ffcdd2',
    borderLeftWidth: 3, borderLeftColor: '#e53935',
    marginBottom: 12, gap: 6,
  },
  rescheduleBatasTitle: { fontSize: 13, fontWeight: '800', color: '#c62828' },
  rescheduleBatasSub:   { fontSize: 12, color: '#78909c', lineHeight: 17 },

  btnReschedule: {
    flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 6,
    paddingVertical: 13, borderRadius: 12, backgroundColor: '#f57c00',
  },
  btnRescheduleText: { color: '#fff', fontWeight: '700', fontSize: 13 },

  rescheduleNote: {
    flexDirection: 'row', alignItems: 'flex-start', gap: 6,
    backgroundColor: '#f5f5f5', borderRadius: 10, padding: 10,
    borderWidth: 1, borderColor: '#e0e0e0',
  },
  rescheduleNoteText: { fontSize: 11, color: '#78909c', flex: 1, lineHeight: 16 },
  rescheduleNoteBold: { fontWeight: '800', color: '#546e7a' },

  // ── Section (Butuh Bantuan - WA) — disamakan dengan profile ──
  section: {
    backgroundColor: '#fff',
    borderRadius: 20,
    padding: 18,
    marginBottom: 14,
    borderWidth: 1,
    borderColor: '#E2ECE7',
    ...Platform.select({
      ios:     { shadowColor: '#000', shadowOffset: { width: 0, height: 4 }, shadowOpacity: 0.06, shadowRadius: 10 },
      android: { elevation: 2 },
    }),
  },
  sectionHeader: {
    flexDirection: 'row', alignItems: 'center', gap: 12,
    marginBottom: 12,
  },
  sectionIconWrap: {
    width: 34, height: 34, borderRadius: 10,
    backgroundColor: '#E8F5EE',
    justifyContent: 'center', alignItems: 'center',
  },
  sectionTitle: { fontSize: 14, fontWeight: '800', color: '#0F172A' },
  sectionSub:   { fontSize: 11, color: '#94A3B8', marginTop: 1 },

  waDesc: {
    fontSize: 12, color: '#64748B', lineHeight: 18,
    marginBottom: 14,
    paddingBottom: 14,
    borderBottomWidth: 1, borderColor: '#E2ECE7',
  },

  waBtn: {
    flexDirection: 'row', alignItems: 'center', gap: 12,
    backgroundColor: '#F0FDF4',
    borderRadius: 14, padding: 14,
    borderWidth: 1, borderColor: '#BBF7D0',
  },
  waBtnIconWrap: {
    width: 40, height: 40, borderRadius: 12,
    backgroundColor: '#DCFCE7',
    justifyContent: 'center', alignItems: 'center',
  },
  waBtnRole:  { fontSize: 10, fontWeight: '700', color: '#94A3B8', letterSpacing: 0.5, textTransform: 'uppercase', marginBottom: 2 },
  waBtnNomor: { fontSize: 15, fontWeight: '800', color: '#0F172A' },
  waBtnArrow: {
    width: 28, height: 28, borderRadius: 8,
    backgroundColor: '#DCFCE7',
    justifyContent: 'center', alignItems: 'center',
  },

  // ── Modal ──
  modalOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.5)', justifyContent: 'flex-end' },
  modalSheet: {
    backgroundColor: '#fff', borderTopLeftRadius: 28, borderTopRightRadius: 28,
    paddingTop: 12, paddingHorizontal: 20, paddingBottom: 32, height: '88%',
  },
  modalHandle: { width: 40, height: 4, borderRadius: 2, backgroundColor: '#e0f2ec', alignSelf: 'center', marginBottom: 16 },
  modalHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 10 },
  modalTitle:  { fontSize: 16, fontWeight: '800', color: '#1a1a2e' },
  modalSub:    { fontSize: 11, color: '#90a4ae', marginTop: 3 },
  modalClose:  { width: 32, height: 32, borderRadius: 16, backgroundColor: '#f5faf7', justifyContent: 'center', alignItems: 'center' },

  modalBatasInfo: {
    flexDirection: 'row', alignItems: 'center', gap: 6,
    backgroundColor: '#fff8f0', borderRadius: 10, padding: 10,
    borderWidth: 1, borderColor: '#ffe0b2', marginBottom: 14,
  },
  modalBatasText: { fontSize: 12, color: '#78909c', flex: 1 },

  modalSection:      { marginBottom: 14 },
  modalSectionLabel: { fontSize: 11, fontWeight: '700', color: '#546e7a', textTransform: 'uppercase', letterSpacing: 0.6, marginBottom: 10 },

  chipRow: { flexDirection: 'row', gap: 10 },
  chip: {
    flex: 1, flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 6,
    paddingVertical: 10, borderRadius: 12,
    backgroundColor: '#f5faf7', borderWidth: 1.5, borderColor: '#e0f2ec',
  },
  chipActive:     { backgroundColor: '#00AA5B', borderColor: '#00AA5B' },
  chipText:       { fontSize: 13, fontWeight: '700', color: '#546e7a' },
  chipTextActive: { color: '#fff' },

  wilayahChip: {
    flex: 1, alignItems: 'center', paddingVertical: 10, borderRadius: 12,
    backgroundColor: '#f5faf7', borderWidth: 1.5, borderColor: '#e0f2ec',
  },
  wilayahChipText:    { fontSize: 12, fontWeight: '700', color: '#546e7a', marginBottom: 2 },
  wilayahHarga:       { fontSize: 11, fontWeight: '600', color: '#90a4ae' },
  wilayahHargaActive: { color: 'rgba(255,255,255,0.85)' },

  kotaBox:   { marginTop: 12, paddingTop: 12, borderTopWidth: 1, borderTopColor: '#e8f5e9' },
  kotaLabel: { fontSize: 11, fontWeight: '700', color: '#546e7a', textTransform: 'uppercase', letterSpacing: 0.6, marginBottom: 8 },

  dropdownTrigger: {
    flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between',
    backgroundColor: '#f5faf7', borderRadius: 12,
    borderWidth: 1.5, borderColor: '#a5d6a7',
    paddingHorizontal: 14, paddingVertical: 12,
  },
  dropdownTriggerLeft:  { flexDirection: 'row', alignItems: 'center', gap: 8 },
  dropdownTriggerText:  { fontSize: 14, fontWeight: '700', color: '#1a1a2e' },
  dropdownTriggerRight: { flexDirection: 'row', alignItems: 'center', gap: 8 },
  dropdownTriggerHarga: { fontSize: 13, fontWeight: '800', color: '#00AA5B' },

  dropdownList: {
    marginTop: 6, backgroundColor: '#fff',
    borderRadius: 12, borderWidth: 1.5, borderColor: '#e0f2ec', overflow: 'hidden',
  },
  dropdownItem: {
    flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between',
    paddingHorizontal: 14, paddingVertical: 13, backgroundColor: '#fff',
  },
  dropdownItemActive:       { backgroundColor: '#f0faf5' },
  dropdownItemBorder:       { borderBottomWidth: 1, borderBottomColor: '#f0f4f0' },
  dropdownItemLeft:         { flexDirection: 'row', alignItems: 'center', gap: 10 },
  dropdownItemText:         { fontSize: 13, fontWeight: '600', color: '#37474f' },
  dropdownItemTextActive:   { color: '#00AA5B', fontWeight: '800' },
  dropdownItemHarga:        { fontSize: 12, fontWeight: '700', color: '#90a4ae' },
  dropdownItemHargaActive:  { color: '#00AA5B' },

  luarSubangInfo: {
    flexDirection: 'row', alignItems: 'flex-start', gap: 8,
    marginTop: 12, padding: 12,
    backgroundColor: '#e1f5fe', borderRadius: 10,
    borderWidth: 1, borderColor: '#b3e5fc',
  },
  luarSubangInfoText: { flex: 1, fontSize: 12, color: '#01579b', lineHeight: 17 },

  biayaInfoRow: {
    flexDirection: 'row', alignItems: 'flex-start', gap: 6,
    paddingVertical: 10, paddingHorizontal: 12,
    backgroundColor: '#f5faf7', borderRadius: 10,
    borderWidth: 1, borderColor: '#e8f5e9', marginBottom: 14,
  },
  biayaInfoText:    { fontSize: 13, color: '#546e7a', fontWeight: '600' },
  biayaInfoNominal: { color: '#00AA5B', fontWeight: '800' },
  biayaInfoTotal:   { fontSize: 14, fontWeight: '800', color: '#00AA5B', marginTop: 4 },
  biayaInfoNote:    { fontSize: 10, color: '#90a4ae', fontStyle: 'italic', marginTop: 2 },

  modalDivider: { height: 1, backgroundColor: '#e8f5e9', marginBottom: 14 },

  modalLoading:     { paddingVertical: 40, alignItems: 'center', gap: 12 },
  modalLoadingText: { color: '#90a4ae', fontSize: 13 },
  modalEmpty:       { alignItems: 'center', paddingVertical: 32, gap: 8 },

  jadwalItem: {
    flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center',
    padding: 14, backgroundColor: '#f5faf7', borderRadius: 14, marginBottom: 10,
    borderWidth: 1, borderColor: '#e8f5e9',
  },
  jadwalItemLeft: { flexDirection: 'row', alignItems: 'center', gap: 12 },
  jadwalRight:    { flexDirection: 'row', alignItems: 'center', gap: 6 },
  jadwalIconWrap: { width: 38, height: 38, borderRadius: 10, backgroundColor: '#e8f5e9', justifyContent: 'center', alignItems: 'center' },
  jadwalTanggal:  { fontSize: 14, fontWeight: '700', color: '#1a1a2e' },
  jadwalDetail:   { fontSize: 11, color: '#90a4ae', marginTop: 2 },
  kuotaBadge:     { backgroundColor: '#e8f5e9', paddingVertical: 4, paddingHorizontal: 10, borderRadius: 10 },
  kuotaText:      { fontSize: 11, color: '#00AA5B', fontWeight: '800' },

  modalBtnBatal: {
    marginTop: 12, paddingVertical: 14, borderRadius: 14, alignItems: 'center',
    backgroundColor: '#f5faf7', borderWidth: 1.5, borderColor: '#e0f2ec',
  },
  modalBtnBatalText: { fontSize: 14, fontWeight: '700', color: '#546e7a' },

  // ── Utility ──
  rowGap6:  { flexDirection: 'row', alignItems: 'center', gap: 6 },
  bold546:  { fontWeight: '700', color: '#546e7a' },
  boldText: { fontWeight: '800' },
});