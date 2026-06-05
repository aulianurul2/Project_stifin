import React, { useState, useCallback } from 'react';
import {
  View,
  Text,
  StyleSheet,
  SafeAreaView,
  ScrollView,
  TouchableOpacity,
  ActivityIndicator,
  Modal,
  Dimensions,
  TouchableWithoutFeedback,
  Image
} from 'react-native';

import { Ionicons } from '@expo/vector-icons';
import { useRouter, useFocusEffect } from 'expo-router';
import AsyncStorage from '@react-native-async-storage/async-storage';
import axiosInstance from '@/src/api/axiosConfig';

const { width } = Dimensions.get('window');

interface RiwayatItem {
  id_jadwal: number;
  tanggal: string; // Format yang diharapkan: 'YYYY-MM-DD'
  jam?: string;    // Format yang diharapkan: 'HH:MM' atau 'HH:MM:SS'
  waktu?: string;
  jam_tes?: string;
  jam_pelaksanaan?: string;
  status_tes?: string;
  status?: string;
  file_hasil: string | null;
  file_detail: string | null;
  komentar?: string | null;
}

export default function DashboardIndex() {
  const router = useRouter();

  const [userName, setUserName] = useState("User");
  const [navLoading, setNavLoading] = useState(false);
  
  const [latestRegistration, setLatestRegistration] = useState<RiwayatItem | null>(null);
  const [hasNewNotification, setHasNewNotification] = useState(false);
  const [dropdownVisible, setDropdownVisible] = useState(false);

  // Menyimpan waktu rendering saat ini untuk kalkulasi relatif yang akurat saat dropdown dibuka
  const [now, setNow] = useState<Date>(new Date());

  const getFormattedDate = () => {
    const options: Intl.DateTimeFormatOptions = {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    };
    return new Date().toLocaleDateString('id-ID', options);
  };

  const getInitialName = (name: string) => {
    if (!name) return "U";
    return name.trim().charAt(0).toUpperCase();
  };

  useFocusEffect(
    useCallback(() => {
      const fetchDashboardData = async () => {
        try {
          const savedName = await AsyncStorage.getItem('user_name');
          if (savedName) {
            setUserName(savedName);
          }

          const response = await axiosInstance.get('/riwayat-pendaftaran');
          const riwayat: RiwayatItem[] = response.data;
          
          if (riwayat && riwayat.length > 0) {
            const latest = riwayat[0];
            setLatestRegistration(latest);
            
            const statusValid = dapatkanStatusValid(latest);
            if (statusValid === "Selesai" || statusValid === "Diproses" || statusValid === "Ditolak") {
              setHasNewNotification(true);
            } else {
              setHasNewNotification(false);
            }
          } else {
            setLatestRegistration(null);
            setHasNewNotification(false);
          }
        } catch (error) {
          console.log("Gagal memuat data dashboard:", error);
        }
      };

      fetchDashboardData();
    }, [])
  );

  const dapatkanJamValid = (item: RiwayatItem): string => {
    const nilaiJam = item.jam || item.waktu || item.jam_tes || item.jam_pelaksanaan;
    if (!nilaiJam) return "";
    if (nilaiJam.includes(':') && nilaiJam.split(':').length === 3) {
      const splitJam = nilaiJam.split(':');
      return `${splitJam[0]}:${splitJam[1]}`;
    }
    return nilaiJam;
  };

  const dapatkanStatusValid = (item: RiwayatItem): string => {
    const statusUtama = item.status ? item.status.trim() : "";
    const statusHasil = item.status_tes ? item.status_tes.trim() : "";

    if (statusUtama === "Ditolak") return "Ditolak";
    if (statusUtama === "Menunggu" || statusUtama === "Konfirmasi") return "Menunggu";

    if (statusHasil) {
      if (statusHasil.toLowerCase() === "proses") return "Diproses";
      if (statusHasil.toLowerCase() === "selesai") return "Selesai";
      return statusHasil;
    }
    return statusUtama || "Menunggu";
  };

  // Fungsi utilitas untuk menghitung waktu relatif secara realtime
  const getRelativeTime = (tanggalStr: string, jamStr?: string): string => {
    if (!tanggalStr) return "Baru saja";
    
    try {
      // Gabungkan komponen tanggal dan jam agar presisi
      const waktuEfektif = jamStr ? jamStr : "00:00";
      const formatIso = `${tanggalStr}T${waktuEfektif}`;
      const timestampNotif = new Date(formatIso);
      
      if (isNaN(timestampNotif.getTime())) return "Baru saja";

      const selisihMiliDetik = now.getTime() - timestampNotif.getTime();
      const selisihDetik = Math.floor(selisihMiliDetik / 1000);
      const selisihMenit = Math.floor(selisihDetik / 60);
      const selisihJam = Math.floor(selisihMenit / 60);
      const selisihHari = Math.floor(selisihJam / 24);

      // Tangani skenario jika jam server sedikit mendahului device (selisih negatif)
      if (selisihDetik < 45) {
        return "Baru saja";
      } else if (selisihMenit < 60) {
        return `${selisihMenit} menit yang lalu`;
      } else if (selisihJam < 24) {
        return `${selisihJam} jam yang lalu`;
      } else if (selisihHari < 7) {
        return `${selisihHari} hari yang lalu`;
      } else {
        // Jika sudah lebih dari seminggu, tampilkan format tanggal default terformat singkat
        return timestampNotif.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
      }
    } catch (e) {
      return "Baru saja";
    }
  };

  const handleNotificationPress = () => {
    setNow(new Date()); // Segarkan komponen waktu sesaat sebelum dropdown ditampilkan
    setDropdownVisible(!dropdownVisible);
    setHasNewNotification(false);
  };

  const handleHasilTesNavigation = async () => {
    setDropdownVisible(false);
    try {
      setNavLoading(true);
      const response = await axiosInstance.get('/riwayat-pendaftaran');
      const riwayat: RiwayatItem[] = response.data;

      if (riwayat && riwayat.length > 0) {
        const latestItem = riwayat[0];
        const jamValid = dapatkanJamValid(latestItem);
        const statusValid = dapatkanStatusValid(latestItem);

        router.push({
          pathname: '/hasil-tes',
          params: {
            id_jadwal: latestItem.id_jadwal ? latestItem.id_jadwal.toString() : 'undefined',
            tanggal: latestItem.tanggal || '',
            jam: jamValid,
            file_hasil: latestItem.file_hasil ? latestItem.file_hasil : 'null',
            file_detail: latestItem.file_detail ? latestItem.file_detail : 'null',
            status: statusValid,
            komentar: latestItem.komentar ? latestItem.komentar : 'null'
          }
        });
      } else {
        router.push('/hasil-tes');
      }
    } catch (error) {
      console.log("Gagal memproses navigasi hasil tes:", error);
    } finally {
      setNavLoading(false);
    }
  };

  const renderNotificationContent = () => {
    if (!latestRegistration) {
      return {
        text: "Belum ada riwayat pendaftaran atau aktivitas tes STIFIn baru pada akun Anda.",
        icon: "information-circle-outline",
        color: "#78909c"
      };
    }

    const statusValid = dapatkanStatusValid(latestRegistration);
    switch (statusValid) {
      case "Ditolak":
        return {
          text: `Pendaftaran Anda tanggal ${latestRegistration.tanggal} ditolak. Alasan: ${latestRegistration.komentar || 'Data tidak sesuai.'}`,
          icon: "close-circle-outline",
          color: "#ef4444"
        };
      case "Menunggu":
        return {
          text: `Pendaftaran tes tanggal ${latestRegistration.tanggal} sedang menunggu verifikasi dokumen oleh admin.`,
          icon: "time-outline",
          color: "#f57c00"
        };
      case "Diproses":
        return {
          text: `Pelaksanaan tes tanggal ${latestRegistration.tanggal} sedang dalam tahap analisis berkas oleh tim ahli.`,
          icon: "sync-outline",
          color: "#0288d1"
        };
      case "Selesai":
        return {
          text: `Selamat! Sertifikat dan hasil analisis kecerdasan STIFIn Anda untuk pendaftaran tanggal ${latestRegistration.tanggal} telah diterbitkan.`,
          icon: "checkmark-circle-outline",
          color: "#00AA5B",
          actionable: true
        };
      default:
        return {
          text: `Pendaftaran Anda pada tanggal ${latestRegistration.tanggal} saat ini berstatus: ${statusValid}.`,
          icon: "notifications-outline",
          color: "#00AA5B"
        };
    }
  };

  const notifDetails = renderNotificationContent();

  const menuItems = [
    { title: "Daftar Tes", icon: "create-outline", color: "#00AA5B", bgColor: "#e8f5e9", onPress: () => router.push('/pendaftaran') },
    { title: "Riwayat Tes", icon: "calendar-outline", color: "#0288d1", bgColor: "#e1f5fe", onPress: () => router.push('/riwayat') },
    { title: "Hasil Tes", icon: "stats-chart-outline", color: "#f57c00", bgColor: "#fff3e0", isCustom: true },
    { title: "Panduan", icon: "book-outline", color: "#7b1fa2", bgColor: "#f3e5f5", onPress: () => router.push('/panduan') },
  ];

  return (
    <SafeAreaView style={styles.container}>

      {/* Header */}
      <View style={styles.header}>
        <View style={styles.headerLeft}>
          <Text style={styles.brandText}>STIFIn Mobile</Text>
          <Text style={styles.dateText}>{getFormattedDate()}</Text>
        </View>

        <TouchableOpacity style={styles.notifBtn} onPress={handleNotificationPress} activeOpacity={0.7}>
          <Ionicons name="notifications-outline" size={22} color="#00AA5B" />
          {hasNewNotification && <View style={styles.notifBadge} />}
        </TouchableOpacity>
      </View>

      <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
        {/* Welcome Card */}
        <View style={styles.welcomeCard}>
          <View style={styles.welcomeCardBg1} />
          <View style={styles.welcomeCardBg2} />
          <View style={styles.welcomeInfo}>
            <View style={styles.welcomeLabelContainer}>
              <Ionicons name="hand-left-outline" size={14} color="rgba(255,255,255,0.8)" />
              <Text style={styles.welcomeLabel}>Selamat Datang</Text>
            </View>
            <Text style={styles.userName}>{userName}</Text>
            <View style={styles.roleBadge}>
              <View style={styles.roleDot} />
              <Text style={styles.roleText}>Aktif</Text>
            </View>
          </View>

          <View style={styles.avatarIconContainer}>
            <Text style={styles.avatarInitialText}>{getInitialName(userName)}</Text>
          </View>
        </View>

        {/* Quick Stats Row */}
        <View style={styles.statsRow}>
          <View style={styles.statBox}>
            <Ionicons name="checkmark-circle" size={20} color="#00AA5B" />
            <Text style={styles.statValue}>Aktif</Text>
            <Text style={styles.statLabel}>Status</Text>
          </View>
          <View style={styles.statDivider} />
          <View style={styles.statBox}>
            <Ionicons name="shield-checkmark" size={20} color="#0288d1" />
            <Text style={styles.statValue}>Aman</Text>
            <Text style={styles.statLabel}>Data</Text>
          </View>
          <View style={styles.statDivider} />
          <View style={styles.statBox}>
            {/* Perbaikan path file gambar lokal logo_light.png */}
            <Image 
              source={require('../../assets/images/logo_light.png')} 
              style={styles.statIconImg}
              resizeMode="contain"
            />
            <Text style={styles.statValue}>STIFIn</Text>
            <Text style={styles.statLabel}>Sistem</Text>
          </View>
        </View>

        {/* Section Title */}
        <Text style={styles.sectionTitle}>Layanan Utama</Text>

        {/* Menu Grid */}
        <View style={styles.grid}>
          {menuItems.map((item, index) => {
            if (item.isCustom) {
              return (
                <TouchableOpacity
                  key={index}
                  style={styles.menuBox}
                  onPress={handleHasilTesNavigation}
                  disabled={navLoading}
                  activeOpacity={0.7}
                >
                  {navLoading ? (
                    <ActivityIndicator size="small" color={item.color} style={{ padding: 18, marginBottom: 8 }} />
                  ) : (
                    <View style={[styles.iconCircle, { backgroundColor: item.bgColor }]}>
                      <Ionicons name={item.icon as any} size={26} color={item.color} />
                    </View>
                  )}
                  <Text style={styles.menuTitle}>{item.title}</Text>
                </TouchableOpacity>
              );
            }
            return (
              <MenuBox
                key={index}
                title={item.title}
                icon={item.icon}
                color={item.color}
                bgColor={item.bgColor}
                onPress={item.onPress}
              />
            );
          })}
        </View>

        {/* Promo Banner */}
        <TouchableOpacity style={styles.promoBanner} onPress={() => router.push('/pendaftaran')} activeOpacity={0.9}>
          <View style={styles.promoLeft}>
            <View style={styles.promoTagContainer}>
              <Ionicons name="sparkles-outline" size={13} color="rgba(255,255,255,0.9)" />
              <Text style={styles.promoTag}>Layanan Unggulan</Text>
            </View>
            <Text style={styles.promoTitle}>Daftar Tes STIFIn Sekarang</Text>
            <Text style={styles.promoSub}>Temukan Jati Diri Anda Sekarang</Text>
          </View>
          <View style={styles.promoArrow}>
            <Ionicons name="arrow-forward" size={18} color="#fff" />
          </View>
        </TouchableOpacity>
      </ScrollView>

      {/* --- PRO DROPDOWN NOTIFIKASI --- */}
      <Modal
        visible={dropdownVisible}
        transparent={true}
        animationType="fade"
        onRequestClose={() => setDropdownVisible(false)}
      >
        <TouchableWithoutFeedback onPress={() => setDropdownVisible(false)}>
          <View style={styles.dropdownOverlay}>
            <TouchableWithoutFeedback>
              <View style={styles.dropdownContainer}>
                
                {/* Header Dropdown */}
                <View style={styles.dropdownHeader}>
                  <Text style={styles.dropdownHeaderText}>Notifikasi Terbaru</Text>
                  <View style={styles.activeIndicatorContainer}>
                    <View style={styles.activeDot} />
                    <Text style={styles.activeText}>Realtime</Text>
                  </View>
                </View>

                {/* List Notifikasi */}
                <ScrollView style={styles.dropdownList} bounces={false}>
                  
                  {/* Item Utama (Dinamis dari Database dengan Waktu Realtime) */}
                  {latestRegistration && (
                    <View style={styles.notifItem}>
                      <View style={styles.notifMainRow}>
                        <View style={[styles.statusIndicatorBar, { backgroundColor: notifDetails.color }]} />
                        <View style={[styles.proIconCircle, { backgroundColor: notifDetails.color + '10' }]}>
                          <Ionicons name={notifDetails.icon as any} size={15} color={notifDetails.color} />
                        </View>
                        <View style={styles.notifTextWrapper}>
                          <Text style={styles.proMainText}>{notifDetails.text}</Text>
                          {/* Penanda waktu realtime yang dikalkulasi secara dinamis */}
                          <Text style={styles.proTimeText}>
                            {getRelativeTime(latestRegistration.tanggal, dapatkanJamValid(latestRegistration))}
                          </Text>
                        </View>
                      </View>
                      
                      {notifDetails.actionable && (
                        <TouchableOpacity 
                          style={styles.proActionBtn} 
                          onPress={handleHasilTesNavigation}
                          activeOpacity={0.7}
                        >
                          <Text style={styles.proActionText}>Lihat Hasil Lengkap</Text>
                          <Ionicons name="chevron-forward" size={12} color="#00AA5B" />
                        </TouchableOpacity>
                      )}
                    </View>
                  )}

                  {!latestRegistration && (
                    <View style={styles.notifItem}>
                      <View style={styles.notifMainRow}>
                        <View style={[styles.statusIndicatorBar, { backgroundColor: notifDetails.color }]} />
                        <View style={[styles.proIconCircle, { backgroundColor: notifDetails.color + '10' }]}>
                          <Ionicons name={notifDetails.icon as any} size={15} color={notifDetails.color} />
                        </View>
                        <View style={styles.notifTextWrapper}>
                          <Text style={styles.proMainText}>{notifDetails.text}</Text>
                        </View>
                      </View>
                    </View>
                  )}

                </ScrollView>

                {/* Footer Dropdown */}
                <TouchableOpacity 
                  style={styles.dropdownFooter} 
                  onPress={() => {
                    setDropdownVisible(false);
                    router.push('/riwayat');
                  }}
                  activeOpacity={0.8}
                >
                  <Text style={styles.seeAllText}>Lihat Riwayat Aktivitas</Text>
                  <Ionicons name="arrow-forward-outline" size={12} color="#546e7a" />
                </TouchableOpacity>

              </View>
            </TouchableWithoutFeedback>
          </View>
        </TouchableWithoutFeedback>
      </Modal>

    </SafeAreaView>
  );
}

