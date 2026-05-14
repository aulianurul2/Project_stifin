import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  SafeAreaView,
  TextInput,
  TouchableOpacity,
  ScrollView,
  Alert,
  ActivityIndicator,
  KeyboardTypeOptions
} from 'react-native';

import { useRouter, useLocalSearchParams } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';
import axiosInstance from '@/src/api/axiosConfig';

interface KlienFormData {
  id_klien: string;
  nama: string;
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

interface CustomInputProps {
  label: string;
  value: string;
  onChange: (val: string) => void;
  keyboardType?: KeyboardTypeOptions;
  multiline?: boolean;
  placeholder?: string;
}

export default function FormPendaftaran() {
  const router = useRouter();
  const params = useLocalSearchParams();

  const { id_jadwal, tanggal, waktu } = params;

  const [loading, setLoading] = useState(false);
  const [fetchingData, setFetchingData] = useState(true);

  const [formData, setFormData] = useState<KlienFormData>({
    id_klien: '',
    nama: '',
    no_hp: '',
    tanggal_lahir: '',
    jenis_kelamin: 'L',
    golongan_darah: '-',
    email: '',
    alamat: '',
    institusi: '',
    sosmed: '',
    domisili: ''
  });

  useEffect(() => {
    loadProfileData();
  }, []);

  const loadProfileData = async () => {
    try {
      const response = await axiosInstance.get('/profile');
      const u = response.data;

      setFormData({
        id_klien: u.id_user || '',
        nama: u.nama || '',
        no_hp: u.no_hp || '',
        tanggal_lahir: u.tanggal_lahir || '',
        jenis_kelamin: u.jenis_kelamin || 'L',
        golongan_darah: u.golongan_darah || '-',
        email: u.email || '',
        alamat: u.alamat || '',
        institusi: u.institusi || '',
        sosmed: u.sosmed || '',
        domisili: u.domisili || ''
      });
    } catch (error) {
      console.log(error);
    } finally {
      setFetchingData(false);
    }
  };

  const handleKirim = async () => {
    if (
      !formData.nama ||
      !formData.no_hp ||
      !formData.email ||
      !formData.alamat
    ) {
      Alert.alert(
        'Perhatian',
        'Mohon isi Nama, Nomor HP, Email, dan Alamat.'
      );
      return;
    }

    setLoading(true);

    try {
      const response = await axiosInstance.post(
        '/pendaftaran/submit',
        {
          id_jadwal: id_jadwal,
          nama_lengkap: formData.nama,
          no_hp: formData.no_hp,
          email: formData.email,
          alamat: formData.alamat,
          tanggal_lahir: formData.tanggal_lahir,
          jenis_kelamin: formData.jenis_kelamin,
          golongan_darah: formData.golongan_darah,
          domisili: formData.domisili,
          institusi: formData.institusi,
          sosmed: formData.sosmed
        }
      );

      if (
        response.status === 200 ||
        response.status === 201
      ) {
        Alert.alert(
          'Berhasil',
          'Pendaftaran berhasil dikirim!'
        );

        setTimeout(() => {
          router.replace('/riwayat');
        }, 300);
      }
    } catch (error) {
      console.log(error);
      Alert.alert(
        'Gagal',
        'Terjadi kesalahan saat mengirim data.'
      );
    } finally {
      setLoading(false);
    }
  };

  if (fetchingData) {
    return (
      <View style={styles.center}>
        <ActivityIndicator
          size="large"
          color="#1e40af"
        />
        <Text style={{ marginTop: 10 }}>
          Menyiapkan formulir...
        </Text>
      </View>
    );
  }

  return (
    <SafeAreaView style={styles.container}>
      {/* HEADER */}
      <View style={styles.header}>
        <TouchableOpacity
          onPress={() => router.back()}
        >
          <Ionicons
            name="arrow-back"
            size={24}
            color="#000"
          />
        </TouchableOpacity>

        <Text style={styles.headerTitle}>
          Konfirmasi Data Diri
        </Text>
      </View>

      <ScrollView
        contentContainerStyle={styles.content}
        showsVerticalScrollIndicator={false}
      >
        {/* INFO JADWAL */}
        <View style={styles.infoJadwal}>
          <Ionicons
            name="time-outline"
            size={20}
            color="#fff"
          />

          <Text style={styles.infoJadwalText}>
            Jadwal: {tanggal} | {waktu} WIB
          </Text>
        </View>

        {/* DATA PERSONAL */}
        <Text style={styles.sectionTitle}>
          Data Personal
        </Text>

        <CustomInput
          label="Nama Lengkap"
          value={formData.nama}
          onChange={(val) =>
            setFormData({
              ...formData,
              nama: val
            })
          }
        />

        <CustomInput
          label="Nomor WhatsApp"
          value={formData.no_hp}
          keyboardType="phone-pad"
          onChange={(val) =>
            setFormData({
              ...formData,
              no_hp: val
            })
          }
        />

        {/* TANGGAL + GOL DARAH */}
        <View style={styles.row}>
          <View style={{ flex: 1, marginRight: 10 }}>
            <CustomInput
              label="Tgl Lahir (Tahun-Bulan-Tanggal)"
              value={formData.tanggal_lahir}
              onChange={(val) =>
                setFormData({
                  ...formData,
                  tanggal_lahir: val
                })
              }
            />
          </View>

          <View style={{ width: 150 }}>
            <Text style={styles.labelSimple}>
              Gol. Darah
            </Text>

            <View style={styles.bloodRow}>
              {['A', 'B', 'AB', 'O'].map(
                (item) => (
                  <TouchableOpacity
                    key={item}
                    style={[
                      styles.bloodBtn,
                      formData.golongan_darah ===
                        item &&
                        styles.bloodBtnActive
                    ]}
                    onPress={() =>
                      setFormData({
                        ...formData,
                        golongan_darah: item
                      })
                    }
                  >
                    <Text
                      style={[
                        styles.bloodText,
                        formData.golongan_darah ===
                          item &&
                          styles.bloodTextActive
                      ]}
                    >
                      {item}
                    </Text>
                  </TouchableOpacity>
                )
              )}
            </View>
          </View>
        </View>

        <CustomInput
          label="Email Aktif"
          value={formData.email}
          keyboardType="email-address"
          onChange={(val) =>
            setFormData({
              ...formData,
              email: val
            })
          }
        />

        {/* DATA TAMBAHAN */}
        <Text style={styles.sectionTitle}>
          Data Tambahan
        </Text>

        <CustomInput
          label="Institusi / Pekerjaan"
          value={formData.institusi}
          onChange={(val) =>
            setFormData({
              ...formData,
              institusi: val
            })
          }
        />

        <CustomInput
          label="Username Sosmed (FB/IG)"
          value={formData.sosmed}
          onChange={(val) =>
            setFormData({
              ...formData,
              sosmed: val
            })
          }
        />

        <CustomInput
          label="Kota Domisili"
          value={formData.domisili}
          onChange={(val) =>
            setFormData({
              ...formData,
              domisili: val
            })
          }
        />

        <CustomInput
          label="Alamat Lengkap"
          value={formData.alamat}
          multiline
          onChange={(val) =>
            setFormData({
              ...formData,
              alamat: val
            })
          }
        />

        {/* BUTTON */}
        <View style={styles.footer}>
          <TouchableOpacity
            style={[
              styles.btnSubmit,
              loading && { opacity: 0.7 }
            ]}
            onPress={handleKirim}
            disabled={loading}
          >
            {loading ? (
              <ActivityIndicator color="#fff" />
            ) : (
              <Text style={styles.btnText}>
                Konfirmasi & Daftar
              </Text>
            )}
          </TouchableOpacity>
        </View>
      </ScrollView>
    </SafeAreaView>
  );
}

const CustomInput = ({
  label,
  value,
  onChange,
  keyboardType = 'default',
  multiline = false,
  placeholder
}: CustomInputProps) => (
  <View style={styles.inputGroup}>
    <Text style={styles.labelSimple}>
      {label}
    </Text>

    <TextInput
      style={[
        styles.input,
        multiline && styles.textArea
      ]}
      value={value}
      onChangeText={onChange}
      keyboardType={keyboardType}
      multiline={multiline}
      placeholder={
        placeholder ||
        `Masukkan ${label.toLowerCase()}`
      }
      placeholderTextColor="#94a3b8"
    />
  </View>
);

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f8fafc'
  },

  center: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center'
  },

  header: {
    padding: 20,
    backgroundColor: '#fff',
    flexDirection: 'row',
    alignItems: 'center',
    gap: 15,
    borderBottomWidth: 1,
    borderColor: '#e2e8f0'
  },

  headerTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#0f172a'
  },

  content: {
    padding: 20
  },

  infoJadwal: {
    backgroundColor: '#1e40af',
    padding: 15,
    borderRadius: 12,
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    gap: 8,
    marginBottom: 25
  },

  infoJadwalText: {
    color: '#fff',
    fontWeight: '700',
    fontSize: 14
  },

  sectionTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#1e40af',
    marginBottom: 15,
    marginTop: 5
  },

  inputGroup: {
    marginBottom: 15
  },

  labelSimple: {
    fontSize: 13,
    fontWeight: '600',
    color: '#475569',
    marginBottom: 6,
    marginLeft: 2
  },

  input: {
    backgroundColor: '#fff',
    borderWidth: 1,
    borderColor: '#cbd5e1',
    borderRadius: 10,
    padding: 12,
    fontSize: 15,
    color: '#1e293b'
  },

  textArea: {
    height: 80,
    textAlignVertical: 'top'
  },

  row: {
    flexDirection: 'row',
    alignItems: 'flex-end',
    marginBottom: 15
  },

  bloodRow: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 8
  },

  bloodBtn: {
    borderWidth: 1,
    borderColor: '#cbd5e1',
    paddingVertical: 8,
    paddingHorizontal: 12,
    borderRadius: 10,
    backgroundColor: '#fff'
  },

  bloodBtnActive: {
    backgroundColor: '#1e40af',
    borderColor: '#1e40af'
  },

  bloodText: {
    color: '#334155',
    fontWeight: '600'
  },

  bloodTextActive: {
    color: '#fff'
  },

  footer: {
    marginTop: 20,
    marginBottom: 40
  },

  btnSubmit: {
    backgroundColor: '#1e40af',
    padding: 16,
    borderRadius: 12,
    alignItems: 'center',
    elevation: 4
  },

  btnText: {
    color: '#fff',
    fontWeight: 'bold',
    fontSize: 16
  }
});