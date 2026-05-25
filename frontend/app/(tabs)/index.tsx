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

      {/* Compact Green Header */}
      <View style={styles.header}>
        <View style={styles.headerLeft}>
          <View>
            <Text style={styles.brandText}>STIFIn</Text>
            <Text style={styles.brandSub}>Information System</Text>
          </View>
        </View>
        <TouchableOpacity style={styles.profileButton} onPress={() => router.push('/edit-profile')}>
          <View style={styles.profileAvatar}>
            <Ionicons name="person-outline" size={20} color="#00AA5B" />
          </View>
        </TouchableOpacity>
      </View>

      <ScrollView showsVerticalScrollIndicator={false} contentContainerStyle={styles.content}>

        {/* Hero Banner */}
        <View style={styles.heroBanner}>
          <View style={styles.heroBannerContent}>
            <Text style={styles.heroBannerTag}>🧬 Genetik Tes</Text>
            <Text style={styles.heroBannerTitle}>Kenali Potensi{'\n'}Genetik Anda</Text>
            <Text style={styles.heroBannerSub}>Temukan kekuatan tersembunyi lewat metode STIFIn</Text>
          </View>
          <View style={styles.heroBannerDeco}>
            <Text style={{ fontSize: 64 }}>🧬</Text>
          </View>
        </View>

        {/* Info Cards Slider */}
        <View style={styles.sliderSection}>
          <View style={styles.sliderTitleRow}>
            <Text style={styles.sectionTitle}>Informasi Layanan</Text>
            <View style={styles.liveDot}>
              <View style={styles.liveDotInner} />
              <Text style={styles.liveText}>Live</Text>
            </View>
          </View>

          {loading ? (
            <View style={styles.loadingBox}>
              <ActivityIndicator size="small" color="#00AA5B" />
              <Text style={styles.loadingText}>Memuat informasi...</Text>
            </View>
          ) : (
            <ScrollView 
              horizontal 
              showsHorizontalScrollIndicator={false} 
              snapToInterval={CARD_WIDTH + 16}
              decelerationRate="fast"
              contentContainerStyle={styles.sliderContainer}
            >
              {infoCards.map((card) => (
                <View key={card.id} style={styles.card}>
                  <View style={styles.cardIconRow}>
                    <View style={styles.cardIconBg}>
                      <Ionicons name={(card.icon || 'information-circle-outline') as any} size={22} color="#00AA5B" />
                    </View>
                  </View>

                  {card.image && (
                    <View style={styles.imageContainer}>
                      <Image 
                        source={{ uri: `${BACKEND_ROOT}/storage/${card.image}` }} 
                        style={styles.cardImageBlur} 
                        blurRadius={15}
                        resizeMode="cover"
                      />
                      <Image 
                        source={{ uri: `${BACKEND_ROOT}/storage/${card.image}` }} 
                        style={styles.cardImageMain} 
                        resizeMode="contain"
                      />
                    </View>
                  )}

                  <Text style={styles.cardTitle}>{card.title}</Text>
                  <Text style={styles.cardDescription}>{card.description}</Text>
                </View>
              ))}
            </ScrollView>
          )}
        </View>

        {/* CTA Section */}
        <View style={styles.ctaSection}>
          <View style={styles.ctaCard}>
            <View style={styles.ctaLeft}>
              <Text style={styles.ctaLabel}>Siap Memulai?</Text>
              <Text style={styles.ctaTitle}>Daftarkan diri Anda sekarang</Text>
            </View>
            <TouchableOpacity 
              style={styles.ctaButton} 
              onPress={() => router.push('/pendaftaran')}
              activeOpacity={0.85}
            >
              <Ionicons name="arrow-forward" size={20} color="#fff" />
            </TouchableOpacity>
          </View>
        </View>

      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { 
    flex: 1, 
    backgroundColor: '#f5faf7',
  },
  header: { 
    paddingHorizontal: 20, 
    paddingTop: 16, 
    paddingBottom: 14, 
    backgroundColor: '#fff',
    flexDirection: 'row', 
    justifyContent: 'space-between', 
    alignItems: 'center',
    borderBottomWidth: 1,
    borderColor: '#e8f5e9',
    shadowColor: '#00AA5B',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 6,
    elevation: 3,
  } as ViewStyle,
  headerLeft: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 10,
  },
  logoMini: {
    width: 36,
    height: 36,
    borderRadius: 10,
    backgroundColor: '#00AA5B',
    justifyContent: 'center',
    alignItems: 'center',
  },
  logoMiniText: { fontSize: 18 },
  brandText: { 
    fontSize: 16, 
    fontWeight: '900', 
    color: '#1a1a2e',
    letterSpacing: 1,
  },
  brandSub: {
    fontSize: 10,
    color: '#90a4ae',
    marginTop: 1,
  },
  profileButton: { padding: 2 },
  profileAvatar: {
    width: 38,
    height: 38,
    borderRadius: 19,
    backgroundColor: '#e8f5e9',
    justifyContent: 'center',
    alignItems: 'center',
    borderWidth: 2,
    borderColor: '#00AA5B',
  },
  content: { paddingVertical: 20 },

  heroBanner: {
    marginHorizontal: 16,
    marginBottom: 24,
    backgroundColor: '#00AA5B',
    borderRadius: 20,
    padding: 22,
    flexDirection: 'row',
    overflow: 'hidden',
    shadowColor: '#00AA5B',
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.3,
    shadowRadius: 12,
    elevation: 8,
  },
  heroBannerContent: { flex: 1 },
  heroBannerTag: {
    fontSize: 12,
    color: 'rgba(255,255,255,0.8)',
    fontWeight: '600',
    marginBottom: 8,
  },
  heroBannerTitle: {
    fontSize: 22,
    fontWeight: '900',
    color: '#fff',
    lineHeight: 28,
    marginBottom: 8,
  },
  heroBannerSub: {
    fontSize: 12,
    color: 'rgba(255,255,255,0.75)',
    lineHeight: 17,
  },
  heroBannerDeco: {
    justifyContent: 'center',
    alignItems: 'center',
    opacity: 0.4,
  },

  sliderSection: { marginBottom: 20 },
  sliderTitleRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 20,
    marginBottom: 14,
  },
  sectionTitle: { 
    fontSize: 16, 
    fontWeight: '800', 
    color: '#1a1a2e',
  },
  liveDot: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#e8f5e9',
    borderRadius: 12,
    paddingHorizontal: 8,
    paddingVertical: 4,
    gap: 4,
  },
  liveDotInner: {
    width: 6,
    height: 6,
    borderRadius: 3,
    backgroundColor: '#00AA5B',
  },
  liveText: { fontSize: 11, color: '#00AA5B', fontWeight: '700' },

  loadingBox: {
    padding: 40,
    alignItems: 'center',
    gap: 8,
  },
  loadingText: {
    fontSize: 12,
    color: '#90a4ae',
  },
  sliderContainer: { 
    paddingLeft: 16, 
    paddingRight: 4,
    paddingBottom: 8,
  },
  card: { 
    width: CARD_WIDTH, 
    borderRadius: 18, 
    padding: 18, 
    marginRight: 14, 
    backgroundColor: '#fff',
    shadowColor: '#00AA5B',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.08,
    shadowRadius: 10,
    elevation: 3,
    borderWidth: 1,
    borderColor: '#e8f5e9',
  } as ViewStyle,
  cardIconRow: {
    marginBottom: 12,
  },
  cardIconBg: {
    width: 42,
    height: 42,
    borderRadius: 12,
    backgroundColor: '#e8f5e9',
    justifyContent: 'center',
    alignItems: 'center',
  },
  imageContainer: {
    width: '100%',
    aspectRatio: 16 / 9,
    borderRadius: 12,
    marginBottom: 12,
    overflow: 'hidden',
    position: 'relative',
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#f5faf7',
  },
  cardImageBlur: {
    width: '100%',
    height: '100%',
    position: 'absolute',
    opacity: 0.35,
  },
  cardImageMain: {
    width: '100%',
    height: '100%',
  },
  cardTitle: { 
    fontSize: 15, 
    fontWeight: '800', 
    color: '#1a1a2e',
    lineHeight: 21,
    marginBottom: 6,
  },
  cardDescription: { 
    fontSize: 12, 
    color: '#78909c', 
    lineHeight: 17,
  },

  ctaSection: {
    paddingHorizontal: 16,
    marginTop: 6,
  },
  ctaCard: {
    backgroundColor: '#fff',
    borderRadius: 18,
    padding: 18,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    borderWidth: 1.5,
    borderColor: '#00AA5B',
    shadowColor: '#00AA5B',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.1,
    shadowRadius: 8,
    elevation: 3,
  },
  ctaLeft: { flex: 1 },
  ctaLabel: {
    fontSize: 11,
    color: '#00AA5B',
    fontWeight: '700',
    textTransform: 'uppercase',
    letterSpacing: 0.8,
    marginBottom: 4,
  },
  ctaTitle: {
    fontSize: 15,
    fontWeight: '800',
    color: '#1a1a2e',
  },
  ctaButton: {
    width: 44,
    height: 44,
    borderRadius: 22,
    backgroundColor: '#00AA5B',
    justifyContent: 'center',
    alignItems: 'center',
    shadowColor: '#00AA5B',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 8,
    elevation: 4,
  },
  btnText: { 
    color: '#fff', 
    fontWeight: '800', 
    fontSize: 15,
  },
});