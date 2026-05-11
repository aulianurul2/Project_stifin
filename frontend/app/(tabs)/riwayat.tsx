import React, { useState, useCallback } from 'react';
import { View, Text, StyleSheet, SafeAreaView, ScrollView, ActivityIndicator, TouchableOpacity } from 'react-native';
import { useFocusEffect } from 'expo-router';
import AsyncStorage from '@react-native-async-storage/async-storage';
import axiosInstance from '@/src/api/axiosConfig';

export default function RiwayatJadwal() {
  const [userName, setUserName] = useState("User");
  const [riwayat, setRiwayat] = useState([]);
  const [loading, setLoading] = useState(true);

  useFocusEffect(
    useCallback(() => {
      const loadData = async () => {
        const savedName = await AsyncStorage.getItem('user_name');
        if (savedName) setUserName(savedName);
        fetchRiwayat();
      };
      loadData();
    }, [])
  );

  const fetchRiwayat = async () => {
    try {
      setLoading(true);
      // Ganti IP sesuai laptop kamu
      const response = await axiosInstance.get('/riwayat-pendaftaran');
      setRiwayat(response.data);
    } catch (error) {
      console.error("Gagal ambil riwayat", error);
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <View style={styles.profileSection}>
          <View style={styles.placeholderImg} />
          <View>
            <Text style={styles.welcomeText}>Halo, {userName}!</Text>
            <Text style={styles.subText}>Selamat datang di Sistem Tes STIFIn</Text>
          </View>
        </View>
      </View>

      <View style={styles.content}>
        <View style={styles.tableCard}>
          <View style={styles.tableHeader}>
            <Text style={styles.tableHeaderText}>Riwayat Jadwal Tes</Text>
          </View>
          
          <View style={styles.rowHeader}>
            <Text style={[styles.col, styles.bold]}>Tanggal</Text>
            <Text style={[styles.col, styles.bold]}>Waktu</Text>
            <Text style={[styles.col, styles.bold]}>Status</Text>
          </View>

          {loading ? (
            <ActivityIndicator size="large" color="#3b82f6" style={{ margin: 20 }} />
          ) : riwayat.length === 0 ? (
            <View style={styles.emptyBox}>
              <Text style={styles.emptyText}>Jadwal belum tersedia.</Text>
            </View>
          ) : (
            riwayat.map((item: any, index) => (
              <View key={index} style={styles.rowData}>
                <Text style={styles.col}>{item.tanggal}</Text>
                <Text style={styles.col}>{item.waktu}</Text>
                <View style={styles.col}>
                  <View style={styles.statusBadge}>
                    <Text style={styles.statusText}>{item.status}</Text>
                  </View>
                </View>
              </View>
            ))
          )}
        </View>
      </View>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#fff' },
  header: { padding: 20, borderBottomWidth: 1, borderColor: '#eee' },
  profileSection: { flexDirection: 'row', alignItems: 'center' },
  placeholderImg: { width: 60, height: 60, backgroundColor: '#cbd5e1', marginRight: 15 },
  welcomeText: { fontSize: 20, fontWeight: 'bold' },
  subText: { fontSize: 13, color: '#64748b' },
  content: { padding: 20 },
  tableCard: { backgroundColor: '#cbd5e1', borderRadius: 2, overflow: 'hidden', minHeight: 200 },
  tableHeader: { backgroundColor: '#94a3b8', padding: 12 },
  tableHeaderText: { fontSize: 16, fontWeight: 'bold' },
  rowHeader: { flexDirection: 'row', backgroundColor: '#fff', padding: 10, borderBottomWidth: 1, borderColor: '#eee' },
  rowData: { flexDirection: 'row', padding: 12, backgroundColor: '#94a3b8', marginBottom: 1 },
  col: { flex: 1, fontSize: 12 },
  bold: { fontWeight: 'bold' },
  statusBadge: { backgroundColor: '#e2e8f0', borderRadius: 10, paddingVertical: 2, paddingHorizontal: 8, alignSelf: 'flex-start' },
  statusText: { fontSize: 10, color: '#1e293b' },
  emptyBox: { padding: 40, alignItems: 'center' },
  emptyText: { color: '#64748b' }
});