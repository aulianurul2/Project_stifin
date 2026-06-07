import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  SafeAreaView,
  TouchableOpacity,
  ScrollView,
  ActivityIndicator,
  Platform,
  StatusBar,
  Linking,
} from 'react-native';
import { useRouter } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';
import axiosInstance from '@/src/api/axiosConfig';

interface ProfileData {
  nama: string;
  username: string;
  no_hp: string;
  tanggal_lahir: string;
  jenis_kelamin: string;
  golongan_darah: string;
  email: string;
  alamat: string;
  institusi: string;
  sosmed: string;
  domisili: string;
}

export default function ProfileScreen() {
  const router = useRouter();
  const [fetching, setFetching]                   = useState(true);
  const [showLogoutConfirm, setShowLogoutConfirm] = useState(false);

  const [adminWa1, setAdminWa1] = useState<string>('6282127747105');
  const [adminWa2, setAdminWa2] = useState<string>('6281224595556');

  const [profile, setProfile] = useState<ProfileData>({
    nama: '', username: '', no_hp: '', tanggal_lahir: '',
    jenis_kelamin: 'L', golongan_darah: '-',
    email: '', alamat: '', institusi: '', sosmed: '', domisili: '',
  });

  useEffect(() => {
    const fetchProfile = async () => {
      try {
        const res = await axiosInstance.get('/profile');
        const u   = res.data;
        setProfile({
          nama:           u.nama           || '',
          username:       u.username       || '',
          no_hp:          u.no_hp          || '',
          tanggal_lahir:  u.tanggal_lahir  || '',
          jenis_kelamin:  u.jenis_kelamin  || 'L',
          golongan_darah: u.golongan_darah || '-',
          email:          u.email          || '',
          alamat:         u.alamat         || '',
          institusi:      u.institusi      || '',
          sosmed:         u.sosmed         || '',
          domisili:       u.domisili       || '',
        });
      } catch (e) {
        console.log('Gagal fetch profil:', e);
      } finally {
        setFetching(false);
      }
    };

    const fetchAdminContact = async () => {
      try {
        const BASE_URL = process.env.EXPO_PUBLIC_API_URL;
        const res      = await fetch(`${BASE_URL}/admin-contact`);
        const data     = await res.json();
        if (data.wa1) setAdminWa1(data.wa1);
        if (data.wa2) setAdminWa2(data.wa2);
      } catch (e) {
        console.log('Gagal fetch admin contact:', e);
      }
    };

    fetchProfile();
    fetchAdminContact();
  }, []);

  const handleLogout = async () => {
    try {
      await axiosInstance.post('/logout');
    } catch (_) {}
    router.replace('/login');
  };

  const hubungiWA = (nomor: string) => {
    const pesan = encodeURIComponent(
      'Halo Admin, saya ingin menanyakan seputar informasi jadwal dan hasil tes STIFIn. Mohon bantuannya, terima kasih.'
    );
    Linking.openURL(`https://wa.me/${nomor}?text=${pesan}`);
  };

  const toLokalFormat = (nomor: string) => {
    if (nomor.startsWith('62')) return '0' + nomor.slice(2);
    return nomor;
  };

  const inisial = profile.nama
    ? profile.nama.trim().split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase()
    : '?';

  if (fetching) {
    return (
      <View style={styles.center}>
        <ActivityIndicator size="large" color="#00AA5B" />
        <Text style={styles.loadingText}>Memuat profil...</Text>
      </View>
    );
  }

  return (
    <SafeAreaView style={styles.container}>

      {/* Top Bar */}
      <View style={styles.topBar}>
        <TouchableOpacity style={styles.backBtn} onPress={() => router.replace('/home')}>
          <Ionicons name="arrow-back" size={22} color="#fff" />
        </TouchableOpacity>
        <View style={styles.topBarCenter}>
          <Text style={styles.topBarTitle}>Profil Saya</Text>
          <Text style={styles.topBarSub}>Informasi akun Anda</Text>
        </View>
        <View style={{ width: 38 }} />
      </View>

      <ScrollView contentContainerStyle={styles.content} showsVerticalScrollIndicator={false}>

        {/* Avatar & Nama */}
        <View style={styles.avatarCard}>
          <View style={styles.avatarCircle}>
            <Text style={styles.avatarText}>{inisial}</Text>
          </View>
          <Text style={styles.avatarNama}>{profile.nama}</Text>
          <Text style={styles.avatarUsername}>@{profile.username}</Text>

          <TouchableOpacity
            style={styles.btnEditProfil}
            onPress={() => router.push('/edit-profile')}
            activeOpacity={0.85}
          >
            <Ionicons name="create-outline" size={16} color="#00AA5B" />
            <Text style={styles.btnEditProfilText}>Edit Profil</Text>
          </TouchableOpacity>
        </View>

        {/* Hubungi Admin WA */}
        <View style={styles.card}>
          <View style={styles.cardHeader}>
            <View style={[styles.cardIconWrap, { backgroundColor: '#e8ffe8' }]}>
              <Ionicons name="headset-outline" size={15} color="#25D366" />
            </View>
            <Text style={styles.cardTitle}>Butuh Bantuan?</Text>
          </View>

          <Text style={styles.waDesc}>
            Hubungi Admin untuk pertanyaan seputar informasi, jadwal, dan hasil tes STIFIn Anda.
          </Text>

          <View style={styles.waRow}>
            {/* Tombol WA 1 */}
            <TouchableOpacity
              style={styles.waBtn}
              onPress={() => hubungiWA(adminWa1)}
              activeOpacity={0.8}
            >
              <View style={styles.waBtnIcon}>
                <Ionicons name="logo-whatsapp" size={22} color="#25D366" />
              </View>
              <View style={{ flex: 1 }}>
                <Text style={styles.waBtnLabel}>Chat dengan promotor</Text>
                <Text style={styles.waBtnNomor}>{toLokalFormat(adminWa1)}</Text>
              </View>
              <Ionicons name="chevron-forward" size={16} color="#25D366" />
            </TouchableOpacity>

            {/* Tombol WA 2 */}
            <TouchableOpacity
              style={[styles.waBtn, { marginTop: 10 }]}
              onPress={() => hubungiWA(adminWa2)}
              activeOpacity={0.8}
            >
              <View style={styles.waBtnIcon}>
                <Ionicons name="logo-whatsapp" size={22} color="#25D366" />
              </View>
              <View style={{ flex: 1 }}>
                <Text style={styles.waBtnLabel}>Chat dengan admin</Text>
                <Text style={styles.waBtnNomor}>{toLokalFormat(adminWa2)}</Text>
              </View>
              <Ionicons name="chevron-forward" size={16} color="#25D366" />
            </TouchableOpacity>
          </View>
        </View>

        {/* Logout */}
        <View style={styles.logoutSection}>
          {!showLogoutConfirm ? (
            <TouchableOpacity
              style={styles.btnLogout}
              onPress={() => setShowLogoutConfirm(true)}
              activeOpacity={0.85}
            >
              <Ionicons name="log-out-outline" size={20} color="#fff" />
              <Text style={styles.btnLogoutText}>Keluar Akun</Text>
            </TouchableOpacity>
          ) : (
            <View style={styles.logoutConfirmBox}>
              <Text style={styles.logoutConfirmText}>Yakin ingin keluar dari akun?</Text>
              <View style={styles.logoutConfirmRow}>
                <TouchableOpacity
                  style={styles.btnBatal}
                  onPress={() => setShowLogoutConfirm(false)}
                  activeOpacity={0.85}
                >
                  <Text style={styles.btnBatalText}>Batal</Text>
                </TouchableOpacity>
                <TouchableOpacity
                  style={styles.btnLogoutConfirm}
                  onPress={handleLogout}
                  activeOpacity={0.85}
                >
                  <Ionicons name="log-out-outline" size={18} color="#fff" />
                  <Text style={styles.btnLogoutText}>Ya, Keluar</Text>
                </TouchableOpacity>
              </View>
            </View>
          )}
        </View>

      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container:   { flex: 1, backgroundColor: '#f5faf7' },
  center:      { flex: 1, justifyContent: 'center', alignItems: 'center', gap: 12, backgroundColor: '#f5faf7' },
  loadingText: { fontSize: 13, color: '#546e7a', fontWeight: '600' },

  topBar: {
    backgroundColor: '#00AA5B',
    paddingTop: Platform.OS === 'android'
      ? (StatusBar.currentHeight ? StatusBar.currentHeight + 12 : 30)
      : 16,
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
  topBarTitle:  { fontSize: 18, fontWeight: '800', color: '#fff' },
  topBarSub:    { fontSize: 11, color: 'rgba(255,255,255,0.8)', marginTop: 2 },

  content: { padding: 16, paddingBottom: 48 },

  // ── Avatar Card ──
  avatarCard: {
    backgroundColor: '#fff',
    borderRadius: 20,
    padding: 24,
    alignItems: 'center',
    marginBottom: 14,
    borderWidth: 1,
    borderColor: '#e8f5e9',
    shadowColor: '#00AA5B',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.06,
    shadowRadius: 8,
    elevation: 2,
  },

  /* ── Green glow: lingkaran avatar ── */
  avatarCircle: {
    width: 80, height: 80, borderRadius: 40,
    backgroundColor: '#00AA5B',
    justifyContent: 'center', alignItems: 'center',
    marginBottom: 12,
    shadowColor: '#00AA5B',
    shadowOffset: { width: 0, height: 0 },
    shadowOpacity: 0.6,
    shadowRadius: 12,
    elevation: 8,
  },

  avatarText:     { fontSize: 30, fontWeight: '800', color: '#fff' },
  avatarNama:     { fontSize: 18, fontWeight: '800', color: '#1a1a2e', marginBottom: 2 },
  avatarUsername: { fontSize: 13, color: '#90a4ae', marginBottom: 16 },
  btnEditProfil: {
    flexDirection: 'row', alignItems: 'center', gap: 6,
    paddingVertical: 9, paddingHorizontal: 20,
    borderRadius: 12,
    borderWidth: 1.5, borderColor: '#00AA5B',
    backgroundColor: '#f0faf5',
  },
  btnEditProfilText: { fontSize: 13, fontWeight: '800', color: '#00AA5B' },

  // ── Info Card ──
  card: {
    backgroundColor: '#fff',
    borderRadius: 18,
    padding: 16,
    marginBottom: 14,
    borderWidth: 1,
    borderColor: '#e8f5e9',
    shadowColor: '#00AA5B',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.06,
    shadowRadius: 8,
    elevation: 2,
  },
  cardHeader: {
    flexDirection: 'row', alignItems: 'center', gap: 8,
    marginBottom: 16,
  },
  cardIconWrap: {
    width: 28, height: 28, borderRadius: 8,
    backgroundColor: '#e8f5e9',
    justifyContent: 'center', alignItems: 'center',
  },
  cardTitle: { fontSize: 14, fontWeight: '800', color: '#1a1a2e' },

  // ── WA Buttons ──
  waDesc: { fontSize: 12, color: '#78909c', lineHeight: 17, marginBottom: 14 },
  waRow:  {},

  /* ── Green glow: kotak chat WA ── */
  waBtn: {
    flexDirection: 'row', alignItems: 'center', gap: 12,
    backgroundColor: '#f0fff8',
    borderRadius: 14, padding: 14,
    borderWidth: 1.5, borderColor: '#b9f0d0',
    shadowColor: '#00AA5B',
    shadowOffset: { width: 0, height: 0 },
    shadowOpacity: 0.45,
    shadowRadius: 10,
    elevation: 6,
  },

  waBtnIcon: {
    width: 42, height: 42, borderRadius: 12,
    backgroundColor: '#e8ffe8',
    justifyContent: 'center', alignItems: 'center',
  },
  waBtnLabel: { fontSize: 10, fontWeight: '700', color: '#78909c', textTransform: 'capitalize', letterSpacing: 0.5 },
  waBtnNomor: { fontSize: 14, fontWeight: '800', color: '#1a1a2e', marginTop: 1 },

  // ── Logout ──
  logoutSection: { marginTop: 4, marginBottom: 8 },
  btnLogout: {
    backgroundColor: '#e53935',
    padding: 16, borderRadius: 14,
    flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 8,
    shadowColor: '#e53935',
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.3, shadowRadius: 10, elevation: 6,
  },
  btnLogoutText: { color: '#fff', fontWeight: '800', fontSize: 15 },
  logoutConfirmBox: {
    backgroundColor: '#fff5f5',
    borderRadius: 14, padding: 16,
    borderWidth: 1.5, borderColor: '#ffcdd2',
  },
  logoutConfirmText: {
    fontSize: 13, fontWeight: '700', color: '#c62828',
    textAlign: 'center', marginBottom: 12,
  },
  logoutConfirmRow: { flexDirection: 'row', gap: 10 },
  btnBatal: {
    flex: 1, padding: 14, borderRadius: 12, alignItems: 'center',
    backgroundColor: '#f5faf7', borderWidth: 1.5, borderColor: '#e0f2ec',
  },
  btnBatalText: { fontSize: 14, fontWeight: '700', color: '#546e7a' },
  btnLogoutConfirm: {
    flex: 1, padding: 14, borderRadius: 12, alignItems: 'center',
    flexDirection: 'row', justifyContent: 'center', gap: 6,
    backgroundColor: '#e53935',
    shadowColor: '#e53935',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.25, shadowRadius: 6, elevation: 4,
  },
});