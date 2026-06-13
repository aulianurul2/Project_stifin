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

// ── Design Tokens ──────────────────────────────────────────────
const C = {
  primary:      '#00AA5B',
  primaryDark:  '#007A40',
  primaryLight: '#E8F5EE',
  primaryMint:  '#D0EDE0',
  surface:      '#FFFFFF',
  bg:           '#F3F7F5',
  text:         '#0F172A',
  textSub:      '#64748B',
  textMuted:    '#94A3B8',
  border:       '#E2ECE7',
  danger:       '#DC2626',
  dangerLight:  '#FEF2F2',
  dangerBorder: '#FECACA',
  wa:           '#22C55E',
  waLight:      '#F0FDF4',
  waBorder:     '#BBF7D0',
};

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
    try { await axiosInstance.post('/logout'); } catch (_) {}
    router.replace('/login');
  };

  const hubungiWA = (nomor: string) => {
    const pesan = encodeURIComponent(
      'Halo Admin, saya ingin menanyakan seputar informasi jadwal dan hasil tes STIFIn. Mohon bantuannya, terima kasih.'
    );
    Linking.openURL(`https://wa.me/${nomor}?text=${pesan}`);
  };

  const toLokalFormat = (nomor: string) =>
    nomor.startsWith('62') ? '0' + nomor.slice(2) : nomor;

  const inisial = profile.nama
    ? profile.nama.trim().split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase()
    : '?';

  if (fetching) {
    return (
      <View style={styles.center}>
        <ActivityIndicator size="large" color={C.primary} />
        <Text style={styles.loadingText}>Memuat profil…</Text>
      </View>
    );
  }

  return (
    <SafeAreaView style={styles.container}>
      <StatusBar barStyle="light-content" backgroundColor={C.primary} />

      {/* ── Top Bar ─────────────────────────────────────── */}
      <View style={styles.topBar}>
        <TouchableOpacity style={styles.backBtn} onPress={() => router.replace('/home')}>
          <Ionicons name="arrow-back" size={20} color="#fff" />
        </TouchableOpacity>
        <View style={styles.topBarCenter}>
          <Text style={styles.topBarTitle}>Profil Saya</Text>
          <Text style={styles.topBarSub}>Kelola informasi akun Anda</Text>
        </View>
        <View style={{ width: 38 }} />
      </View>

      <ScrollView
        contentContainerStyle={styles.content}
        showsVerticalScrollIndicator={false}
      >

        {/* ── Avatar Card ─────────────────────────────────── */}
        <View style={styles.avatarCard}>
          {/* Avatar circle */}
          <View style={styles.avatarCircle}>
            <Text style={styles.avatarText}>{inisial}</Text>
          </View>

          <Text style={styles.avatarNama}>{profile.nama || '—'}</Text>
          <Text style={styles.avatarUsername}>@{profile.username || '—'}</Text>

          {/* Divider */}
          <View style={styles.avatarDivider} />

          {/* Edit button */}
          <TouchableOpacity
            style={styles.btnEdit}
            onPress={() => router.push('/edit-profile')}
            activeOpacity={0.85}
          >
            <Ionicons name="create-outline" size={15} color={C.primary} />
            <Text style={styles.btnEditText}>Edit Profil</Text>
          </TouchableOpacity>
        </View>

        {/* ── Bantuan / WA ────────────────────────────────── */}
        <View style={styles.section}>
          {/* Section header */}
          <View style={styles.sectionHeader}>
            <View style={styles.sectionIconWrap}>
              <Ionicons name="headset-outline" size={14} color={C.primary} />
            </View>
            <View>
              <Text style={styles.sectionTitle}>Butuh Bantuan?</Text>
              <Text style={styles.sectionSub}>Hubungi kami via WhatsApp</Text>
            </View>
          </View>

          <Text style={styles.waDesc}>
            Tanya seputar informasi, jadwal, atau hasil tes STIFIn langsung ke tim kami.
          </Text>

          {/* WA 1 */}
          <TouchableOpacity
            style={styles.waBtn}
            onPress={() => hubungiWA(adminWa1)}
            activeOpacity={0.82}
          >
            <View style={styles.waBtnIconWrap}>
              <Ionicons name="logo-whatsapp" size={20} color={C.wa} />
            </View>
            <View style={{ flex: 1 }}>
              <Text style={styles.waBtnRole}>Promotor STIFIn</Text>
              <Text style={styles.waBtnNomor}>{toLokalFormat(adminWa1)}</Text>
            </View>
            <View style={styles.waBtnArrow}>
              <Ionicons name="arrow-forward" size={13} color={C.wa} />
            </View>
          </TouchableOpacity>

          {/* WA 2 */}
          <TouchableOpacity
            style={[styles.waBtn, { marginTop: 10 }]}
            onPress={() => hubungiWA(adminWa2)}
            activeOpacity={0.82}
          >
            <View style={styles.waBtnIconWrap}>
              <Ionicons name="logo-whatsapp" size={20} color={C.wa} />
            </View>
            <View style={{ flex: 1 }}>
              <Text style={styles.waBtnRole}>Admin STIFIn</Text>
              <Text style={styles.waBtnNomor}>{toLokalFormat(adminWa2)}</Text>
            </View>
            <View style={styles.waBtnArrow}>
              <Ionicons name="arrow-forward" size={13} color={C.wa} />
            </View>
          </TouchableOpacity>
        </View>

        {/* ── Logout ──────────────────────────────────────── */}
        <View style={styles.logoutSection}>
          {!showLogoutConfirm ? (
            <TouchableOpacity
              style={styles.btnLogout}
              onPress={() => setShowLogoutConfirm(true)}
              activeOpacity={0.85}
            >
              <Ionicons name="log-out-outline" size={18} color={C.danger} />
              <Text style={styles.btnLogoutText}>Keluar Akun</Text>
            </TouchableOpacity>
          ) : (
            <View style={styles.logoutConfirmBox}>
              <View style={styles.logoutConfirmIcon}>
                <Ionicons name="warning-outline" size={22} color={C.danger} />
              </View>
              <Text style={styles.logoutConfirmTitle}>Keluar dari Akun?</Text>
              <Text style={styles.logoutConfirmSub}>
                Anda harus login kembali untuk mengakses aplikasi.
              </Text>
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
                  <Ionicons name="log-out-outline" size={16} color="#fff" />
                  <Text style={styles.btnLogoutConfirmText}>Ya, Keluar</Text>
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
  container:   { flex: 1, backgroundColor: C.bg },
  center:      { flex: 1, justifyContent: 'center', alignItems: 'center', gap: 12, backgroundColor: C.bg },
  loadingText: { fontSize: 13, color: C.textSub, fontWeight: '600' },

  // Top Bar
  topBar: {
    backgroundColor: C.primary,
    paddingTop: Platform.OS === 'android'
      ? (StatusBar.currentHeight ? StatusBar.currentHeight + 12 : 30)
      : 16,
    paddingBottom: 20,
    paddingHorizontal: 16,
    flexDirection: 'row',
    alignItems: 'center',
  },
  backBtn: {
    width: 38, height: 38, borderRadius: 19,
    backgroundColor: 'rgba(255,255,255,0.18)',
    justifyContent: 'center', alignItems: 'center',
  },
  topBarCenter: { flex: 1, alignItems: 'center' },
  topBarTitle:  { fontSize: 17, fontWeight: '800', color: '#fff', letterSpacing: -0.2 },
  topBarSub:    { fontSize: 11, color: 'rgba(255,255,255,0.72)', marginTop: 2 },

  content: { padding: 16, paddingBottom: 52 },

  // ── Avatar Card ──
  avatarCard: {
    backgroundColor: C.surface,
    borderRadius: 24,
    paddingVertical: 28,
    paddingHorizontal: 20,
    alignItems: 'center',
    marginBottom: 14,
    borderWidth: 1,
    borderColor: C.border,
    ...Platform.select({
      ios:     { shadowColor: '#000', shadowOffset: { width: 0, height: 4 }, shadowOpacity: 0.07, shadowRadius: 12 },
      android: { elevation: 3 },
    }),
  },
  avatarCircle: {
    width: 84, height: 84, borderRadius: 42,
    backgroundColor: C.primary,
    justifyContent: 'center', alignItems: 'center',
    marginBottom: 14,
    ...Platform.select({
      ios:     { shadowColor: C.primary, shadowOffset: { width: 0, height: 6 }, shadowOpacity: 0.45, shadowRadius: 14 },
      android: { elevation: 8 },
    }),
  },
  avatarText:     { fontSize: 32, fontWeight: '800', color: '#fff', letterSpacing: -0.5 },
  avatarNama:     { fontSize: 19, fontWeight: '800', color: C.text, letterSpacing: -0.3, marginBottom: 3 },
  avatarUsername: { fontSize: 13, color: C.textMuted, fontWeight: '500' },

  avatarDivider: {
    width: '100%', height: 1,
    backgroundColor: C.border,
    marginVertical: 18,
  },

  btnEdit: {
    flexDirection: 'row', alignItems: 'center', gap: 7,
    paddingVertical: 10, paddingHorizontal: 24,
    borderRadius: 12,
    backgroundColor: C.primaryLight,
    borderWidth: 1, borderColor: C.primaryMint,
  },
  btnEditText: { fontSize: 13, fontWeight: '700', color: C.primary },

  // ── Section (WA) ──
  section: {
    backgroundColor: C.surface,
    borderRadius: 20,
    padding: 18,
    marginBottom: 14,
    borderWidth: 1,
    borderColor: C.border,
    ...Platform.select({
      ios:     { shadowColor: '#000', shadowOffset: { width: 0, height: 4 }, shadowOpacity: 0.06, shadowRadius: 10 },
      android: { elevation: 2 },
    }),
  },
  sectionHeader: {
    flexDirection: 'row', alignItems: 'center', gap: 12,
    marginBottom: 12,
  },
  sectionIconWrap: {
    width: 34, height: 34, borderRadius: 10,
    backgroundColor: C.primaryLight,
    justifyContent: 'center', alignItems: 'center',
  },
  sectionTitle: { fontSize: 14, fontWeight: '800', color: C.text },
  sectionSub:   { fontSize: 11, color: C.textMuted, marginTop: 1 },

  waDesc: {
    fontSize: 12, color: C.textSub, lineHeight: 18,
    marginBottom: 14,
    paddingBottom: 14,
    borderBottomWidth: 1, borderColor: C.border,
  },

  waBtn: {
    flexDirection: 'row', alignItems: 'center', gap: 12,
    backgroundColor: C.waLight,
    borderRadius: 14, padding: 14,
    borderWidth: 1, borderColor: C.waBorder,
  },
  waBtnIconWrap: {
    width: 40, height: 40, borderRadius: 12,
    backgroundColor: '#DCFCE7',
    justifyContent: 'center', alignItems: 'center',
  },
  waBtnRole:  { fontSize: 10, fontWeight: '700', color: C.textMuted, letterSpacing: 0.5, textTransform: 'uppercase', marginBottom: 2 },
  waBtnNomor: { fontSize: 15, fontWeight: '800', color: C.text },
  waBtnArrow: {
    width: 28, height: 28, borderRadius: 8,
    backgroundColor: '#DCFCE7',
    justifyContent: 'center', alignItems: 'center',
  },

  // ── Logout ──
  logoutSection: { marginTop: 2 },

  btnLogout: {
    flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 8,
    padding: 16, borderRadius: 16,
    backgroundColor: C.surface,
    borderWidth: 1.5, borderColor: C.dangerBorder,
    ...Platform.select({
      ios:     { shadowColor: C.danger, shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.1, shadowRadius: 6 },
      android: { elevation: 2 },
    }),
  },
  btnLogoutText: { fontSize: 15, fontWeight: '700', color: C.danger },

  logoutConfirmBox: {
    backgroundColor: C.surface,
    borderRadius: 20, padding: 22,
    alignItems: 'center',
    borderWidth: 1.5, borderColor: C.dangerBorder,
    ...Platform.select({
      ios:     { shadowColor: C.danger, shadowOffset: { width: 0, height: 4 }, shadowOpacity: 0.1, shadowRadius: 10 },
      android: { elevation: 3 },
    }),
  },
  logoutConfirmIcon: {
    width: 48, height: 48, borderRadius: 24,
    backgroundColor: C.dangerLight,
    justifyContent: 'center', alignItems: 'center',
    marginBottom: 12,
  },
  logoutConfirmTitle: {
    fontSize: 16, fontWeight: '800', color: C.text, marginBottom: 6,
  },
  logoutConfirmSub: {
    fontSize: 12, color: C.textSub, textAlign: 'center', lineHeight: 18,
    marginBottom: 20,
  },
  logoutConfirmRow: { flexDirection: 'row', gap: 10, width: '100%' },
  btnBatal: {
    flex: 1, padding: 14, borderRadius: 12, alignItems: 'center',
    backgroundColor: C.bg,
    borderWidth: 1, borderColor: C.border,
  },
  btnBatalText: { fontSize: 14, fontWeight: '700', color: C.textSub },
  btnLogoutConfirm: {
    flex: 1, padding: 14, borderRadius: 12,
    flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 6,
    backgroundColor: C.danger,
    ...Platform.select({
      ios:     { shadowColor: C.danger, shadowOffset: { width: 0, height: 4 }, shadowOpacity: 0.3, shadowRadius: 8 },
      android: { elevation: 4 },
    }),
  },
  btnLogoutConfirmText: { fontSize: 14, fontWeight: '700', color: '#fff' },
});