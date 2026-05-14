import React, { useState, useCallback } from 'react';
import {
  View,
  Text,
  StyleSheet,
  SafeAreaView,
  ScrollView,
  ActivityIndicator,
  TouchableOpacity,
  Alert,
  Platform
} from 'react-native';

import { useFocusEffect } from 'expo-router';
import AsyncStorage from '@react-native-async-storage/async-storage';
import axiosInstance from '@/src/api/axiosConfig';

import * as FileSystem from 'expo-file-system/legacy';
import * as Sharing from 'expo-sharing';

export default function RiwayatJadwal() {
  const [userName, setUserName] = useState("User");
  const [riwayat, setRiwayat] = useState([]);
  const [loading, setLoading] = useState(true);

  useFocusEffect(
    useCallback(() => {
      loadData();
    }, [])
  );

  const loadData = async () => {
    const savedName = await AsyncStorage.getItem('user_name');

    if (savedName) {
      setUserName(savedName);
    }

    fetchRiwayat();
  };

  const fetchRiwayat = async () => {
    try {
      setLoading(true);

      const response = await axiosInstance.get('/riwayat-pendaftaran');

      setRiwayat(response.data);

    } catch (error) {
      console.log("Gagal ambil data:", error);
    } finally {
      setLoading(false);
    }
  };

  const downloadFile = async (fileName: string) => {
    try {
      if (!fileName) {
        Alert.alert("Gagal", "File tidak tersedia");
        return;
      }

      const BASE_URL =
        process.env.EXPO_PUBLIC_API_URL?.replace('/api', '');

      const url =
        `${BASE_URL}/uploads/hasil/${encodeURIComponent(fileName)}`;

      // =====================
      // WEB / BROWSER
      // =====================
      if (Platform.OS === 'web') {
        window.open(url, '_blank');
        return;
      }

      // =====================
      // ANDROID / IOS
      // =====================
      const fileUri =
        FileSystem.cacheDirectory + fileName;

      const downloadResumable =
        FileSystem.createDownloadResumable(
          url,
          fileUri
        );

      const result =
        await downloadResumable.downloadAsync();

      if (!result) {
        Alert.alert("Gagal", "Download gagal");
        return;
      }

      await Sharing.shareAsync(result.uri);

    } catch (error) {
      console.log(error);
      Alert.alert("Error", "Tidak bisa download file");
    }
  };

  const warnaStatus = (status: string) => {
    if (status === "Selesai") return "#16a34a";
    if (status === "Menunggu") return "#eab308";
    if (status === "Proses") return "#eab308";
    if (status === "Ditolak") return "#dc2626";
    return "#2563eb";
  };

  return (
    <SafeAreaView style={styles.container}>

      {/* Header */}
      <View style={styles.header}>
        <Text style={styles.title}>
          Halo, {userName}
        </Text>

        <Text style={styles.subTitle}>
          Riwayat Tes STIFIn Anda
        </Text>
      </View>

      {/* Content */}
      <ScrollView contentContainerStyle={styles.content}>
        {loading ? (
          <ActivityIndicator
            size="large"
            color="#2563eb"
          />
        ) : riwayat.length === 0 ? (
          <Text style={styles.emptyText}>
            Belum ada riwayat tes
          </Text>
        ) : (
          riwayat.map((item: any, index) => (
            <View
              key={index}
              style={styles.card}
            >
              <Text style={styles.label}>
                Tanggal : {item.tanggal}
              </Text>

              <Text style={styles.label}>
                Jam : {item.waktu}
              </Text>

              <View
                style={[
                  styles.badge,
                  {
                    backgroundColor:
                      warnaStatus(item.status_tes || item.status)
                  }
                ]}
              >
                <Text style={styles.badgeText}>
                  {item.status_tes || item.status}
                </Text>
              </View>

              {/* Jika selesai tampil tombol */}
              {item.status_tes === "Selesai" && (
                <View style={{ marginTop: 15 }}>

                  <TouchableOpacity
                    style={styles.btnBlue}
                    onPress={() =>
                      downloadFile(item.file_hasil)
                    }
                  >
                    <Text style={styles.btnText}>
                      Download Sertifikat
                    </Text>
                  </TouchableOpacity>

                  <TouchableOpacity
                    style={styles.btnGreen}
                    onPress={() =>
                      downloadFile(item.file_detail)
                    }
                  >
                    <Text style={styles.btnText}>
                      Download Detail Tes
                    </Text>
                  </TouchableOpacity>

                </View>
              )}

            </View>
          ))
        )}
      </ScrollView>

    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f8fafc'
  },

  header: {
    padding: 20,
    paddingBottom: 10
  },

  title: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#0f172a'
  },

  subTitle: {
    fontSize: 14,
    color: '#64748b',
    marginTop: 4
  },

  content: {
    padding: 20
  },

  emptyText: {
    textAlign: 'center',
    color: '#64748b',
    marginTop: 30
  },

  card: {
    backgroundColor: '#ffffff',
    padding: 18,
    borderRadius: 16,
    marginBottom: 15,
    elevation: 3
  },

  label: {
    fontSize: 14,
    color: '#1e293b',
    marginBottom: 5
  },

  badge: {
    alignSelf: 'flex-start',
    paddingVertical: 6,
    paddingHorizontal: 12,
    borderRadius: 20,
    marginTop: 10
  },

  badgeText: {
    color: '#fff',
    fontWeight: 'bold',
    fontSize: 12
  },

  btnBlue: {
    backgroundColor: '#2563eb',
    padding: 12,
    borderRadius: 10,
    marginBottom: 10
  },

  btnGreen: {
    backgroundColor: '#16a34a',
    padding: 12,
    borderRadius: 10
  },

  btnText: {
    color: '#fff',
    textAlign: 'center',
    fontWeight: 'bold'
  }
});