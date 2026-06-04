import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  SafeAreaView,
  TouchableOpacity,
  ScrollView,
  ActivityIndicator,
  Alert,
  Platform,
  StatusBar,
} from 'react-native';
import { useRouter } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';
import axiosInstance from '@/src/api/axiosConfig';
import AsyncStorage from '@react-native-async-storage/async-storage';

interface Jadwal {
  id_jadwal: number;
  tanggal: string;
  waktu: string;
  lokasi: string;
  status: string;
}

export default function PendaftaranTes() {
  const router = useRouter();
  const [userName, setUserName] = useState('User');
  const [tempatTes, setTempatTes] = useState('Kantor Cabang');
  const [isLuarSubang, setIsLuarSubang] = useState(false);
  const [jadwalData, setJadwalData] = useState<Jadwal[]>([]);
  const [loading, setLoading] = useState(true);
  const [selectedJadwal, setSelectedJadwal] = useState<Jadwal | null>(null);

  useEffect(() => {
    const loadData = async () => {
      try {
        const savedName = await AsyncStorage.getItem('user_name');
        if (savedName) setUserName(savedName);
        await fetchJadwal();
      } catch (error) {
        console.error('Error loading initial data:', error);
      }
    };
    loadData();
  }, []);

  const fetchJadwal = async () => {
    try {
      setLoading(true);
      const response = await axiosInstance.get('/jadwal-tersedia');
      setJadwalData(response.data);
    } catch (error) {
      Alert.alert('Error', 'Gagal mengambil data jadwal dari server.');
    } finally {
      setLoading(false);
    }
  };

  const filteredJadwal = jadwalData.filter((item) => {
    const lokasiDB = String(item.lokasi || '').toLowerCase().trim();
    const lokasiPilihan = String(tempatTes || '').toLowerCase().trim();
    const statusDB = String(item.status || '').toLowerCase().trim();
    return lokasiDB === lokasiPilihan && statusDB === 'tersedia';
  });

  // Navigasi ke halaman form + kirim is_luar_subang sebagai params
  const handleNext = () => {
    if (!selectedJadwal) {
      Alert.alert('Pilih Jadwal', 'Silakan pilih salah satu jadwal yang tersedia.');
      return;
    }

    router.push({
      pathname: '/form-pendaftaran',
      params: {
        id_jadwal: selectedJadwal.id_jadwal,
        tanggal: selectedJadwal.tanggal,
        waktu: selectedJadwal.waktu,
        is_luar_subang: isLuarSubang ? '1' : '0', // Kirim sebagai string '1'/'0'
      },
    });
  };

  const lokasiOptions = ['Home Visit', 'Kantor Cabang'];

  return (
    <SafeAreaView style={styles.container}>

      {/* Green Top Bar */}
      <View style={styles.topBar}>
        <TouchableOpacity style={styles.backBtn} onPress={() => router.back()}>
          <Ionicons name="arrow-back-outline" size={22} color="#fff" />
        </TouchableOpacity>
        <View style={styles.topBarCenter}>
          <Text style={styles.topBarTitle}>Pilih Jadwal Tes</Text>
          <Text style={styles.topBarSub}>Halo, {userName}!</Text>
        </View>
        <View style={{ width: 38 }} />
      </View>

      <ScrollView contentContainerStyle={styles.content} showsVerticalScrollIndicator={false}>

        {/* Info Banner */}
        <View style={styles.infoBanner}>
          <Ionicons name="information-circle-outline" size={18} color="#0288d1" />
          <Text style={styles.infoBannerText}>
            Pilih lokasi dan waktu tes yang sesuai dengan Anda
          </Text>
        </View>

        {/* === Selector: Dalam/Luar Subang (menentukan harga) === */}
        <View style={styles.locationCard}>
          <Text style={styles.locationLabel}>
            <Ionicons name="cash-outline" size={13} color="#546e7a" /> Wilayah & Biaya
          </Text>
          <View style={styles.locationOptions}>
            <TouchableOpacity
              style={[styles.locationChip, !isLuarSubang && styles.locationChipActive]}
              onPress={() => setIsLuarSubang(false)}
              activeOpacity={0.7}
            >
              <Ionicons
                name="home-outline"
                size={14}
                color={!isLuarSubang ? '#fff' : '#546e7a'}
              />
              <Text style={[styles.locationChipText, !isLuarSubang && styles.locationChipTextActive]}>
                Dalam Subang{'\n'}
                <Text style={styles.hargaChip}>Rp 550.000</Text>
              </Text>
            </TouchableOpacity>

            <TouchableOpacity
              style={[styles.locationChip, isLuarSubang && styles.locationChipActive]}
              onPress={() => setIsLuarSubang(true)}
              activeOpacity={0.7}
            >
              <Ionicons
                name="map-outline"
                size={14}
                color={isLuarSubang ? '#fff' : '#546e7a'}
              />
              <Text style={[styles.locationChipText, isLuarSubang && styles.locationChipTextActive]}>
                Luar Subang{'\n'}
                <Text style={styles.hargaChip}>Rp 650.000</Text>
              </Text>
            </TouchableOpacity>
          </View>
        </View>

        {/* === Selector: Lokasi Tes (Home Visit / Kantor Cabang) === */}
        <View style={styles.locationCard}>
          <Text style={styles.locationLabel}>
            <Ionicons name="location-outline" size={13} color="#546e7a" /> Lokasi Tes
          </Text>
          <View style={styles.locationOptions}>
            {lokasiOptions.map((val) => (
              <TouchableOpacity
                key={val}
                style={[styles.locationChip, tempatTes === val && styles.locationChipActive]}
                onPress={() => {
                  setTempatTes(val);
                  setSelectedJadwal(null);
                }}
                activeOpacity={0.7}
              >
                <Ionicons
                  name={val === 'Home Visit' ? 'home-outline' : 'business-outline'}
                  size={14}
                  color={tempatTes === val ? '#fff' : '#546e7a'}
                />
                <Text
                  style={[
                    styles.locationChipText,
                    tempatTes === val && styles.locationChipTextActive,
                  ]}
                >
                  {val}
                </Text>
              </TouchableOpacity>
            ))}
          </View>
        </View>

        {/* Schedule Table */}
        <View style={styles.tableCard}>
          <View style={styles.tableCardHeader}>
            <View style={styles.tableCardHeaderLeft}>
              <Ionicons name="calendar-outline" size={18} color="#00AA5B" />
              <Text style={styles.tableCardTitle}>Jadwal Tersedia</Text>
            </View>
            <View style={styles.countBadge}>
              <Text style={styles.countBadgeText}>{filteredJadwal.length} Slot</Text>
            </View>
          </View>

          {/* Table Head */}
          <View style={styles.rowHeader}>
            <Text style={[styles.col, styles.colHeader]}>Tanggal</Text>
            <Text style={[styles.colMid, styles.colHeader]}>Waktu</Text>
            <Text style={[styles.colEnd, styles.colHeader]}>Status</Text>
          </View>

          {loading ? (
            <View style={styles.loadingBox}>
              <ActivityIndicator size="large" color="#00AA5B" />
              <Text style={styles.loadingText}>Memuat jadwal...</Text>
            </View>
          ) : filteredJadwal.length > 0 ? (
            filteredJadwal.map((item) => {
              const isSelected = selectedJadwal?.id_jadwal === item.id_jadwal;
              return (
                <TouchableOpacity
                  key={item.id_jadwal}
                  onPress={() => setSelectedJadwal(item)}
                  style={[styles.rowData, isSelected && styles.selectedRow]}
                  activeOpacity={0.7}
                >
                  {isSelected && <View style={styles.selectedIndicator} />}
                  <Text style={[styles.col, isSelected && styles.selectedText]}>
                    {item.tanggal}
                  </Text>
                  <Text style={[styles.colMid, isSelected && styles.selectedText]}>
                    {item.waktu} WIB
                  </Text>
                  <View style={styles.colEnd}>
                    <View style={styles.statusBadge}>
                      <View style={styles.statusDot} />
                      <Text style={styles.statusText}>{item.status}</Text>
                    </View>
                  </View>
                </TouchableOpacity>
              );
            })
          ) : (
            <View style={styles.emptyState}>
              <View style={styles.emptyIconBg}>
                <Ionicons name="calendar-outline" size={32} color="#90a4ae" />
              </View>
              <Text style={styles.emptyTitle}>Tidak Ada Jadwal</Text>
              <Text style={styles.emptyText}>
                Belum ada jadwal tersedia untuk lokasi ini.
              </Text>
            </View>
          )}
        </View>

        {/* Selected Info */}
        {selectedJadwal && (
          <View style={styles.selectedInfo}>
            <Ionicons name="checkmark-circle" size={18} color="#00AA5B" />
            <Text style={styles.selectedInfoText}>
              Dipilih:{' '}
              <Text style={{ fontWeight: '800' }}>{selectedJadwal.tanggal}</Text> pukul{' '}
              {selectedJadwal.waktu} WIB
            </Text>
          </View>
        )}

        {/* Footer Buttons */}
        <View style={styles.footerBtns}>
          <TouchableOpacity
            style={styles.btnBack}
            onPress={() => router.back()}
            activeOpacity={0.7}
          >
            <Ionicons name="arrow-back" size={16} color="#546e7a" />
            <Text style={styles.btnBackText}>Kembali</Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={[styles.btnNext, !selectedJadwal && styles.btnNextDisabled]}
            onPress={handleNext}
            disabled={!selectedJadwal}
            activeOpacity={0.85}
          >
            <Text style={[styles.btnNextText, !selectedJadwal && styles.btnNextTextDisabled]}>
              Berikutnya
            </Text>
            <Ionicons
              name="arrow-forward"
              size={16}
              color={selectedJadwal ? '#fff' : '#b0bec5'}
            />
          </TouchableOpacity>
        </View>

      </ScrollView>
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
    width: 38,
    height: 38,
    borderRadius: 19,
    backgroundColor: 'rgba(255,255,255,0.2)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  topBarCenter: { flex: 1, alignItems: 'center' },
  topBarTitle: { fontSize: 18, fontWeight: '800', color: '#fff' },
  topBarSub: { fontSize: 12, color: 'rgba(255,255,255,0.8)', marginTop: 2 },

  content: { padding: 16, paddingBottom: 48 },

  infoBanner: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#e1f5fe',
    borderRadius: 12,
    paddingHorizontal: 14,
    paddingVertical: 10,
    marginBottom: 16,
    gap: 8,
    borderLeftWidth: 3,
    borderLeftColor: '#0288d1',
  },
  infoBannerText: { fontSize: 12, color: '#01579b', fontWeight: '600', flex: 1 },

  locationCard: {
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
  locationLabel: {
    fontSize: 11,
    fontWeight: '700',
    color: '#546e7a',
    textTransform: 'uppercase',
    letterSpacing: 0.6,
    marginBottom: 12,
  },
  locationOptions: { flexDirection: 'row', gap: 10 },
  locationChip: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 6,
    paddingVertical: 11,
    borderRadius: 12,
    backgroundColor: '#f5faf7',
    borderWidth: 1.5,
    borderColor: '#e0f2ec',
  },
  locationChipActive: {
    backgroundColor: '#00AA5B',
    borderColor: '#00AA5B',
    shadowColor: '#00AA5B',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.25,
    shadowRadius: 6,
    elevation: 4,
  },
  locationChipText: { fontSize: 13, fontWeight: '700', color: '#546e7a' },
  locationChipTextActive: { color: '#fff' },
  hargaChip: { fontSize: 11, fontWeight: '600' },

  tableCard: {
    backgroundColor: '#fff',
    borderRadius: 16,
    overflow: 'hidden',
    borderWidth: 1,
    borderColor: '#e8f5e9',
    shadowColor: '#00AA5B',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 6,
    elevation: 2,
    marginBottom: 14,
  },
  tableCardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: 14,
    borderBottomWidth: 1,
    borderColor: '#e8f5e9',
    backgroundColor: '#f5faf7',
  },
  tableCardHeaderLeft: { flexDirection: 'row', alignItems: 'center', gap: 6 },
  tableCardTitle: { fontSize: 13, fontWeight: '800', color: '#1a1a2e' },
  countBadge: {
    backgroundColor: '#e8f5e9',
    borderRadius: 12,
    paddingHorizontal: 10,
    paddingVertical: 3,
  },
  countBadgeText: { fontSize: 11, color: '#00AA5B', fontWeight: '800' },

  rowHeader: {
    flexDirection: 'row',
    paddingHorizontal: 14,
    paddingVertical: 10,
    borderBottomWidth: 1,
    borderColor: '#f5faf7',
    backgroundColor: '#fafffe',
  },
  colHeader: { color: '#90a4ae', fontSize: 11, fontWeight: '700', textTransform: 'uppercase' },
  rowData: {
    flexDirection: 'row',
    paddingHorizontal: 14,
    paddingVertical: 14,
    borderBottomWidth: 1,
    borderColor: '#f5faf7',
    alignItems: 'center',
    position: 'relative',
  },
  selectedRow: { backgroundColor: '#e8f5e9', borderColor: '#00AA5B' },
  selectedIndicator: {
    position: 'absolute',
    left: 0,
    top: 0,
    bottom: 0,
    width: 3,
    backgroundColor: '#00AA5B',
    borderRadius: 2,
  },
  col: { flex: 2, fontSize: 13, color: '#37474f', fontWeight: '600' },
  colMid: { flex: 2, fontSize: 13, color: '#37474f', fontWeight: '600' },
  colEnd: { flex: 1.5, alignItems: 'flex-end' },
  selectedText: { color: '#00AA5B', fontWeight: '700' },
  statusBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#e8f5e9',
    borderRadius: 12,
    paddingHorizontal: 8,
    paddingVertical: 4,
    gap: 4,
  },
  statusDot: { width: 5, height: 5, borderRadius: 3, backgroundColor: '#00AA5B' },
  statusText: { fontSize: 10, color: '#00AA5B', fontWeight: '800', textTransform: 'uppercase' },

  loadingBox: { padding: 40, alignItems: 'center', gap: 10 },
  loadingText: { fontSize: 12, color: '#90a4ae' },
  emptyState: { padding: 36, alignItems: 'center' },
  emptyIconBg: {
    width: 70,
    height: 70,
    borderRadius: 35,
    backgroundColor: '#f5faf7',
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 12,
  },
  emptyTitle: { fontSize: 15, fontWeight: '700', color: '#546e7a', marginBottom: 4 },
  emptyText: { fontSize: 12, color: '#90a4ae', textAlign: 'center' },

  selectedInfo: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    backgroundColor: '#e8f5e9',
    borderRadius: 12,
    padding: 12,
    marginBottom: 16,
    borderWidth: 1,
    borderColor: '#a5d6a7',
  },
  selectedInfoText: { fontSize: 13, color: '#2e7d32', flex: 1 },

  footerBtns: { flexDirection: 'row', gap: 12, marginTop: 4, marginBottom: 16 },
  btnBack: {
    flex: 1,
    backgroundColor: '#fff',
    paddingVertical: 14,
    borderRadius: 12,
    alignItems: 'center',
    flexDirection: 'row',
    justifyContent: 'center',
    gap: 6,
    borderWidth: 1.5,
    borderColor: '#e0f2ec',
  },
  btnBackText: { color: '#546e7a', fontWeight: '700', fontSize: 14 },
  btnNext: {
    flex: 1,
    backgroundColor: '#00AA5B',
    paddingVertical: 14,
    borderRadius: 12,
    alignItems: 'center',
    flexDirection: 'row',
    justifyContent: 'center',
    gap: 6,
    shadowColor: '#00AA5B',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 8,
    elevation: 4,
  },
  btnNextDisabled: { backgroundColor: '#e0e0e0', shadowOpacity: 0, elevation: 0 },
  btnNextText: { color: '#fff', fontWeight: '800', fontSize: 14 },
  btnNextTextDisabled: { color: '#b0bec5' },
});