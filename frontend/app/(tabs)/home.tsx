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

  // Format tanggal Indonesia
  const getFormattedDate = () => {
    const options: Intl.DateTimeFormatOptions = {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    };

    return new Date().toLocaleDateString('id-ID', options);
  };

  // Ambil huruf pertama nama user
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

  // Ambil jam yang valid dari berbagai kemungkinan field
  const dapatkanJamValid = (item: RiwayatItem): string => {
    const nilaiJam =
      item.jam ||
      item.waktu ||
      item.jam_tes ||
      item.jam_pelaksanaan;

    if (!nilaiJam) return "";

    // Hilangkan detik dari format HH:mm:ss
    if (
      nilaiJam.includes(':') &&
      nilaiJam.split(':').length === 3
    ) {
      const splitJam = nilaiJam.split(':');

      return `${splitJam[0]}:${splitJam[1]}`;
    }

    return nilaiJam;
  };

  // Ambil status yang paling valid
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
        // Ambil data terbaru
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
        // Jika belum ada riwayat
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

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <View>
          <Text style={styles.brandText}>STIFIn Mobile</Text>
          <Text style={styles.dateText}>
            {getFormattedDate()}
          </Text>
        </View>

        <TouchableOpacity style={styles.notifBtn}>
          <Ionicons
            name="notifications-outline"
            size={24}
            color="#1e293b"
          />

          <View style={styles.notifBadge} />
        </TouchableOpacity>
      </View>

      <ScrollView
        contentContainerStyle={styles.scrollContent}
        showsVerticalScrollIndicator={false}
      >
        <View style={styles.welcomeCard}>
          <View style={styles.welcomeInfo}>
            <Text style={styles.welcomeLabel}>
              Selamat Datang,
            </Text>

            <Text style={styles.userName}>
              {userName}
            </Text>

            <View style={styles.roleBadge}>
              <Text style={styles.roleText}>
                Klien Aktif
              </Text>
            </View>
          </View>

          {/* Inisial Nama */}
          <View style={styles.avatarIconContainer}>
            <Text style={styles.avatarInitialText}>
              {getInitialName(userName)}
            </Text>
          </View>
        </View>

        <Text style={styles.sectionTitle}>
          Layanan Utama
        </Text>

        <View style={styles.grid}>
          <MenuBox
            title="Daftar Tes"
            icon="create-outline"
            color="#3b82f6"
            onPress={() => router.push('/pendaftaran')}
          />

          <MenuBox
            title="Riwayat Tes"
            icon="calendar-outline"
            color="#10b981"
            onPress={() => router.push('/riwayat')}
          />

          <TouchableOpacity
            style={styles.menuBox}
            onPress={handleHasilTesNavigation}
            disabled={navLoading}
            activeOpacity={0.7}
          >
            {navLoading ? (
              <ActivityIndicator
                size="small"
                color="#f59e0b"
                style={styles.loaderSpacing}
              />
            ) : (
              <View
                style={[
                  styles.iconCircle,
                  { backgroundColor: '#f59e0b15' }
                ]}
              >
                <Ionicons
                  name="stats-chart-outline"
                  size={28}
                  color="#f59e0b"
                />
              </View>
            )}

            <Text style={styles.menuTitle}>
              Hasil Tes
            </Text>
          </TouchableOpacity>

          <MenuBox
            title="Panduan"
            icon="book-outline"
            color="#8b5cf6"
          />
        </View>
      </ScrollView>
    </SafeAreaView>
  );
}

const MenuBox = ({
  title,
  icon,
  color,
  onPress
}: any) => (
  <TouchableOpacity
    style={styles.menuBox}
    onPress={onPress}
    activeOpacity={0.7}
  >
    <View
      style={[
        styles.iconCircle,
        { backgroundColor: color + '15' }
      ]}
    >
      <Ionicons
        name={icon}
        size={28}
        color={color}
      />
    </View>

    <Text style={styles.menuTitle}>
      {title}
    </Text>
  </TouchableOpacity>
);

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f8fafc'
  },

  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    padding: 20,
    backgroundColor: '#fff',
    alignItems: 'center',
    borderBottomWidth: 1,
    borderBottomColor: '#f1f5f9'
  },

  brandText: {
    fontSize: 20,
    fontWeight: '800',
    color: '#1e293b'
  },

  dateText: {
    fontSize: 12,
    color: '#94a3b8',
    marginTop: 2,
    textTransform: 'capitalize'
  },

  notifBtn: {
    padding: 8,
    backgroundColor: '#f1f5f9',
    borderRadius: 12
  },

  notifBadge: {
    position: 'absolute',
    top: 10,
    right: 10,
    width: 8,
    height: 8,
    borderRadius: 4,
    backgroundColor: '#ef4444',
    borderWidth: 2,
    borderColor: '#fff'
  },

  scrollContent: {
    padding: 20
  },

  welcomeCard: {
    backgroundColor: '#1e293b',
    padding: 25,
    borderRadius: 24,
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 20
  },

  welcomeInfo: {
    flex: 1
  },

  welcomeLabel: {
    color: '#94a3b8',
    fontSize: 14
  },

  userName: {
    color: '#fff',
    fontSize: 24,
    fontWeight: 'bold',
    marginVertical: 4
  },

  roleBadge: {
    backgroundColor: 'rgba(59, 130, 246, 0.2)',
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 8,
    alignSelf: 'flex-start'
  },

  roleText: {
    color: '#3b82f6',
    fontSize: 11,
    fontWeight: '700'
  },

  avatarIconContainer: {
    width: 55,
    height: 55,
    backgroundColor: '#ffffff',
    borderRadius: 18,
    justifyContent: 'center',
    alignItems: 'center'
  },

  avatarInitialText: {
    fontSize: 22,
    fontWeight: 'bold',
    color: '#3b82f6'
  },

  sectionTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#1e293b',
    marginBottom: 15,
    marginLeft: 4
  },

  grid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    justifyContent: 'space-between'
  },

  menuBox: {
    width: '47%',
    backgroundColor: '#fff',
    padding: 20,
    borderRadius: 24,
    alignItems: 'center',
    marginBottom: 15,
    borderWidth: 1,
    borderColor: '#f1f5f9',
    justifyContent: 'center'
  },

  iconCircle: {
    padding: 16,
    borderRadius: 18,
    marginBottom: 12
  },

  menuTitle: {
    fontSize: 14,
    fontWeight: '700',
    color: '#334155'
  },

  loaderSpacing: {
    padding: 20,
    marginBottom: 12
  }
});