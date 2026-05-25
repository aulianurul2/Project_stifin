// C:\Users\calvi\Project_stifin\frontend\app\(tabs)\home.tsx

import React, { useState, useCallback } from 'react';
import {
  View,
  Text,
  StyleSheet,
  SafeAreaView,
  ScrollView,
  TouchableOpacity,
  ActivityIndicator,
  Alert
} from 'react-native';

import { Ionicons } from '@expo/vector-icons';
import { useRouter, useFocusEffect } from 'expo-router';
import AsyncStorage from '@react-native-async-storage/async-storage';
import axiosInstance from '@/src/api/axiosConfig';

interface RiwayatItem {
  id_jadwal: number;
  tanggal: string;
  jam?: string;
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
      const getSavedName = async () => {
        const savedName = await AsyncStorage.getItem('user_name');
        if (savedName) {
          setUserName(savedName);
        }
      };
      getSavedName();
    }, [])
  );

  const dapatkanJamValid = (item: RiwayatItem): string => {
    const nilaiJam =
      item.jam ||
      item.waktu ||
      item.jam_tes ||
      item.jam_pelaksanaan;

    if (!nilaiJam) return "";

    if (
      nilaiJam.includes(':') &&
      nilaiJam.split(':').length === 3
    ) {
      const splitJam = nilaiJam.split(':');
      return `${splitJam[0]}:${splitJam[1]}`;
    }

    return nilaiJam;
  };

  const dapatkanStatusValid = (item: RiwayatItem): string => {
    const statusUtama = item.status
      ? item.status.trim()
      : "";

    const statusHasil = item.status_tes
      ? item.status_tes.trim()
      : "";

    if (statusUtama === "Ditolak") {
      return "Ditolak";
    }

    if (
      statusUtama === "Menunggu" ||
      statusUtama === "Konfirmasi"
    ) {
      return "Menunggu";
    }

    if (statusHasil) {
      if (statusHasil.toLowerCase() === "proses") {
        return "Diproses";
      }

      if (statusHasil.toLowerCase() === "selesai") {
        return "Selesai";
      }

      return statusHasil;
    }

    return statusUtama || "Menunggu";
  };

  const handleHasilTesNavigation = async () => {
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
            id_jadwal: latestItem.id_jadwal
              ? latestItem.id_jadwal.toString()
              : 'undefined',
            tanggal: latestItem.tanggal || '',
            jam: jamValid,
            file_hasil: latestItem.file_hasil
              ? latestItem.file_hasil
              : 'null',
            file_detail: latestItem.file_detail
              ? latestItem.file_detail
              : 'null',
            status: statusValid,
            komentar: latestItem.komentar
              ? latestItem.komentar
              : 'null'
          }
        });

      } else {
        router.push('/hasil-tes');
      }

    } catch (error) {
      console.log("Gagal memproses navigasi hasil tes:", error);
      Alert.alert(
        "Error",
        "Gagal mengambil data pendaftaran terakhir Anda."
      );
    } finally {
      setNavLoading(false);
    }
  };

  const menuItems = [
    { title: "Daftar Tes", icon: "create-outline", color: "#00AA5B", bgColor: "#e8f5e9", onPress: () => router.push('/pendaftaran') },
    { title: "Riwayat Tes", icon: "calendar-outline", color: "#0288d1", bgColor: "#e1f5fe", onPress: () => router.push('/riwayat') },
    { title: "Hasil Tes", icon: "stats-chart-outline", color: "#f57c00", bgColor: "#fff3e0", isCustom: true },
    { title: "Panduan", icon: "book-outline", color: "#7b1fa2", bgColor: "#f3e5f5", onPress: undefined },
  ];

  return (
    <SafeAreaView style={styles.container}>

      {/* Header */}
      <View style={styles.header}>
        <View>
          <Text style={styles.brandText}>STIFIn Mobile</Text>
          <Text style={styles.dateText}>{getFormattedDate()}</Text>
        </View>

        <TouchableOpacity style={styles.notifBtn}>
          <Ionicons name="notifications-outline" size={22} color="#00AA5B" />
          <View style={styles.notifBadge} />
        </TouchableOpacity>
      </View>

      <ScrollView
        contentContainerStyle={styles.scrollContent}
        showsVerticalScrollIndicator={false}
      >

        {/* Welcome Card - Green gradient style */}
        <View style={styles.welcomeCard}>
          <View style={styles.welcomeCardBg1} />
          <View style={styles.welcomeCardBg2} />
          <View style={styles.welcomeInfo}>
            <Text style={styles.welcomeLabel}>Selamat Datang 👋</Text>
            <Text style={styles.userName}>{userName}</Text>
            <View style={styles.roleBadge}>
              <View style={styles.roleDot} />
              <Text style={styles.roleText}>Klien Aktif</Text>
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
            <Ionicons name="flash" size={20} color="#f57c00" />
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
                    <ActivityIndicator
                      size="small"
                      color={item.color}
                      style={{ padding: 18, marginBottom: 8 }}
                    />
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
            <Text style={styles.promoTag}>✨ Spesial</Text>
            <Text style={styles.promoTitle}>Daftar Tes STIFIn Sekarang</Text>
            <Text style={styles.promoSub}>Temukan mesin kecerdasan genetik Anda</Text>
          </View>
          <View style={styles.promoArrow}>
            <Ionicons name="arrow-forward" size={18} color="#fff" />
          </View>
        </TouchableOpacity>

      </ScrollView>
    </SafeAreaView>
  );
}

