import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet, SafeAreaView, TouchableOpacity, ScrollView, ActivityIndicator, Alert } from 'react-native';
import { useRouter } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';
import axios from 'axios';
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
      const savedName = await AsyncStorage.getItem('user_name');
      if (savedName) setUserName(savedName);
      fetchJadwal();
    };
    loadData();
  }, []);

  const fetchJadwal = async () => {
    try {
      setLoading(true);
      const response = await axios.get('http://192.168.100.117:8000/api/jadwal-tersedia');
      setJadwalData(response.data);
    } catch (error) {
      Alert.alert("Error", "Gagal mengambil data jadwal.");
    } finally { setLoading(false); }
  };

  const filteredJadwal = jadwalData.filter(item => 
    item.lokasi.toLowerCase().trim() === tempatTes.toLowerCase().trim() && item.status === 'Tersedia'
  );

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <TouchableOpacity style={styles.menuIcon} onPress={() => router.back()}>
          <Ionicons name="arrow-back-outline" size={28} color="#1e293b" />
        </TouchableOpacity>
        <View style={styles.profileSection}>
          <View style={styles.placeholderImg} />
          <View>
            <Text style={styles.welcomeText}>Halo, {userName}!</Text>
            <Text style={styles.subText}>Selamat datang di Sistem Tes STIFIn</Text>
          </View>
        </View>
      </View>

      <ScrollView contentContainerStyle={styles.content}>
        <View style={styles.selectorSection}>
          <View style={styles.labelBox}><Text style={styles.bold}>Tempat Tes :</Text></View>
          <View style={styles.dropdownContainer}>
            <TouchableOpacity style={styles.dropdownTrigger} onPress={() => setShowDropdown(!showDropdown)}>
              <Text>{tempatTes}</Text>
              <Ionicons name="chevron-down" size={18} color="#0891b2" />
            </TouchableOpacity>
            {showDropdown && (
              <View style={styles.dropdownMenu}>
                {['Home Visit', 'Kantor Cabang'].map(val => (
                  <TouchableOpacity key={val} onPress={() => {setTempatTes(val); setShowDropdown(false); setSelectedJadwal(null)}} style={styles.dropItem}>
                    <Text>{val}</Text>
                  </TouchableOpacity>
                ))}
              </View>
            )}
          </View>
        </View>

        <View style={styles.tableCard}>
          <View style={styles.tableHeader}><Text style={styles.tableHeaderText}>Jadwal Tes</Text></View>
          <View style={styles.rowHeader}>
            <Text style={[styles.col, styles.bold]}>Tanggal</Text>
            <Text style={[styles.col, styles.bold]}>Waktu</Text>
            <Text style={[styles.col, styles.bold]}>Status</Text>
          </View>
          {loading ? <ActivityIndicator size="large" color="#3b82f6" style={{margin: 20}} /> : 
            filteredJadwal.map(item => (
              <TouchableOpacity key={item.id_jadwal} onPress={() => setSelectedJadwal(item)} style={[styles.rowData, selectedJadwal?.id_jadwal === item.id_jadwal && styles.selectedRow]}>
                <Text style={styles.col}>{item.tanggal}</Text>
                <Text style={styles.col}>{item.waktu}</Text>
                <View style={styles.col}><View style={styles.statusBadge}><Text style={styles.statusText}>{item.status}</Text></View></View>
              </TouchableOpacity>
            ))
          }
        </View>
        <View style={styles.footerBtns}>
          <TouchableOpacity style={styles.btnNav} onPress={() => router.back()}><Text>Kembali</Text></TouchableOpacity>
          <TouchableOpacity 
            style={[styles.btnNav, {backgroundColor: '#cbd5e1'}]} 
            onPress={() => {
              if(!selectedJadwal) return Alert.alert("Pilih Jadwal", "Klik salah satu jadwal.");
              router.push({ pathname: '/form-pendaftaran', params: { id_jadwal: selectedJadwal.id_jadwal, tanggal: selectedJadwal.tanggal, waktu: selectedJadwal.waktu }});
            }}
          >
            <Text>Berikutnya</Text>
          </TouchableOpacity>
        </View>
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#fff' },
  header: { padding: 20, borderBottomWidth: 1, borderColor: '#ccc' },
  menuIcon: { marginBottom: 10 },
  profileSection: { flexDirection: 'row', alignItems: 'center' },
  placeholderImg: { width: 70, height: 60, backgroundColor: '#d1d5db', marginRight: 15 },
  welcomeText: { fontSize: 22, fontWeight: 'bold' },
  subText: { fontSize: 13, color: '#64748b' },
  content: { padding: 20 },
  selectorSection: { flexDirection: 'row', alignItems: 'center', marginBottom: 20, zIndex: 100 },
  labelBox: { backgroundColor: '#e5e7eb', padding: 10, marginRight: 10 },
  dropdownContainer: { flex: 1 },
  dropdownTrigger: { borderWidth: 1, borderColor: '#22d3ee', padding: 8, flexDirection: 'row', justifyContent: 'space-between' },
  dropdownMenu: { position: 'absolute', top: 40, left: 0, right: 0, backgroundColor: '#fff', borderWidth: 1, zIndex: 1000 },
  dropItem: { padding: 10, borderBottomWidth: 1, borderColor: '#eee' },
  tableCard: { backgroundColor: '#d1d5db', borderRadius: 5, overflow: 'hidden' },
  tableHeader: { backgroundColor: '#9ca3af', padding: 10 },
  tableHeaderText: { fontWeight: 'bold' },
  rowHeader: { flexDirection: 'row', backgroundColor: '#fff', padding: 10 },
  rowData: { flexDirection: 'row', padding: 12, backgroundColor: '#fff', borderBottomWidth: 1, borderColor: '#eee' },
  selectedRow: { backgroundColor: '#e0f2fe' },
  col: { flex: 1, fontSize: 12 },
  bold: { fontWeight: 'bold' },
  statusBadge: { backgroundColor: '#e5e7eb', borderRadius: 10, padding: 4, alignItems: 'center' },
  statusText: { fontSize: 10 },
  footerBtns: { flexDirection: 'row', justifyContent: 'flex-end', marginTop: 20, gap: 10 },
  btnNav: { backgroundColor: '#e5e7eb', padding: 10, borderRadius: 15, width: 100, alignItems: 'center' }
});