import React, { useState, useCallback } from 'react';
import { View, Text, StyleSheet, SafeAreaView, ScrollView, TouchableOpacity, Image } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useRouter, useFocusEffect } from 'expo-router';
import AsyncStorage from '@react-native-async-storage/async-storage';

export default function DashboardIndex() {
  const router = useRouter();
  const [userName, setUserName] = useState("User");

  // useFocusEffect akan memanggil data setiap kali tab Dashboard dibuka
  useFocusEffect(
    useCallback(() => {
      const getSavedName = async () => {
        const savedName = await AsyncStorage.getItem('user_name');
        if (savedName) setUserName(savedName);
      };
      getSavedName();
    }, [])
  );

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <View>
          <Text style={styles.brandText}>STIFIn Mobile</Text>
          <Text style={styles.dateText}>Kamis, 07 Mei 2026</Text>
        </View>
        <TouchableOpacity style={styles.notifBtn}>
          <Ionicons name="notifications-outline" size={24} color="#1e293b" />
          <View style={styles.notifBadge} />
        </TouchableOpacity>
      </View>

      <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
        <View style={styles.welcomeCard}>
          <View style={styles.welcomeInfo}>
            <Text style={styles.welcomeLabel}>Selamat Datang,</Text>
            <Text style={styles.userName}>{userName}</Text>
            <View style={styles.roleBadge}><Text style={styles.roleText}>Klien Aktif</Text></View>
          </View>
          <Image 
            source={{ uri: `https://ui-avatars.com/api/?name=${userName}&background=3b82f6&color=fff&bold=true` }} 
            style={styles.avatar} 
          />
        </View>

        <Text style={styles.sectionTitle}>Layanan Utama</Text>
        <View style={styles.grid}>
          <MenuBox title="Daftar Tes" icon="create-outline" color="#3b82f6" onPress={() => router.push('/(tabs)/pendaftaran')} />
          <MenuBox title="Jadwal Tes" icon="calendar-outline" color="#10b981"  />
          <MenuBox title="Hasil Tes" icon="stats-chart-outline" color="#f59e0b" />
          <MenuBox title="Panduan" icon="book-outline" color="#8b5cf6" />
        </View>
      </ScrollView>
    </SafeAreaView>
  );
}

const MenuBox = ({ title, icon, color, onPress }: any) => (
  <TouchableOpacity style={styles.menuBox} onPress={onPress}>
    <View style={[styles.iconCircle, { backgroundColor: color + '15' }]}>
      <Ionicons name={icon} size={28} color={color} />
    </View>
    <Text style={styles.menuTitle}>{title}</Text>
  </TouchableOpacity>
);

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f8fafc' },
  header: { flexDirection: 'row', justifyContent: 'space-between', padding: 20, backgroundColor: '#fff', alignItems: 'center', borderBottomWidth: 1, borderBottomColor: '#f1f5f9' },
  brandText: { fontSize: 20, fontWeight: '800', color: '#1e293b' },
  dateText: { fontSize: 12, color: '#94a3b8', marginTop: 2 },
  notifBtn: { padding: 8, backgroundColor: '#f1f5f9', borderRadius: 12 },
  notifBadge: { position: 'absolute', top: 10, right: 10, width: 8, height: 8, borderRadius: 4, backgroundColor: '#ef4444', borderWidth: 2, borderColor: '#fff' },
  scrollContent: { padding: 20 },
  welcomeCard: { backgroundColor: '#1e293b', padding: 25, borderRadius: 24, flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 20 },
  welcomeInfo: { flex: 1 },
  welcomeLabel: { color: '#94a3b8', fontSize: 14 },
  userName: { color: '#fff', fontSize: 24, fontWeight: 'bold', marginVertical: 4 },
  roleBadge: { backgroundColor: 'rgba(59, 130, 246, 0.2)', paddingHorizontal: 10, paddingVertical: 4, borderRadius: 8, alignSelf: 'flex-start' },
  roleText: { color: '#3b82f6', fontSize: 11, fontWeight: '700' },
  avatar: { width: 60, height: 60, borderRadius: 20 },
  sectionTitle: { fontSize: 16, fontWeight: 'bold', color: '#1e293b', marginBottom: 15, marginLeft: 4 },
  grid: { flexDirection: 'row', flexWrap: 'wrap', justifyContent: 'space-between' },
  menuBox: { width: '47%', backgroundColor: '#fff', padding: 20, borderRadius: 24, alignItems: 'center', marginBottom: 15, borderWidth: 1, borderColor: '#f1f5f9' },
  iconCircle: { padding: 16, borderRadius: 18, marginBottom: 12 },
  menuTitle: { fontSize: 14, fontWeight: '700', color: '#334155' },
});