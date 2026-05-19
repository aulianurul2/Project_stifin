import React, { useState, useEffect } from 'react';
import { 
  View, 
  Text, 
  StyleSheet, 
  SafeAreaView, 
  ScrollView, 
  TouchableOpacity, 
  Dimensions,
  ViewStyle,
  ActivityIndicator,
  Image
} from 'react-native';
import { useRouter } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';
import axiosInstance from '@/src/api/axiosConfig';

const { width } = Dimensions.get('window');
const CARD_WIDTH = width * 0.75; 

// Mengambil baseUrl otomatis dari config axios kamu (menghapus '/api' agar mengarah ke root storage)
const AXIOS_BASE_URL = axiosInstance.defaults.baseURL;
const BACKEND_ROOT = AXIOS_BASE_URL ? AXIOS_BASE_URL.replace('/api', '') : '';

interface InfoCard {
  id: string;
  title: string;
  description: string;
  icon: string;
  color: string;
  textColor: string;
  image: string | null;
}

export default function HomeSTIFIn() {
  const router = useRouter();
  const [infoCards, setInfoCards] = useState<InfoCard[]>([]);
  const [loading, setLoading] = useState<boolean>(true);

  // Mengambil data dari API backend Laravel secara real-time
  useEffect(() => {
    const fetchInformasi = async () => {
      try {
        const response = await axiosInstance.get('/informasi-tes');
        setInfoCards(response.data);
      } catch (error) {
        console.log("Gagal memuat info dari backend:", error);
      } finally {
        setLoading(false);
      }
    };

    fetchInformasi();
  }, []);

  return (
    <SafeAreaView style={styles.container}>
      {/* Welcome Header */}
      <View style={styles.header}>
        <View>
          <Text style={styles.welcomeText}>Selamat Datang,</Text>
          <Text style={styles.brandText}>STIFIn Information System</Text>
        </View>
        <TouchableOpacity style={styles.profileButton} onPress={() => router.push('/edit-profile')}>
          <Ionicons name="person-circle-outline" size={32} color="#1e40af" />
        </TouchableOpacity>
      </View>

      <ScrollView showsVerticalScrollIndicator={false} contentContainerStyle={styles.content}>
        {/* Section Judul Slider */}
        <Text style={styles.sectionTitle}>Informasi Tes & Layanan</Text>

        {/* Indikator Loading saat memproses data internet */}
        {loading ? (
          <View style={{ padding: 40, alignItems: 'center' }}>
            <ActivityIndicator size="small" color="#1e40af" />
            <Text style={{ marginTop: 8, fontSize: 12, color: '#64748b' }}>Memuat informasi...</Text>
          </View>
        ) : (
          /* Horizontal Carousel / Slider Card */
          <ScrollView 
            horizontal 
            showsHorizontalScrollIndicator={false} 
            snapToInterval={CARD_WIDTH + 16}
            decelerationRate="fast"
            contentContainerStyle={styles.sliderContainer}
          >
            {infoCards.map((card) => (
              <View key={card.id} style={[styles.card, { backgroundColor: card.color || '#eff6ff' }]}>
                <View>
                  <View style={styles.cardHeader}>
                    <Ionicons name={(card.icon || 'information-circle-outline') as any} size={28} color={card.textColor || '#1e40af'} />
                  </View>

                  {/* TAMPILAN GAMBAR PREMIUM DENGAN BLUR BACKGROUND */}
                  {card.image && (
                    <View style={styles.imageContainer}>
                      {/* Lapisan Belakang: Gambar Duplikat di-Blur */}
                      <Image 
                        source={{ uri: `${BACKEND_ROOT}/storage/${card.image}` }} 
                        style={styles.cardImageBlur} 
                        blurRadius={15}
                        resizeMode="cover"
                      />
                      {/* Lapisan Depan: Gambar Utama Asli */}
                      <Image 
                        source={{ uri: `${BACKEND_ROOT}/storage/${card.image}` }} 
                        style={styles.cardImageMain} 
                        resizeMode="contain"
                      />
                    </View>
                  )}

                  <Text style={[styles.cardTitle, { color: card.textColor || '#1e40af' }]}>{card.title}</Text>
                  <Text style={styles.cardDescription}>{card.description}</Text>
                </View>
              </View>
            ))}
          </ScrollView>
        )}

        {/* Container Aksi Tombol Daftar Sekarang */}
        <View style={styles.actionContainer}>
          <Text style={styles.actionPrompt}>Sudah siap mengetahui potensi genetik Anda?</Text>
          <TouchableOpacity 
            style={styles.btnDaftar} 
            onPress={() => router.push('/pendaftaran')}
          >
            <Text style={styles.btnText}>Daftar Sekarang</Text>
            <Ionicons name="arrow-forward" size={20} color="#fff" />
          </TouchableOpacity>
        </View>
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
    paddingHorizontal: 20, 
    paddingTop: 20, 
    paddingBottom: 15, 
    backgroundColor: '#fff', 
    flexDirection: 'row', 
    justifyContent: 'space-between', 
    alignItems: 'center',
    borderBottomWidth: 1,
    borderColor: '#e2e8f0'
  } as ViewStyle,
  welcomeText: { 
    fontSize: 14, 
    color: '#64748b' 
  },
  brandText: { 
    fontSize: 18, 
    fontWeight: 'bold', 
    color: '#0f172a' 
  },
  profileButton: {
    padding: 4,
  },
  content: { 
    paddingVertical: 20 
  },
  sectionTitle: { 
    fontSize: 16, 
    fontWeight: 'bold', 
    color: '#334155', 
    paddingHorizontal: 20,
    marginBottom: 12 
  },
  sliderContainer: { 
    paddingLeft: 20, 
    paddingRight: 4,
    paddingBottom: 10
  },
  card: { 
    width: CARD_WIDTH, 
    borderRadius: 16, 
    padding: 20, 
    marginRight: 16, 
    shadowColor: '#0f172a',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.05,
    shadowRadius: 10,
    elevation: 2,
    minHeight: 180
  } as ViewStyle,
  cardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 12
  } as ViewStyle,
  badge: {
    backgroundColor: 'rgba(255,255,255,0.6)',
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 8
  },
  badgeText: {
    fontSize: 11,
    fontWeight: '700'
  },
  // STYLING BARU UNTUK KOMPONEN GAMBAR BLUR BALUTAN
  imageContainer: {
    width: '100%',
    aspectRatio: 16 / 9,
    borderRadius: 12,
    marginBottom: 12,
    overflow: 'hidden',
    position: 'relative',
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#f1f5f9',
  },
  cardImageBlur: {
    width: '100%',
    height: '100%',
    position: 'absolute',
    opacity: 0.35, // Transparansi latar belakang agar tidak terlalu mencolok
  },
  cardImageMain: {
    width: '100%',
    height: '100%',
  },
  cardTitle: { 
    fontSize: 16, 
    fontWeight: 'bold', 
    lineHeight: 22,
    marginBottom: 6
  },
  cardDescription: { 
    fontSize: 13, 
    color: '#475569', 
    lineHeight: 18 
  },
  actionContainer: { 
    marginTop: 35, 
    paddingHorizontal: 20, 
    alignItems: 'center' 
  },
  actionPrompt: {
    fontSize: 14,
    color: '#64748b',
    marginBottom: 15,
    textAlign: 'center'
  },
  btnDaftar: { 
    backgroundColor: '#1e40af', 
    width: '100%',
    padding: 16, 
    borderRadius: 14, 
    flexDirection: 'row', 
    justifyContent: 'center', 
    alignItems: 'center', 
    gap: 8,
    shadowColor: '#1e40af',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.2,
    shadowRadius: 8,
    elevation: 4 
  } as ViewStyle,
  btnText: { 
    color: '#fff', 
    fontWeight: 'bold', 
    fontSize: 16 
  }
});