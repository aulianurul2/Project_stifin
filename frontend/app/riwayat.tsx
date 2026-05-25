import React, { useState, useCallback } from 'react';
import {
  View,
  Text,
  StyleSheet,
  SafeAreaView,
  ScrollView,
  ActivityIndicator,
  TouchableOpacity,
  ViewStyle,
  Platform
} from 'react-native';

import { useFocusEffect } from 'expo-router';
import { useRouter } from 'expo-router';
import AsyncStorage from '@react-native-async-storage/async-storage';
import axiosInstance from '@/src/api/axiosConfig';
import Ionicons from '@expo/vector-icons/Ionicons';

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

  // FIX: Mengamankan mapping status agar seragam dengan data backend
 const dapatkanStatusValid = (item: RiwayatItem): string => {
  const statusUtama = item.status ? item.status.trim() : "";
  const statusHasil = item.status_tes ? item.status_tes.trim() : "";

  // Pastikan status "Ditolak" dari kolom 'status' (jadwal) diutamakan
  if (statusUtama.toLowerCase() === "ditolak") return "Ditolak";
  
  if (statusUtama === "Menunggu" || statusUtama === "Konfirmasi") return "Menunggu";

  if (statusHasil) {
    if (statusHasil.toLowerCase() === 'proses') return "Diproses";
    if (statusHasil.toLowerCase() === 'selesai') return "Selesai";
    // Tambahan: jika ada status ditolak di tabel hasil tes
    if (statusHasil.toLowerCase() === 'ditolak') return "Ditolak"; 
    return statusHasil;
  }

  return statusUtama || "Menunggu";
};

  // FIX: Sinkronisasi warna hex pembacaan status baru
 const warnaStatus = (status: string) => {
  const s = status.toLowerCase();
  if (s === "selesai") return "#00AA5B"; // Hijau (Selesai)
  if (s === "menunggu" || s === "konfirmasi") return "#f57c00"; // Oranye (Menunggu)
  if (s === "diproses" || s === "proses" || s === "diterima") return "#0288d1"; // Biru (Diproses)
  
  // Pastikan ini ada dan warnanya kontras (Merah)
  if (s === "ditolak") return "#e53935"; 
  
  if (s === "dibatalkan") return "#78909c"; // Abu-abu
  return "#0288d1";
};

  const warnaBgStatus = (status: string) => {
    const s = status.toLowerCase();
    if (s === "selesai") return "#e8f5e9";
    if (s === "menunggu" || s === "konfirmasi") return "#fff3e0";
    if (s === "diproses" || s === "proses" || s === "diterima") return "#e1f5fe";
    if (s === "ditolak") return "#ffebee";
    if (s === "dibatalkan") return "#f5f5f5";
    return "#e1f5fe";
  };

  const ikonStatus = (status: string): keyof typeof Ionicons.glyphMap => {
    const s = status.toLowerCase();
    if (s === "selesai") return "checkmark-circle";
    if (s === "menunggu" || s === "konfirmasi") return "time-outline";
    if (s === "diproses" || s === "proses" || s === "diterima") return "sync-outline";
    if (s === "ditolak") return "close-circle";
    if (s === "dibatalkan") return "ban-outline";
    return "ellipse-outline";
  };

  const handleRowPress = (item: RiwayatItem) => {
    console.log("ITEM =", item);
    
    router.push({
      pathname: '/hasil-tes',
      params: {
        id_jadwal: item.id_jadwal.toString(),
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

      {/* Green Top Bar */}
      <View style={styles.topBar}>
        <TouchableOpacity style={styles.backBtn} onPress={() => router.replace('/home')}>
          <Ionicons name="arrow-back-outline" size={22} color="#fff" />
        </TouchableOpacity>
        <View style={styles.topBarCenter}>
          <Text style={styles.topBarTitle}>Riwayat Tes</Text>
          <Text style={styles.topBarSub}>STIFIn Genetic Test</Text>
        </View>
        <View style={{ width: 38 }} />
      </View>

      {/* User Greeting */}
      <View style={styles.greetingBar}>
        <View style={styles.greetingAvatar}>
          <Text style={styles.greetingAvatarText}>
            {userName.trim().charAt(0).toUpperCase()}
          </Text>
        </View>
        <View>
          <Text style={styles.greetingName}>Halo, {userName}!</Text>
          <Text style={styles.greetingSubtitle}>Berikut riwayat pendaftaran tes Anda</Text>
        </View>
      </View>

      <ScrollView
        contentContainerStyle={styles.content}
        showsVerticalScrollIndicator={false}
      >
        {loading ? (
          <View style={styles.loadingBox}>
            <ActivityIndicator size="large" color="#00AA5B" />
            <Text style={styles.loadingText}>Memuat riwayat...</Text>
          </View>

        ) : riwayat.length === 0 ? (

          /* Empty State */
          <View style={styles.emptyContainer}>
            <View style={styles.emptyIconWrap}>
              <Ionicons name="document-text-outline" size={48} color="#90a4ae" />
            </View>
            <Text style={styles.emptyTitle}>Belum Ada Riwayat Tes</Text>
            <Text style={styles.emptySubtitle}>
              Anda belum melakukan pendaftaran tes STIFIn atau jadwal yang Anda pilih sebelumnya telah dihapus.
            </Text>
            <TouchableOpacity 
              style={styles.emptyButton} 
              onPress={() => router.push('/pendaftaran')}
              activeOpacity={0.85}
            >
              <Ionicons name="add-circle-outline" size={18} color="#fff" />
              <Text style={styles.emptyButtonText}>Daftar Tes Sekarang</Text>
            </TouchableOpacity>
          </View>

        ) : (

          /* Riwayat List */
          <View>
            {/* Summary Chip */}
            <View style={styles.summaryChip}>
              <Ionicons name="list-outline" size={14} color="#00AA5B" />
              <Text style={styles.summaryText}>
                {riwayat.length} riwayat pendaftaran ditemukan
              </Text>
            </View>

            <View style={styles.listContainer}>
              {riwayat.map((item, index) => {
                const statusAktif = dapatkanStatusValid(item);
                const jamAktif = dapatkanJamValid(item);
                const warna = warnaStatus(statusAktif);
                const warnaBg = warnaBgStatus(statusAktif);
                const ikon = ikonStatus(statusAktif);
                const adaBerkas = item.file_hasil || item.file_detail;

                return (
                  <TouchableOpacity
                    key={item.id_jadwal || index}
                    style={styles.card}
                    onPress={() => handleRowPress(item)}
                    activeOpacity={0.7}
                  >
                    {/* Left accent bar */}
                    <View style={[styles.cardAccent, { backgroundColor: warna }]} />

                    <View style={styles.cardBody}>
                      {/* Top row: tanggal + status badge */}
                      <View style={styles.cardTopRow}>
                        <View style={styles.cardDateWrap}>
                          <View style={styles.cardDateIcon}>
                            <Ionicons name="calendar-outline" size={13} color="#00AA5B" />
                          </View>
                          <Text style={styles.cardTanggal}>{item.tanggal || '—'}</Text>
                        </View>

                        <View style={[styles.statusBadge, { backgroundColor: warnaBg }]}>
                          <Ionicons name={ikon} size={12} color={warna} />
                          <Text style={[styles.statusBadgeText, { color: warna }]}>
                            {statusAktif}
                          </Text>
                        </View>
                      </View>

                      {/* Middle row: jam */}
                      <View style={styles.cardMidRow}>
                        <Ionicons name="time-outline" size={13} color="#90a4ae" />
                        <Text style={styles.cardJam}>
                          {jamAktif ? `${jamAktif} WIB` : 'Waktu belum ditentukan'}
                        </Text>
                      </View>

                      {/* Bottom row: berkas indicator + chevron */}
                      <View style={styles.cardBottomRow}>
                        {adaBerkas ? (
                          <View style={styles.berkasBadge}>
                            <Ionicons name="document-attach-outline" size={12} color="#00AA5B" />
                            <Text style={styles.berkasBadgeText}>Berkas Tersedia</Text>
                          </View>
                        ) : (
                          <View style={styles.noberkasBadge}>
                            <Ionicons name="document-outline" size={12} color="#90a4ae" />
                            <Text style={styles.noberkasBadgeText}>Belum Ada Berkas</Text>
                          </View>
                        )}

                        <View style={styles.chevronWrap}>
                          <Ionicons name="chevron-forward" size={16} color="#00AA5B" />
                        </View>
                      </View>
                    </View>
                  </TouchableOpacity>
                );
              })}
            </View>
          </View>
        )}
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f5faf7',
  },

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

  greetingBar: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 12,
    paddingHorizontal: 16,
    paddingVertical: 14,
    backgroundColor: '#fff',
    borderBottomWidth: 1,
    borderBottomColor: '#e8f5e9',
  },
  greetingAvatar: {
    width: 42,
    height: 42,
    borderRadius: 14,
    backgroundColor: '#00AA5B',
    justifyContent: 'center',
    alignItems: 'center',
    shadowColor: '#00AA5B',
    shadowOffset: { width: 0, height: 3 },
    shadowOpacity: 0.25,
    shadowRadius: 6,
    elevation: 3,
  },
  greetingAvatarText: {
    fontSize: 18,
    fontWeight: '900',
    color: '#fff',
  },
  greetingName: {
    fontSize: 15,
    fontWeight: '800',
    color: '#1a1a2e',
  },
  greetingSubtitle: {
    fontSize: 11,
    color: '#90a4ae',
    marginTop: 2,
  },

  content: {
    padding: 16,
    paddingBottom: 40,
    flexGrow: 1,
  },

  loadingBox: {
    marginTop: 60,
    alignItems: 'center',
    gap: 12,
  },
  loadingText: {
    fontSize: 13,
    color: '#90a4ae',
    fontWeight: '600',
  },

  emptyContainer: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    paddingHorizontal: 20,
    marginTop: 60,
  },
  emptyIconWrap: {
    width: 100,
    height: 100,
    borderRadius: 50,
    backgroundColor: '#fff',
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 20,
    borderWidth: 2,
    borderColor: '#e0f2ec',
    borderStyle: 'dashed',
  },
  emptyTitle: {
    fontSize: 18,
    fontWeight: '800',
    color: '#1a1a2e',
    marginBottom: 8,
    textAlign: 'center',
  },
  emptySubtitle: {
    fontSize: 13,
    color: '#90a4ae',
    textAlign: 'center',
    lineHeight: 20,
    marginBottom: 28,
  },
  emptyButton: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    backgroundColor: '#00AA5B',
    paddingVertical: 13,
    paddingHorizontal: 24,
    borderRadius: 14,
    shadowColor: '#00AA5B',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 8,
    elevation: 4,
  },
  emptyButtonText: {
    color: '#fff',
    fontWeight: '800',
    fontSize: 14,
  },

  summaryChip: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    backgroundColor: '#e8f5e9',
    alignSelf: 'flex-start',
    borderRadius: 12,
    paddingHorizontal: 12,
    paddingVertical: 6,
    marginBottom: 14,
  },
  summaryText: {
    fontSize: 12,
    color: '#2e7d32',
    fontWeight: '700',
  },

  listContainer: {
    gap: 10,
  },

  card: {
    backgroundColor: '#fff',
    borderRadius: 16,
    flexDirection: 'row',
    overflow: 'hidden',
    borderWidth: 1,
    borderColor: '#e8f5e9',
    shadowColor: '#00AA5B',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.06,
    shadowRadius: 8,
    elevation: 2,
  },

  cardAccent: {
    width: 4,
    borderRadius: 2,
  },

  cardBody: {
    flex: 1,
    padding: 14,
    gap: 8,
  },

  cardTopRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },

  cardDateWrap: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
  },
  cardDateIcon: {
    width: 22,
    height: 22,
    borderRadius: 6,
    backgroundColor: '#e8f5e9',
    justifyContent: 'center',
    alignItems: 'center',
  },
  cardTanggal: {
    fontSize: 14,
    fontWeight: '800',
    color: '#1a1a2e',
  },

  statusBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 20,
  },
  statusBadgeText: {
    fontSize: 11,
    fontWeight: '800',
  },

  cardMidRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 5,
  },
  cardJam: {
    fontSize: 12,
    color: '#78909c',
    fontWeight: '600',
  },

  cardBottomRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginTop: 2,
  },

  berkasBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
    backgroundColor: '#e8f5e9',
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: 8,
  },
  berkasBadgeText: {
    fontSize: 10,
    color: '#2e7d32',
    fontWeight: '700',
  },

  noberkasBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
    backgroundColor: '#f5f5f5',
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: 8,
  },
  noberkasBadgeText: {
    fontSize: 10,
    color: '#90a4ae',
    fontWeight: '600',
  },

  chevronWrap: {
    width: 28,
    height: 28,
    borderRadius: 8,
    backgroundColor: '#e8f5e9',
    justifyContent: 'center',
    alignItems: 'center',
  },
});