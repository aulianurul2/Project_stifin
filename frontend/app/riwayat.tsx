import React, { useState, useCallback } from 'react';
import {
  View,
  Text,
  StyleSheet,
  SafeAreaView,
  ScrollView,
  ActivityIndicator,
  TouchableOpacity,
  ViewStyle
} from 'react-native';

import { useFocusEffect } from 'expo-router';
import { useRouter } from 'expo-router';
import AsyncStorage from '@react-native-async-storage/async-storage';
import axiosInstance from '@/src/api/axiosConfig';
import Ionicons from '@expo/vector-icons/Ionicons';

interface RiwayatItem {
  id: number;
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

export default function RiwayatJadwal() {
  const router = useRouter();
  const [userName, setUserName] = useState("User");
  const [riwayat, setRiwayat] = useState<RiwayatItem[]>([]);
  const [loading, setLoading] = useState(true);

  useFocusEffect(
    useCallback(() => {
      loadData();
    }, [])
  );

  const loadData = async () => {
    const savedName = await AsyncStorage.getItem('user_name');
    if (savedName) setUserName(savedName);
    fetchRiwayat();
  };

  const fetchRiwayat = async () => {
    try {
      setLoading(true);
      const response = await axiosInstance.get('/riwayat-pendaftaran');
      setRiwayat(response.data);
    } catch (error) {
      console.log("Gagal ambil data database:", error);
    } finally {
      setLoading(false);
    }
  };

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
    return item.status_tes || item.status || "Menunggu";
  };

  const warnaStatus = (status: string) => {
    const s = status.toLowerCase();
    if (s === "selesai") return "#16a34a";
    if (s === "menunggu" || s === "proses") return "#eab308";
    if (s === "ditolak") return "#dc2626";
    return "#2563eb";
  };

  const handleRowPress = (item: RiwayatItem) => {
    router.push({
      pathname: '/hasil-tes',
      params: {
        tanggal: item.tanggal || '',
        jam: dapatkanJamValid(item) || '',
        file_hasil: item.file_hasil ? item.file_hasil : 'null',
        file_detail: item.file_detail ? item.file_detail : 'null',
        status: dapatkanStatusValid(item),
        komentar: item.komentar ? item.komentar : 'null'
      }
    });
  };