const MenuBox = ({ title, icon, color, bgColor, onPress }: any) => (
  <TouchableOpacity style={styles.menuBox} onPress={onPress} activeOpacity={0.7}>
    <View style={[styles.iconCircle, { backgroundColor: bgColor }]}>
      <Ionicons name={icon} size={26} color={color} />
    </View>
    <Text style={styles.menuTitle}>{title}</Text>
  </TouchableOpacity>
);

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f5faf7' },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingHorizontal: 20,
    paddingVertical: 14,
    backgroundColor: '#fff',
    alignItems: 'center',
    borderBottomWidth: 1,
    borderBottomColor: '#e8f5e9',
    zIndex: 10,
  },
  headerLeft: { flex: 1 },
  brandText: { fontSize: 20, fontWeight: '900', color: '#1a1a2e', letterSpacing: 0.5 },
  dateText: { fontSize: 11, color: '#90a4ae', marginTop: 2, textTransform: 'capitalize' },
  notifBtn: { width: 40, height: 40, backgroundColor: '#e8f5e9', borderRadius: 12, justifyContent: 'center', alignItems: 'center', marginLeft: 10 },
  notifBadge: { position: 'absolute', top: 8, right: 8, width: 8, height: 8, borderRadius: 4, backgroundColor: '#ef4444', borderWidth: 2, borderColor: '#fff' },
  scrollContent: { padding: 16, paddingBottom: 32 },
  welcomeCard: { backgroundColor: '#00AA5B', padding: 22, borderRadius: 22, flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 14, overflow: 'hidden' },
  welcomeCardBg1: { position: 'absolute', width: 120, height: 120, borderRadius: 60, backgroundColor: 'rgba(255,255,255,0.08)', top: -30, right: 60 },
  welcomeCardBg2: { position: 'absolute', width: 80, height: 80, borderRadius: 40, backgroundColor: 'rgba(255,255,255,0.06)', bottom: -20, left: 20 },
  welcomeInfo: { flex: 1 },
  welcomeLabelContainer: { flexDirection: 'row', alignItems: 'center', gap: 4, marginBottom: 4 },
  welcomeLabel: { color: 'rgba(255,255,255,0.8)', fontSize: 13, fontWeight: '600' },
  userName: { color: '#fff', fontSize: 22, fontWeight: '900', marginBottom: 8 },
  roleBadge: { flexDirection: 'row', alignItems: 'center', backgroundColor: 'rgba(255,255,255,0.2)', paddingHorizontal: 10, paddingVertical: 4, borderRadius: 20, alignSelf: 'flex-start', gap: 5 },
  roleDot: { width: 6, height: 6, borderRadius: 3, backgroundColor: '#fff' },
  roleText: { color: '#fff', fontSize: 11, fontWeight: '700' },
  avatarIconContainer: { width: 54, height: 54, backgroundColor: '#fff', borderRadius: 18, justifyContent: 'center', alignItems: 'center' },
  avatarInitialText: { fontSize: 22, fontWeight: '900', color: '#00AA5B' },
  statsRow: { backgroundColor: '#fff', borderRadius: 16, padding: 16, flexDirection: 'row', justifyContent: 'space-around', alignItems: 'center', marginBottom: 20, borderWidth: 1, borderColor: '#e8f5e9' },
  statBox: { alignItems: 'center', gap: 4 },
  statValue: { fontSize: 13, fontWeight: '800', color: '#1a1a2e' },
  statLabel: { fontSize: 10, color: '#90a4ae', fontWeight: '600' },
  statDivider: { width: 1, height: 36, backgroundColor: '#e8f5e9' },
  statIconImg: { width: 40, height: 22 }, // Menjaga gambar tetap proporsional sejajar Ionicons
  sectionTitle: { fontSize: 16, fontWeight: '800', color: '#1a1a2e', marginBottom: 14, marginLeft: 2 },
  grid: { flexDirection: 'row', flexWrap: 'wrap', justifyContent: 'space-between', marginBottom: 16 },
  menuBox: { width: '47%', backgroundColor: '#fff', padding: 18, borderRadius: 20, alignItems: 'center', marginBottom: 14, borderWidth: 1, borderColor: '#e8f5e9', justifyContent: 'center' },
  iconCircle: { padding: 14, borderRadius: 16, marginBottom: 10 },
  menuTitle: { fontSize: 13, fontWeight: '800', color: '#1a1a2e', textAlign: 'center' },
  promoBanner: { backgroundColor: '#00AA5B', borderRadius: 18, padding: 18, flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' },
  promoLeft: { flex: 1 },
  promoTagContainer: { flexDirection: 'row', alignItems: 'center', gap: 4, marginBottom: 4 },
  promoTag: { fontSize: 11, color: 'rgba(255,255,255,0.9)', fontWeight: '700', textTransform: 'uppercase' },
  promoTitle: { fontSize: 15, fontWeight: '900', color: '#fff', marginBottom: 3 },
  promoSub: { fontSize: 11, color: 'rgba(255,255,255,0.75)' },
  promoArrow: { width: 36, height: 36, borderRadius: 18, backgroundColor: 'rgba(255,255,255,0.25)', justifyContent: 'center', alignItems: 'center' },

  /* --- DESIGN DROPDOWN PROFESIONAL & CLEAN --- */
  dropdownOverlay: {
    flex: 1,
    backgroundColor: 'rgba(26, 26, 46, 0.08)', 
  },
  dropdownContainer: {
    position: 'absolute',
    top: 68, 
    right: 16,
    width: width * 0.88,
    maxHeight: 380,
    backgroundColor: '#ffffff',
    borderRadius: 16,
    borderWidth: 1,
    borderColor: '#e2ebd5',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 8 },
    shadowOpacity: 0.08,
    shadowRadius: 16,
    elevation: 6,
    overflow: 'hidden',
  },
  dropdownHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 16,
    paddingVertical: 14,
    backgroundColor: '#ffffff',
    borderBottomWidth: 1,
    borderBottomColor: '#f0f4f1',
  },
  dropdownHeaderText: {
    fontSize: 14,
    fontWeight: '800',
    color: '#1a1a2e',
    letterSpacing: 0.2,
  },
  activeIndicatorContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#e8f5e9',
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: 8,
    gap: 5,
  },
  activeDot: {
    width: 6,
    height: 6,
    borderRadius: 3,
    backgroundColor: '#00AA5B',
  },
  activeText: {
    fontSize: 10,
    fontWeight: '700',
    color: '#00AA5B',
  },
  dropdownList: {
    backgroundColor: '#ffffff',
  },
  notifItem: {
    backgroundColor: '#fafdfa', 
    padding: 16,
    borderBottomWidth: 1,
    borderBottomColor: '#f1f6f2',
  },
  readItem: {
    backgroundColor: '#ffffff', 
  },
  notifMainRow: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    gap: 12,
  },
  statusIndicatorBar: {
    position: 'absolute',
    left: -16,
    top: 0,
    bottom: 0,
    width: 3.5,
    borderTopRightRadius: 4,
    borderBottomRightRadius: 4,
  },
  proIconCircle: {
    width: 30,
    height: 30,
    borderRadius: 10,
    justifyContent: 'center',
    alignItems: 'center',
    marginTop: 1,
  },
  notifTextWrapper: {
    flex: 1,
    gap: 4,
  },
  proMainText: {
    fontSize: 13,
    color: '#2c3e50',
    lineHeight: 18,
    fontWeight: '500',
  },
  proTimeText: {
    fontSize: 11,
    color: '#95a5a6',
    fontWeight: '500',
  },
  proActionBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    alignSelf: 'flex-end',
    marginTop: 10,
    backgroundColor: '#fff',
    borderWidth: 1,
    borderColor: '#00AA5B',
    paddingHorizontal: 12,
    paddingVertical: 5,
    borderRadius: 8,
    gap: 4,
  },
  proActionText: {
    fontSize: 11,
    color: '#00AA5B',
    fontWeight: '700',
  },
  dropdownFooter: {
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#f8faf9',
    paddingVertical: 12,
    borderTopWidth: 1,
    borderTopColor: '#f0f4f1',
    gap: 6,
  },
  seeAllText: {
    fontSize: 12,
    fontWeight: '700',
    color: '#546e7a',
  },
});