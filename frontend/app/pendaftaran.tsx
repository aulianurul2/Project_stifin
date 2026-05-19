import React, { useState, useEffect } from 'react';
import { 
  View, 
  Text, 
  StyleSheet, 
  SafeAreaView, 
  TouchableOpacity, 
  ScrollView, 
  ActivityIndicator, 
  Alert 
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
  const [userName, setUserName] = useState("User");
  const [tempatTes, setTempatTes] = useState('Kantor Cabang');
  const [showDropdown, setShowDropdown] = useState(false);
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
        console.error("Error loading initial data:", error);
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
      Alert.alert("Error", "Gagal mengambil data jadwal dari server.");
    } finally { 
      setLoading(false); 
    }
  };

  const filteredJadwal = jadwalData.filter(item => {
    const lokasiDB = String(item.lokasi || "").toLowerCase().trim();
    const lokasiPilihan = String(tempatTes || "").toLowerCase().trim();
    const statusDB = String(item.status || "").toLowerCase().trim();
    return lokasiDB === lokasiPilihan && statusDB === 'tersedia';
  });

  const handleNext = () => {
    if (!selectedJadwal) {
      Alert.alert("Pilih Jadwal", "Silakan pilih salah satu jadwal yang tersedia.");
      return;
    }
    
    // Berpindah ke form pendaftaran dengan membawa parameter jadwal
    router.push({
      pathname: '/form-pendaftaran',
      params: { 
        id_jadwal: selectedJadwal.id_jadwal, 
        tanggal: selectedJadwal.tanggal, 
        waktu: selectedJadwal.waktu 
      }
    });
  };

  return (
    <SafeAreaView style={styles.container}>
      {/* Header Section */}
      <View style={styles.header}>
        <TouchableOpacity style={styles.menuIcon} onPress={() => router.back()}>
          <Ionicons name="arrow-back-outline" size={28} color="#1e293b" />
        </TouchableOpacity>
        <View style={styles.profileSection}>
          <View style={styles.avatarPlaceholder}>
             <Ionicons name="person" size={30} color="#94a3b8" />
          </View>
          <View>
            <Text style={styles.welcomeText}>Halo, {userName}!</Text>
            <Text style={styles.subText}>Silakan pilih lokasi dan waktu tes Anda</Text>
          </View>
        </View>
      </View>

      <ScrollView contentContainerStyle={styles.content} showsVerticalScrollIndicator={false}>
        
        {/* Selector Section (Dropdown) */}
        <View style={styles.selectorSection}>
          <View style={styles.labelBox}>
            <Text style={styles.bold}>Tempat Tes :</Text>
          </View>
          <View style={styles.dropdownContainer}>
            <TouchableOpacity 
              style={styles.dropdownTrigger} 
              onPress={() => setShowDropdown(!showDropdown)}
              activeOpacity={0.7}
            >
              <Text style={styles.dropdownValue}>{tempatTes}</Text>
              <Ionicons name={showDropdown ? "chevron-up" : "chevron-down"} size={18} color="#0891b2" />
            </TouchableOpacity>
            
            {showDropdown && (
              <View style={styles.dropdownMenu}>
                {['Home Visit', 'Kantor Cabang'].map((val) => (
                  <TouchableOpacity 
                    key={val} 
                    onPress={() => {
                      setTempatTes(val); 
                      setShowDropdown(false); 
                      setSelectedJadwal(null);
                    }} 
                    style={styles.dropItem}
                  >
                    <Text style={val === tempatTes ? styles.activeDropText : {}}>{val}</Text>
                  </TouchableOpacity>
                ))}
              </View>
            )}
          </View>
        </View>

        {/* Jadwal Table Section */}
        <View style={styles.tableCard}>
          <View style={styles.tableHeader}>
            <Text style={styles.tableHeaderText}>Pilihan Jadwal Tersedia</Text>
          </View>
          
          <View style={styles.rowHeader}>
            <Text style={[styles.col, styles.bold]}>Tanggal</Text>
            <Text style={[styles.col, styles.bold]}>Waktu</Text>
            <Text style={[styles.col, styles.bold, { textAlign: 'center' }]}>Status</Text>
          </View>

          {loading ? (
            <ActivityIndicator size="large" color="#3b82f6" style={{ margin: 30 }} />
          ) : filteredJadwal.length > 0 ? (
            filteredJadwal.map((item) => (
              <TouchableOpacity 
                key={item.id_jadwal} 
                onPress={() => setSelectedJadwal(item)} 
                style={[
                  styles.rowData, 
                  selectedJadwal?.id_jadwal === item.id_jadwal && styles.selectedRow
                ]}
              >
                <Text style={styles.col}>{item.tanggal}</Text>
                <Text style={styles.col}>{item.waktu} WIB</Text>
                <View style={styles.col}>
                  <View style={styles.statusBadge}>
                    <Text style={styles.statusText}>{item.status.toUpperCase()}</Text>
                  </View>
                </View>
              </TouchableOpacity>
            ))
          ) : (
            <View style={styles.emptyState}>
              <Ionicons name="calendar-outline" size={40} color="#cbd5e1" />
              <Text style={styles.emptyText}>Tidak ada jadwal tersedia untuk lokasi ini.</Text>
            </View>
          )}
        </View>

        {/* Footer Navigation */}
        <View style={styles.footerBtns}>
          <TouchableOpacity style={styles.btnBack} onPress={() => router.back()}>
            <Text style={styles.btnBackText}>Kembali</Text>
          </TouchableOpacity>
          
          <TouchableOpacity 
            style={[
              styles.btnNext, 
              { backgroundColor: selectedJadwal ? '#1e40af' : '#cbd5e1' }
            ]} 
            onPress={handleNext}
            disabled={!selectedJadwal}
          >
            <Text style={[styles.btnNextText, { color: selectedJadwal ? '#fff' : '#64748b' }]}>
              Berikutnya
            </Text>
          </TouchableOpacity>
        </View>

      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f8fafc' },
  header: { 
    padding: 20, 
    backgroundColor: '#fff', 
    borderBottomWidth: 1, 
    borderColor: '#e2e8f0',
    paddingTop: 50
  },
  menuIcon: { marginBottom: 15 },
  profileSection: { flexDirection: 'row', alignItems: 'center' },
  avatarPlaceholder: { 
    width: 60, 
    height: 60, 
    backgroundColor: '#f1f5f9', 
    marginRight: 15, 
    borderRadius: 15,
    justifyContent: 'center',
    alignItems: 'center',
    borderWidth: 1,
    borderColor: '#e2e8f0'
  },
  welcomeText: { fontSize: 20, fontWeight: 'bold', color: '#1e293b' },
  subText: { fontSize: 13, color: '#64748b', marginTop: 2 },
  content: { padding: 20 },
  selectorSection: { 
    flexDirection: 'row', 
    alignItems: 'center', 
    marginBottom: 20, 
    zIndex: 1000 // Sangat penting untuk Android
  },
  labelBox: { 
    backgroundColor: '#e2e8f0', 
    paddingVertical: 10, 
    paddingHorizontal: 12, 
    borderRadius: 8,
    marginRight: 10 
  },
  dropdownContainer: { flex: 1, position: 'relative' },
  dropdownTrigger: { 
    backgroundColor: '#fff',
    borderWidth: 1, 
    borderColor: '#22d3ee', 
    borderRadius: 8,
    padding: 10, 
    flexDirection: 'row', 
    justifyContent: 'space-between',
    alignItems: 'center'
  },
  dropdownValue: { fontSize: 14, color: '#1e293b', fontWeight: '600' },
  dropdownMenu: { 
    position: 'absolute', 
    top: 45, 
    left: 0, 
    right: 0, 
    backgroundColor: '#fff', 
    borderRadius: 8,
    borderWidth: 1, 
    borderColor: '#e2e8f0',
    elevation: 5,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    zIndex: 2000 
  },
  dropItem: { padding: 12, borderBottomWidth: 1, borderColor: '#f1f5f9' },
  activeDropText: { color: '#0891b2', fontWeight: 'bold' },
  tableCard: { 
    backgroundColor: '#fff', 
    borderRadius: 12, 
    overflow: 'hidden', 
    borderWidth: 1, 
    borderColor: '#e2e8f0',
    elevation: 2
  },
  tableHeader: { backgroundColor: '#f1f5f9', padding: 15, borderBottomWidth: 1, borderColor: '#e2e8f0' },
  tableHeaderText: { fontWeight: '800', color: '#475569', fontSize: 14 },
  rowHeader: { flexDirection: 'row', backgroundColor: '#fff', padding: 12, borderBottomWidth: 2, borderColor: '#f1f5f9' },
  rowData: { flexDirection: 'row', padding: 15, backgroundColor: '#fff', borderBottomWidth: 1, borderColor: '#f1f5f9', alignItems: 'center' },
  selectedRow: { backgroundColor: '#f0f9ff', borderColor: '#3b82f6' },
  col: { flex: 1, fontSize: 13, color: '#334155' },
  bold: { fontWeight: '700', color: '#1e293b' },
  statusBadge: { backgroundColor: '#dcfce7', borderRadius: 20, paddingVertical: 4, paddingHorizontal: 8, alignItems: 'center' },
  statusText: { fontSize: 10, color: '#16a34a', fontWeight: 'bold' },
  emptyState: { padding: 40, alignItems: 'center' },
  emptyText: { marginTop: 10, color: '#94a3b8', textAlign: 'center', fontSize: 14 },
  footerBtns: { flexDirection: 'row', justifyContent: 'space-between', marginTop: 30, marginBottom: 50 },
  btnBack: { 
    backgroundColor: '#fff', 
    paddingVertical: 12, 
    paddingHorizontal: 20, 
    borderRadius: 10, 
    width: '45%', 
    alignItems: 'center',
    borderWidth: 1,
    borderColor: '#e2e8f0'
  },
  btnBackText: { color: '#475569', fontWeight: 'bold' },
  btnNext: { 
    paddingVertical: 12, 
    paddingHorizontal: 20, 
    borderRadius: 10, 
    width: '45%', 
    alignItems: 'center',
    elevation: 3
  },
  btnNextText: { fontWeight: 'bold' }
});