import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  SafeAreaView,
  TouchableOpacity,
  Platform,
  ScrollView,
  ActivityIndicator,
  Alert,
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

export interface TransportOption {
  label: string;
  biaya: number;
}

export const TRANSPORT_DALAM_SUBANG: TransportOption[] = [
  { label: 'Kota Subang', biaya: 25000 },
  { label: 'Kab. Subang', biaya: 50000 },
];

const BIAYA_TES = 550000;
const BIAYA_TRANSPORT_LUAR = 75000;

export function formatRupiah(nominal: number): string {
  return 'Rp ' + nominal.toLocaleString('id-ID');
}

export default function PendaftaranTes() {
  const router = useRouter();
  const [userName, setUserName] = useState('User');
  const [tempatTes, setTempatTes] = useState('Kantor Subang');
  const [wilayahHomeVisit, setWilayahHomeVisit] = useState<'dalam' | 'luar'>('dalam');

  // Transport dropdown untuk Dalam Subang
  const [selectedTransport, setSelectedTransport] = useState<TransportOption>(TRANSPORT_DALAM_SUBANG[0]);
  const [dropdownOpen, setDropdownOpen] = useState(false);

  const [jadwalData, setJadwalData] = useState<Jadwal[]>([]);
  const [loading, setLoading] = useState(true);
  const [selectedJadwal, setSelectedJadwal] = useState<Jadwal | null>(null);

  // Derived
  const isLuarSubang = tempatTes === 'Home Visit' && wilayahHomeVisit === 'luar';

  const getBiaya = (): number => {
    if (tempatTes !== 'Home Visit') return BIAYA_TES;
    if (wilayahHomeVisit === 'luar') return BIAYA_TES + BIAYA_TRANSPORT_LUAR;
    return BIAYA_TES + selectedTransport.biaya;
  };

  const getTransportBiaya = (): number => {
    if (tempatTes !== 'Home Visit') return 0;
    if (wilayahHomeVisit === 'luar') return BIAYA_TRANSPORT_LUAR;
    return selectedTransport.biaya;
  };

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
    const lokasiDB      = String(item.lokasi || '').toLowerCase().trim();
    const lokasiPilihan = String(tempatTes   || '').toLowerCase().trim();
    const statusDB      = String(item.status || '').toLowerCase().trim();
    return lokasiDB === lokasiPilihan && statusDB === 'tersedia';
  });

  const handleNext = () => {
    if (!selectedJadwal) {
      Alert.alert('Pilih Jadwal', 'Silakan pilih salah satu jadwal yang tersedia.');
      return;
    }
    router.push({
      pathname: '/form-pendaftaran',
      params: {
        id_jadwal:      selectedJadwal.id_jadwal,
        tanggal:        selectedJadwal.tanggal,
        waktu:          selectedJadwal.waktu,
        is_luar_subang: isLuarSubang ? '1' : '0',
        nama_kota:      tempatTes === 'Home Visit' && !isLuarSubang
                          ? selectedTransport.label
                          : '',
        biaya:          getBiaya().toString(),
      },
    });
  };

  const lokasiOptions = ['Home Visit', 'Kantor Subang'];

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

        {/* === Selector: Lokasi Tes === */}
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
                  setWilayahHomeVisit('dalam');
                  setDropdownOpen(false);
                }}
                activeOpacity={0.7}
              >
                <Ionicons
                  name={val === 'Home Visit' ? 'home-outline' : 'business-outline'}
                  size={14}
                  color={tempatTes === val ? '#fff' : '#546e7a'}
                />
                <Text style={[styles.locationChipText, tempatTes === val && styles.locationChipTextActive]}>
                  {val}
                </Text>
              </TouchableOpacity>
            ))}
          </View>

          {/* Wilayah — hanya muncul jika Home Visit dipilih */}
          {tempatTes === 'Home Visit' && (
            <View style={styles.wilayahBox}>
              <Text style={styles.wilayahLabel}>
                <Ionicons name="map-outline" size={12} color="#546e7a" /> Wilayah Home Visit
              </Text>
              <View style={styles.wilayahOptions}>

                {/* Dalam Subang */}
                <TouchableOpacity
                  style={[styles.wilayahChip, wilayahHomeVisit === 'dalam' && styles.wilayahChipActive]}
                  onPress={() => { setWilayahHomeVisit('dalam'); setDropdownOpen(false); }}
                  activeOpacity={0.7}
                >
                  <Text style={[styles.wilayahChipText, wilayahHomeVisit === 'dalam' && styles.wilayahChipTextActive]}>
                    Dalam Subang
                  </Text>
                  <Text style={[styles.wilayahHarga, wilayahHomeVisit === 'dalam' && styles.wilayahHargaActive]}>
                    + {formatRupiah(selectedTransport.biaya)} transport
                  </Text>
                </TouchableOpacity>

                {/* Luar Subang */}
                <TouchableOpacity
                  style={[styles.wilayahChip, wilayahHomeVisit === 'luar' && styles.wilayahChipActive]}
                  onPress={() => { setWilayahHomeVisit('luar'); setDropdownOpen(false); }}
                  activeOpacity={0.7}
                >
                  <Text style={[styles.wilayahChipText, wilayahHomeVisit === 'luar' && styles.wilayahChipTextActive]}>
                    Luar Subang
                  </Text>
                  <Text style={[styles.wilayahHarga, wilayahHomeVisit === 'luar' && styles.wilayahHargaActive]}>
                    + Rp 75.000 transport
                  </Text>
                </TouchableOpacity>
              </View>

              {/* Dropdown transport — hanya muncul jika Dalam Subang */}
              {wilayahHomeVisit === 'dalam' && (
                <View style={styles.kotaBox}>
                  <Text style={styles.kotaLabel}>
                    <Ionicons name="car-outline" size={12} color="#546e7a" /> Pilih Area Transport
                  </Text>

                  <TouchableOpacity
                    style={styles.dropdownTrigger}
                    onPress={() => setDropdownOpen(!dropdownOpen)}
                    activeOpacity={0.8}
                  >
                    <View style={styles.dropdownTriggerLeft}>
                      <Ionicons name="location-outline" size={15} color="#00AA5B" />
                      <Text style={styles.dropdownTriggerText}>{selectedTransport.label}</Text>
                    </View>
                    <View style={styles.dropdownTriggerRight}>
                      <Text style={styles.dropdownTriggerHarga}>
                        + {formatRupiah(selectedTransport.biaya)}
                      </Text>
                      <Ionicons
                        name={dropdownOpen ? 'chevron-up' : 'chevron-down'}
                        size={16}
                        color="#546e7a"
                      />
                    </View>
                  </TouchableOpacity>

                  {dropdownOpen && (
                    <View style={styles.dropdownList}>
                      {TRANSPORT_DALAM_SUBANG.map((item, index) => {
                        const isActive = selectedTransport.label === item.label;
                        return (
                          <TouchableOpacity
                            key={item.label}
                            style={[
                              styles.dropdownItem,
                              isActive && styles.dropdownItemActive,
                              index < TRANSPORT_DALAM_SUBANG.length - 1 && styles.dropdownItemBorder,
                            ]}
                            onPress={() => {
                              setSelectedTransport(item);
                              setDropdownOpen(false);
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

              {/* Keterangan biaya luar subang */}
              {wilayahHomeVisit === 'luar' && (
                <View style={styles.luarSubangInfo}>
                  <Ionicons name="information-circle-outline" size={15} color="#0288d1" />
                  <Text style={styles.luarSubangInfoText}>
                    Biaya transport luar Subang sebesar{' '}
                    <Text style={{ fontWeight: '800', color: '#01579b' }}>Rp 75.000</Text>{' '}
                    berlaku untuk semua wilayah di luar Kabupaten Subang.
                  </Text>
                </View>
              )}

              {/* Info biaya total */}
              <View style={styles.hargaInfoRow}>
                <Ionicons name="pricetag-outline" size={13} color="#00AA5B" />
                <View style={{ flex: 1 }}>
                  {tempatTes === 'Home Visit' ? (
                    <>
                      <Text style={styles.hargaInfoText}>
                        Biaya Tes:{' '}
                        <Text style={{ color: '#546e7a', fontWeight: '700' }}>
                          {formatRupiah(BIAYA_TES)}
                        </Text>
                        {'  +  '}
                        Transport:{' '}
                        <Text style={{ color: '#546e7a', fontWeight: '700' }}>
                          {formatRupiah(getTransportBiaya())}
                        </Text>
                      </Text>
                      <Text style={styles.hargaInfoTotal}>
                        Total: {formatRupiah(getBiaya())}
                      </Text>
                    </>
                  ) : (
                    <Text style={styles.hargaInfoText}>
                      Biaya:{' '}
                      <Text style={styles.hargaInfoNominal}>{formatRupiah(getBiaya())}</Text>
                    </Text>
                  )}
                  <Text style={styles.hargaInfoNote}>
                    * Belum termasuk biaya admin antar bank
                  </Text>
                </View>
              </View>
            </View>
          )}

          {/* Info harga untuk Kantor Subang */}
          {tempatTes !== 'Home Visit' && (
            <View style={styles.hargaInfoRow}>
              <Ionicons name="pricetag-outline" size={13} color="#00AA5B" />
              <View style={{ flex: 1 }}>
                <Text style={styles.hargaInfoText}>
                  Biaya:{' '}
                  <Text style={styles.hargaInfoNominal}>{formatRupiah(getBiaya())}</Text>
                </Text>
                <Text style={styles.hargaInfoNote}>
                  * Belum termasuk biaya admin antar bank
                </Text>
              </View>
            </View>
          )}
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
    width: 38, height: 38, borderRadius: 19,
    backgroundColor: 'rgba(255,255,255,0.2)',
    justifyContent: 'center', alignItems: 'center',
  },
  topBarCenter: { flex: 1, alignItems: 'center' },
  topBarTitle: { fontSize: 18, fontWeight: '800', color: '#fff' },
  topBarSub: { fontSize: 12, color: 'rgba(255,255,255,0.8)', marginTop: 2 },

  content: { padding: 16, paddingBottom: 48 },

  infoBanner: {
    flexDirection: 'row', alignItems: 'center',
    backgroundColor: '#e1f5fe', borderRadius: 12,
    paddingHorizontal: 14, paddingVertical: 10,
    marginBottom: 16, gap: 8,
    borderLeftWidth: 3, borderLeftColor: '#0288d1',
  },
  infoBannerText: { fontSize: 12, color: '#01579b', fontWeight: '600', flex: 1 },

  locationCard: {
    backgroundColor: '#fff', borderRadius: 16,
    padding: 16, marginBottom: 14,
    borderWidth: 1, borderColor: '#e8f5e9',
    shadowColor: '#00AA5B', shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05, shadowRadius: 6, elevation: 2,
  },
  locationLabel: {
    fontSize: 11, fontWeight: '700', color: '#546e7a',
    textTransform: 'uppercase', letterSpacing: 0.6, marginBottom: 12,
  },
  locationOptions: { flexDirection: 'row', gap: 10 },
  locationChip: {
    flex: 1, flexDirection: 'row', alignItems: 'center',
    justifyContent: 'center', gap: 6, paddingVertical: 11,
    borderRadius: 12, backgroundColor: '#f5faf7',
    borderWidth: 1.5, borderColor: '#e0f2ec',
  },
  locationChipActive: {
    backgroundColor: '#00AA5B', borderColor: '#00AA5B',
    shadowColor: '#00AA5B', shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.25, shadowRadius: 6, elevation: 4,
  },
  locationChipText: { fontSize: 13, fontWeight: '700', color: '#546e7a' },
  locationChipTextActive: { color: '#fff' },

  // Wilayah Home Visit
  wilayahBox: {
    marginTop: 14, paddingTop: 14,
    borderTopWidth: 1, borderTopColor: '#e8f5e9',
  },
  wilayahLabel: {
    fontSize: 11, fontWeight: '700', color: '#546e7a',
    textTransform: 'uppercase', letterSpacing: 0.6, marginBottom: 10,
  },
  wilayahOptions: { flexDirection: 'row', gap: 10 },
  wilayahChip: {
    flex: 1, alignItems: 'center', paddingVertical: 10,
    borderRadius: 12, backgroundColor: '#f5faf7',
    borderWidth: 1.5, borderColor: '#e0f2ec',
  },
  wilayahChipActive: { backgroundColor: '#00AA5B', borderColor: '#00AA5B' },
  wilayahChipText: { fontSize: 12, fontWeight: '700', color: '#546e7a', marginBottom: 2 },
  wilayahChipTextActive: { color: '#fff' },
  wilayahHarga: { fontSize: 11, fontWeight: '600', color: '#90a4ae' },
  wilayahHargaActive: { color: 'rgba(255,255,255,0.85)' },

  // Dropdown Transport Dalam Subang
  kotaBox: {
    marginTop: 12, paddingTop: 12,
    borderTopWidth: 1, borderTopColor: '#e8f5e9',
  },
  kotaLabel: {
    fontSize: 11, fontWeight: '700', color: '#546e7a',
    textTransform: 'uppercase', letterSpacing: 0.6, marginBottom: 8,
  },
  dropdownTrigger: {
    flexDirection: 'row', alignItems: 'center',
    justifyContent: 'space-between',
    backgroundColor: '#f5faf7', borderRadius: 12,
    borderWidth: 1.5, borderColor: '#a5d6a7',
    paddingHorizontal: 14, paddingVertical: 12,
  },
  dropdownTriggerLeft: { flexDirection: 'row', alignItems: 'center', gap: 8 },
  dropdownTriggerText: { fontSize: 14, fontWeight: '700', color: '#1a1a2e' },
  dropdownTriggerRight: { flexDirection: 'row', alignItems: 'center', gap: 8 },
  dropdownTriggerHarga: { fontSize: 13, fontWeight: '800', color: '#00AA5B' },

  dropdownList: {
    marginTop: 6, backgroundColor: '#fff',
    borderRadius: 12, borderWidth: 1.5, borderColor: '#e0f2ec',
    overflow: 'hidden',
    shadowColor: '#00AA5B', shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.08, shadowRadius: 8, elevation: 3,
  },
  dropdownItem: {
    flexDirection: 'row', alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 14, paddingVertical: 13,
    backgroundColor: '#fff',
  },
  dropdownItemActive: { backgroundColor: '#f0faf5' },
  dropdownItemBorder: { borderBottomWidth: 1, borderBottomColor: '#f0f4f0' },
  dropdownItemLeft: { flexDirection: 'row', alignItems: 'center', gap: 10 },
  dropdownItemText: { fontSize: 13, fontWeight: '600', color: '#37474f' },
  dropdownItemTextActive: { color: '#00AA5B', fontWeight: '800' },
  dropdownItemHarga: { fontSize: 12, fontWeight: '700', color: '#90a4ae' },
  dropdownItemHargaActive: { color: '#00AA5B' },

  // Info luar subang
  luarSubangInfo: {
    flexDirection: 'row', alignItems: 'flex-start', gap: 8,
    marginTop: 12, padding: 12,
    backgroundColor: '#e1f5fe', borderRadius: 10,
    borderWidth: 1, borderColor: '#b3e5fc',
  },
  luarSubangInfoText: {
    flex: 1, fontSize: 12, color: '#01579b', lineHeight: 17,
  },

  // Info harga
  hargaInfoRow: {
    flexDirection: 'row', alignItems: 'flex-start', gap: 6,
    marginTop: 12, paddingTop: 12,
    borderTopWidth: 1, borderTopColor: '#e8f5e9',
  },
  hargaInfoText: { fontSize: 13, color: '#546e7a', fontWeight: '600' },
  hargaInfoNominal: { color: '#00AA5B', fontWeight: '800' },
  hargaInfoTotal: {
    fontSize: 14, fontWeight: '800', color: '#00AA5B', marginTop: 4,
  },
  hargaInfoNote: {
    fontSize: 10, color: '#90a4ae',
    fontStyle: 'italic', marginTop: 4,
  },

  tableCard: {
    backgroundColor: '#fff', borderRadius: 16,
    overflow: 'hidden', borderWidth: 1, borderColor: '#e8f5e9',
    shadowColor: '#00AA5B', shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05, shadowRadius: 6, elevation: 2, marginBottom: 14,
  },
  tableCardHeader: {
    flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center',
    padding: 14, borderBottomWidth: 1, borderColor: '#e8f5e9', backgroundColor: '#f5faf7',
  },
  tableCardHeaderLeft: { flexDirection: 'row', alignItems: 'center', gap: 6 },
  tableCardTitle: { fontSize: 13, fontWeight: '800', color: '#1a1a2e' },
  countBadge: { backgroundColor: '#e8f5e9', borderRadius: 12, paddingHorizontal: 10, paddingVertical: 3 },
  countBadgeText: { fontSize: 11, color: '#00AA5B', fontWeight: '800' },

  rowHeader: {
    flexDirection: 'row', paddingHorizontal: 14, paddingVertical: 10,
    borderBottomWidth: 1, borderColor: '#f5faf7', backgroundColor: '#fafffe',
  },
  colHeader: { color: '#90a4ae', fontSize: 11, fontWeight: '700', textTransform: 'uppercase' },
  rowData: {
    flexDirection: 'row', paddingHorizontal: 14, paddingVertical: 14,
    borderBottomWidth: 1, borderColor: '#f5faf7', alignItems: 'center', position: 'relative',
  },
  selectedRow: { backgroundColor: '#e8f5e9', borderColor: '#00AA5B' },
  selectedIndicator: {
    position: 'absolute', left: 0, top: 0, bottom: 0,
    width: 3, backgroundColor: '#00AA5B', borderRadius: 2,
  },
  col: { flex: 2, fontSize: 13, color: '#37474f', fontWeight: '600' },
  colMid: { flex: 2, fontSize: 13, color: '#37474f', fontWeight: '600' },
  colEnd: { flex: 1.5, alignItems: 'flex-end' },
  selectedText: { color: '#00AA5B', fontWeight: '700' },
  statusBadge: {
    flexDirection: 'row', alignItems: 'center',
    backgroundColor: '#e8f5e9', borderRadius: 12,
    paddingHorizontal: 8, paddingVertical: 4, gap: 4,
  },
  statusDot: { width: 5, height: 5, borderRadius: 3, backgroundColor: '#00AA5B' },
  statusText: { fontSize: 10, color: '#00AA5B', fontWeight: '800', textTransform: 'uppercase' },

  loadingBox: { padding: 40, alignItems: 'center', gap: 10 },
  loadingText: { fontSize: 12, color: '#90a4ae' },
  emptyState: { padding: 36, alignItems: 'center' },
  emptyIconBg: {
    width: 70, height: 70, borderRadius: 35,
    backgroundColor: '#f5faf7',
    justifyContent: 'center', alignItems: 'center', marginBottom: 12,
  },
  emptyTitle: { fontSize: 15, fontWeight: '700', color: '#546e7a', marginBottom: 4 },
  emptyText: { fontSize: 12, color: '#90a4ae', textAlign: 'center' },

  selectedInfo: {
    flexDirection: 'row', alignItems: 'center', gap: 8,
    backgroundColor: '#e8f5e9', borderRadius: 12, padding: 12,
    marginBottom: 16, borderWidth: 1, borderColor: '#a5d6a7',
  },
  selectedInfoText: { fontSize: 13, color: '#2e7d32', flex: 1 },

  footerBtns: { flexDirection: 'row', gap: 12, marginTop: 4, marginBottom: 16 },
  btnBack: {
    flex: 1, backgroundColor: '#fff', paddingVertical: 14,
    borderRadius: 12, alignItems: 'center', flexDirection: 'row',
    justifyContent: 'center', gap: 6, borderWidth: 1.5, borderColor: '#e0f2ec',
  },
  btnBackText: { color: '#546e7a', fontWeight: '700', fontSize: 14 },
  btnNext: {
    flex: 1, backgroundColor: '#00AA5B', paddingVertical: 14,
    borderRadius: 12, alignItems: 'center', flexDirection: 'row',
    justifyContent: 'center', gap: 6,
    shadowColor: '#00AA5B', shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3, shadowRadius: 8, elevation: 4,
  },
  btnNextDisabled: { backgroundColor: '#e0e0e0', shadowOpacity: 0, elevation: 0 },
  btnNextText: { color: '#fff', fontWeight: '800', fontSize: 14 },
  btnNextTextDisabled: { color: '#b0bec5' },
});