  return (
    <SafeAreaView style={styles.container}>
      {/* Header Section */}
      <View style={styles.header}>
        <TouchableOpacity style={styles.menuIcon} onPress={() => router.replace('/home')}>
          <Ionicons name="arrow-back-outline" size={28} color="#1e293b" />
        </TouchableOpacity>
        <Text style={styles.title}>Halo, {userName}</Text>
        <Text style={styles.subTitle}>Riwayat Tes STIFIn Anda</Text>
      </View>

      <ScrollView contentContainerStyle={styles.content} showsVerticalScrollIndicator={false}>
        {loading ? (
          <ActivityIndicator size="large" color="#2563eb" style={{ marginTop: 40 }} />
        ) : riwayat.length === 0 ? (
          
          /* Tampilan Elegan & Interaktif Saat Riwayat Kosong */
          <View style={styles.emptyContainer}>
            <View style={styles.iconCircleBackground}>
              <Ionicons name="document-text-outline" size={48} color="#94a3b8" />
            </View>
            <Text style={styles.emptyTitle}>Belum Ada Riwayat Tes</Text>
            <Text style={styles.emptySubtitle}>
              Anda belum melakukan pendaftaran tes STIFIn atau jadwal yang Anda pilih sebelumnya telah dihapus.
            </Text>
            
            <TouchableOpacity 
              style={styles.emptyButton} 
              onPress={() => router.push('/pendaftaran')}
              activeOpacity={0.8}
            >
              <Ionicons name="add-circle-outline" size={20} color="#fff" style={{ marginRight: 6 }} />
              <Text style={styles.emptyButtonText}>Daftar Tes Sekarang</Text>
            </TouchableOpacity>
          </View>

        ) : (
          /* Tampilan Tabel Jika Data Riwayat Ada */
          <View style={styles.tableContainer}>
            <View style={styles.tableHeaderRow}>
              <Text style={[styles.th, { flex: 2.5 }]}>Jadwal Tes</Text>
              <Text style={[styles.th, { flex: 2, textAlign: 'center' }]}>Status</Text>
              <Text style={[styles.th, { flex: 0.8, textAlign: 'right' }]}></Text>
            </View>

            {riwayat.map((item, index) => {
              const statusAktif = dapatkanStatusValid(item);
              const jamAktif = dapatkanJamValid(item);

              return (
                <TouchableOpacity
                  key={item.id || index}
                  style={styles.tableRow}
                  onPress={() => handleRowPress(item)}
                  activeOpacity={0.7}
                >
                  <View style={{ flex: 2.5 }}>
                    <Text style={styles.tdTanggal}>{item.tanggal || '—'}</Text>
                    <Text style={styles.tdWaktu}>
                      {jamAktif ? `${jamAktif} WIB` : '—'}
                    </Text>
                  </View>

                  <View style={{ flex: 2, alignItems: 'center', justifyContent: 'center' }}>
                    <View style={[styles.badge, { backgroundColor: warnaStatus(statusAktif) }]}>
                      <Text style={styles.badgeText}>{statusAktif}</Text>
                    </View>
                  </View>

                  <View style={{ flex: 0.8, alignItems: 'flex-end', justifyContent: 'center' }}>
                    <Ionicons name="chevron-forward" size={18} color="#64748b" />
                  </View>
                </TouchableOpacity>
              );
            })}
          </View>
        )}
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f8fafc' },
  header: { padding: 20, paddingBottom: 10, paddingTop: 40 },
  menuIcon: { marginBottom: 15 },
  title: { fontSize: 24, fontWeight: 'bold', color: '#0f172a' },
  subTitle: { fontSize: 14, color: '#64748b', marginTop: 4 },
  content: { padding: 20, flexGrow: 1 },
  
  // Styles Baru untuk Empty State
  emptyContainer: { 
    flex: 1, 
    alignItems: 'center', 
    justifyContent: 'center', 
    paddingHorizontal: 20,
    marginTop: 60
  },
  iconCircleBackground: {
    width: 100,
    height: 100,
    borderRadius: 50,
    backgroundColor: '#f1f5f9',
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 20,
  },
  emptyTitle: { 
    fontSize: 18, 
    fontWeight: '700', 
    color: '#1e293b', 
    marginBottom: 8 
  },
  emptySubtitle: { 
    fontSize: 13, 
    color: '#94a3b8', 
    textAlign: 'center', 
    lineHeight: 20,
    marginBottom: 25
  },
  emptyButton: {
    flexDirection: 'row',
    backgroundColor: '#2563eb',
    paddingVertical: 12,
    paddingHorizontal: 24,
    borderRadius: 12,
    alignItems: 'center',
    elevation: 2,
    shadowColor: '#2563eb',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.15,
    shadowRadius: 4,
  },
  emptyButtonText: { 
    color: '#ffffff', 
    fontWeight: '700', 
    fontSize: 14 
  },

  // Styles Tabel Eksisting
  tableContainer: { backgroundColor: '#ffffff', borderRadius: 16, overflow: 'hidden', elevation: 2, shadowColor: '#0f172a', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.05, shadowRadius: 8 },
  tableHeaderRow: { flexDirection: 'row', backgroundColor: '#f1f5f9', paddingVertical: 14, paddingHorizontal: 16, borderBottomWidth: 1, borderColor: '#e2e8f0' } as ViewStyle,
  th: { fontSize: 13, fontWeight: '700', color: '#475569' },
  tableRow: { flexDirection: 'row', paddingVertical: 16, paddingHorizontal: 16, borderBottomWidth: 1, borderColor: '#f1f5f9', alignItems: 'center' } as ViewStyle,
  tdTanggal: { fontSize: 14, fontWeight: '600', color: '#1e293b' },
  tdWaktu: { fontSize: 12, color: '#64748b', marginTop: 2 },
  badge: { paddingVertical: 4, paddingHorizontal: 10, borderRadius: 12 },
  badgeText: { color: '#fff', fontWeight: '700', fontSize: 11 },
});