const MenuBox = ({ title, icon, color, bgColor, onPress }: any) => (
  <TouchableOpacity
    style={styles.menuBox}
    onPress={onPress}
    activeOpacity={0.7}
  >
    <View style={[styles.iconCircle, { backgroundColor: bgColor }]}>
      <Ionicons name={icon} size={26} color={color} />
    </View>
    <Text style={styles.menuTitle}>{title}</Text>
  </TouchableOpacity>
);

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f5faf7',
  },

  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingHorizontal: 20,
    paddingVertical: 14,
    backgroundColor: '#fff',
    alignItems: 'center',
    borderBottomWidth: 1,
    borderBottomColor: '#e8f5e9',
    shadowColor: '#00AA5B',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 6,
    elevation: 3,
  },

  brandText: {
    fontSize: 20,
    fontWeight: '900',
    color: '#1a1a2e',
    letterSpacing: 0.5,
  },

  dateText: {
    fontSize: 11,
    color: '#90a4ae',
    marginTop: 2,
    textTransform: 'capitalize',
  },

  notifBtn: {
    width: 40,
    height: 40,
    backgroundColor: '#e8f5e9',
    borderRadius: 12,
    justifyContent: 'center',
    alignItems: 'center',
  },

  notifBadge: {
    position: 'absolute',
    top: 8,
    right: 8,
    width: 8,
    height: 8,
    borderRadius: 4,
    backgroundColor: '#ef4444',
    borderWidth: 2,
    borderColor: '#fff',
  },

  scrollContent: {
    padding: 16,
    paddingBottom: 32,
  },

  welcomeCard: {
    backgroundColor: '#00AA5B',
    padding: 22,
    borderRadius: 22,
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 14,
    overflow: 'hidden',
    shadowColor: '#00AA5B',
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.3,
    shadowRadius: 12,
    elevation: 8,
  },

  welcomeCardBg1: {
    position: 'absolute',
    width: 120,
    height: 120,
    borderRadius: 60,
    backgroundColor: 'rgba(255,255,255,0.08)',
    top: -30,
    right: 60,
  },

  welcomeCardBg2: {
    position: 'absolute',
    width: 80,
    height: 80,
    borderRadius: 40,
    backgroundColor: 'rgba(255,255,255,0.06)',
    bottom: -20,
    left: 20,
  },

  welcomeInfo: { flex: 1 },

  welcomeLabel: {
    color: 'rgba(255,255,255,0.8)',
    fontSize: 13,
    marginBottom: 4,
  },

  userName: {
    color: '#fff',
    fontSize: 22,
    fontWeight: '900',
    marginBottom: 8,
    letterSpacing: 0.3,
  },

  roleBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: 'rgba(255,255,255,0.2)',
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 20,
    alignSelf: 'flex-start',
    gap: 5,
  },

  roleDot: {
    width: 6,
    height: 6,
    borderRadius: 3,
    backgroundColor: '#fff',
  },

  roleText: {
    color: '#fff',
    fontSize: 11,
    fontWeight: '700',
  },

  avatarIconContainer: {
    width: 54,
    height: 54,
    backgroundColor: '#fff',
    borderRadius: 18,
    justifyContent: 'center',
    alignItems: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },

  avatarInitialText: {
    fontSize: 22,
    fontWeight: '900',
    color: '#00AA5B',
  },

  statsRow: {
    backgroundColor: '#fff',
    borderRadius: 16,
    padding: 16,
    flexDirection: 'row',
    justifyContent: 'space-around',
    alignItems: 'center',
    marginBottom: 20,
    borderWidth: 1,
    borderColor: '#e8f5e9',
    shadowColor: '#00AA5B',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 6,
    elevation: 2,
  },

  statBox: {
    alignItems: 'center',
    gap: 4,
  },

  statValue: {
    fontSize: 13,
    fontWeight: '800',
    color: '#1a1a2e',
  },

  statLabel: {
    fontSize: 10,
    color: '#90a4ae',
    fontWeight: '600',
  },

  statDivider: {
    width: 1,
    height: 36,
    backgroundColor: '#e8f5e9',
  },

  sectionTitle: {
    fontSize: 16,
    fontWeight: '800',
    color: '#1a1a2e',
    marginBottom: 14,
    marginLeft: 2,
  },

  grid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    justifyContent: 'space-between',
    marginBottom: 16,
  },

  menuBox: {
    width: '47%',
    backgroundColor: '#fff',
    padding: 18,
    borderRadius: 20,
    alignItems: 'center',
    marginBottom: 14,
    borderWidth: 1,
    borderColor: '#e8f5e9',
    shadowColor: '#00AA5B',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.06,
    shadowRadius: 6,
    elevation: 2,
    justifyContent: 'center',
  },

  iconCircle: {
    padding: 14,
    borderRadius: 16,
    marginBottom: 10,
  },

  menuTitle: {
    fontSize: 13,
    fontWeight: '800',
    color: '#1a1a2e',
    textAlign: 'center',
  },

  promoBanner: {
    backgroundColor: '#00AA5B',
    borderRadius: 18,
    padding: 18,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    shadowColor: '#00AA5B',
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.28,
    shadowRadius: 10,
    elevation: 6,
  },

  promoLeft: { flex: 1 },

  promoTag: {
    fontSize: 11,
    color: 'rgba(255,255,255,0.8)',
    fontWeight: '700',
    marginBottom: 4,
  },

  promoTitle: {
    fontSize: 15,
    fontWeight: '900',
    color: '#fff',
    marginBottom: 3,
  },

  promoSub: {
    fontSize: 11,
    color: 'rgba(255,255,255,0.75)',
  },

  promoArrow: {
    width: 36,
    height: 36,
    borderRadius: 18,
    backgroundColor: 'rgba(255,255,255,0.25)',
    justifyContent: 'center',
    alignItems: 'center',
  },
});