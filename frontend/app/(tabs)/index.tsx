import React, { useState, useEffect, useRef } from 'react';
import {
  View,
  Text,
  StyleSheet,
  SafeAreaView,
  ScrollView,
  TouchableOpacity,
  TouchableWithoutFeedback,
  Dimensions,
  ViewStyle,
  ActivityIndicator,
  Image,
  Animated,
  Modal,
  StatusBar,
  Platform,
} from 'react-native';
import { useRouter } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';
import axiosInstance from '@/src/api/axiosConfig';

const { width, height } = Dimensions.get('window');
const CARD_WIDTH = width * 0.85;
const CARD_GAP = 14;

const AXIOS_BASE_URL = axiosInstance.defaults.baseURL;
const BACKEND_ROOT = AXIOS_BASE_URL ? AXIOS_BASE_URL.replace('/api', '') : '';

interface InfoCard {
  id: string;
  title: string;
  description: string;
  color: string;
  textColor: string;
  image: string | null;
}

export default function HomeSTIFIn() {
  const router = useRouter();
  const [infoCards, setInfoCards] = useState<InfoCard[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [selectedCard, setSelectedCard] = useState<InfoCard | null>(null);
  const [modalVisible, setModalVisible] = useState<boolean>(false);

  const scrollViewRef = useRef<ScrollView>(null);
  const currentIndexRef = useRef<number>(0);
  const fadeAnim  = useRef(new Animated.Value(0)).current;
  const slideAnim = useRef(new Animated.Value(20)).current;

  useEffect(() => {
    const fetchInformasi = async () => {
      try {
        const response = await axiosInstance.get('/informasi-tes');
        setInfoCards(response.data);
        Animated.parallel([
          Animated.timing(fadeAnim,  { toValue: 1, duration: 600, useNativeDriver: true }),
          Animated.timing(slideAnim, { toValue: 0, duration: 600, useNativeDriver: true }),
        ]).start();
      } catch (error) {
        console.log('Gagal memuat info dari backend:', error);
      } finally {
        setLoading(false);
      }
    };
    fetchInformasi();
  }, []);

  useEffect(() => {
    if (infoCards.length <= 1 || modalVisible) return;
    const autoPlayInterval = setInterval(() => {
      let nextIndex = currentIndexRef.current + 1;
      if (nextIndex >= infoCards.length) nextIndex = 0;
      currentIndexRef.current = nextIndex;
      scrollViewRef.current?.scrollTo({ x: nextIndex * (CARD_WIDTH + CARD_GAP), animated: true });
    }, 3000);
    return () => clearInterval(autoPlayInterval);
  }, [infoCards, modalVisible]);

  const handleCardPress = (card: InfoCard) => {
    setSelectedCard(card);
    setModalVisible(true);
  };

  return (
    <SafeAreaView style={styles.container}>
      <StatusBar barStyle="dark-content" backgroundColor="#fff" />

      {/* ── Header ── */}
      <View style={styles.header}>
        <View style={styles.headerLeft}>
          <Image
            source={require('../../assets/images/logo_light.png')}
            style={styles.headerLogo}
            resizeMode="contain"
          />
          <Text style={styles.brandSub}>Information System</Text>
        </View>
        <TouchableOpacity style={styles.profileButton} onPress={() => router.push('/edit-profile')}>
          <View style={styles.profileAvatar}>
            <Ionicons name="person-outline" size={20} color="#00AA5B" />
          </View>
        </TouchableOpacity>
      </View>

      <ScrollView showsVerticalScrollIndicator={false} contentContainerStyle={styles.content}>

        {/* ── Hero Banner ── */}
        <View style={styles.heroBanner}>

          {/* Baris atas: tag + judul + sub */}
          <View style={styles.heroTop}>
            <View style={styles.heroTagContainer}>
              <Ionicons name="fitness-outline" size={13} color="rgba(255,255,255,0.9)" />
              <Text style={styles.heroBannerTag}>Genetik Tes</Text>
            </View>
            <Text style={styles.heroBannerTitle}>Unlocking Your{'\n'}Genetic Potential</Text>
            <Text style={styles.heroBannerSub}>
              Bersama Kami, Temukan Jati Diri Anda Lewat Metode STIFIn
            </Text>
          </View>

          {/* Gambar promotor — contain agar tidak terpotong */}
          <View style={styles.promotorWrapper}>
            <Image
              source={require('../../assets/images/promotor.jpeg')}
              style={styles.promotorProfile}
              resizeMode="contain"
            />
          </View>

          {/* Keunggulan */}
          <View style={styles.advantagesContainer}>
            <Text style={styles.advantagesHeading}>KEUNGGULAN METODE STIFIn:</Text>

            {[
              { icon: 'checkbox-outline',  label: 'Simpel' },
              { icon: 'create-outline',    label: 'Aplikatif' },
              { icon: 'disc-outline',      label: 'Akurat' },
            ].map(({ icon, label }) => (
              <View key={label} style={styles.advantageItem}>
                <View style={styles.advantageIconWrapper}>
                  <Ionicons name={icon as any} size={16} color="#00AA5B" />
                </View>
                <Text style={styles.advantageText}>{label}</Text>
              </View>
            ))}
          </View>

          {/* Dekorasi sudut */}
          <View style={styles.heroBannerDeco}>
            <Ionicons name="analytics" size={80} color="rgba(255,255,255,0.1)" />
          </View>
        </View>

        {/* ── Info Cards Slider ── */}
        <View style={styles.sliderSection}>
          <View style={styles.sliderTitleRow}>
            <Text style={styles.sectionTitle}>Informasi Layanan</Text>
            <View style={styles.livePill}>
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
            <Animated.View style={{ opacity: fadeAnim, transform: [{ translateY: slideAnim }] }}>
              <ScrollView
                ref={scrollViewRef}
                horizontal
                showsHorizontalScrollIndicator={false}
                snapToInterval={CARD_WIDTH + CARD_GAP}
                decelerationRate="fast"
                contentContainerStyle={styles.sliderContainer}
                onMomentumScrollEnd={(e) => {
                  currentIndexRef.current = Math.round(
                    e.nativeEvent.contentOffset.x / (CARD_WIDTH + CARD_GAP)
                  );
                }}
              >
                {infoCards.map((card) => (
                  <TouchableOpacity
                    key={card.id}
                    style={styles.card}
                    activeOpacity={0.9}
                    onPress={() => handleCardPress(card)}
                  >
                    <View style={styles.cardImageWrapper}>
                      {card.image ? (
                        <>
                          <Image
                            source={{ uri: `${BACKEND_ROOT}/storage/${card.image}` }}
                            style={styles.cardImageBlur}
                            resizeMode="cover"
                            blurRadius={18}
                          />
                          <Image
                            source={{ uri: `${BACKEND_ROOT}/storage/${card.image}` }}
                            style={styles.cardFullImage}
                            resizeMode="contain"
                          />
                        </>
                      ) : (
                        <View style={styles.cardNoImage}>
                          <Ionicons name="image-outline" size={40} color="rgba(0,170,91,0.3)" />
                        </View>
                      )}
                    </View>
                    <View style={styles.cardTextContent}>
                      <Text style={styles.cardTitle} numberOfLines={2}>{card.title}</Text>
                      <Text style={styles.cardDescription} numberOfLines={3}>{card.description}</Text>
                    </View>
                  </TouchableOpacity>
                ))}
              </ScrollView>
            </Animated.View>
          )}
        </View>

        {/* ── CTA ── */}
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

      {/* ── Modal ── */}
      <Modal
        animationType="fade"
        transparent
        visible={modalVisible}
        onRequestClose={() => setModalVisible(false)}
      >
        <TouchableWithoutFeedback onPress={() => setModalVisible(false)}>
          <View style={styles.modalOverlay}>
            <TouchableWithoutFeedback onPress={(e) => e.stopPropagation()}>
              <View style={styles.modalContent}>

                <TouchableOpacity
                  style={styles.modalCloseButton}
                  onPress={() => setModalVisible(false)}
                >
                  <Ionicons name="close" size={15} color="#334155" />
                </TouchableOpacity>

                <ScrollView showsVerticalScrollIndicator={false}>
                  {selectedCard?.image && (
                    <View style={styles.modalImageContainer}>
                      <Image
                        source={{ uri: `${BACKEND_ROOT}/storage/${selectedCard.image}` }}
                        style={styles.modalImageBlur}
                        resizeMode="cover"
                        blurRadius={18}
                      />
                      <Image
                        source={{ uri: `${BACKEND_ROOT}/storage/${selectedCard.image}` }}
                        style={styles.modalImageFull}
                        resizeMode="contain"
                      />
                    </View>
                  )}
                  <View style={styles.modalTextContainer}>
                    <Text style={styles.modalTitle}>{selectedCard?.title}</Text>
                    <Text style={styles.modalDescription}>{selectedCard?.description}</Text>
                  </View>
                </ScrollView>

                <TouchableOpacity
                  style={styles.modalActionButon}
                  onPress={() => setModalVisible(false)}
                >
                  <Text style={styles.modalActionButtonText}>Tutup</Text>
                </TouchableOpacity>

              </View>
            </TouchableWithoutFeedback>
          </View>
        </TouchableWithoutFeedback>
      </Modal>

    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f5faf7',
  },

  // Header
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
    flexDirection: 'column',
    alignItems: 'flex-start',
    justifyContent: 'center',
    paddingLeft: 20,
  },
  headerLogo: {
    width: 130,
    marginLeft: -60,
    height: 40,
  },
  brandSub: {
    fontSize: 10,
    color: '#1a1a2e',
    marginTop: 2,
    fontWeight: '600',
    letterSpacing: 0.5,
    marginLeft: -20,
  },
  profileButton: { padding: 2 },
  profileAvatar: {
    width: 38,
    height: 38,
    borderRadius: 19,
    backgroundColor: '#e8f5e9',
    justifyContent: 'center',
    alignItems: 'center',
    shadowColor: '#00AA5B',
    shadowOffset: { width: 0, height: 0 },
    shadowOpacity: 0.6,
    shadowRadius: 8,
    elevation: 6,
  },

  content: { paddingVertical: 20 },

  // Hero — layout vertikal: teks → gambar → keunggulan
  heroBanner: {
    marginHorizontal: 16,
    marginBottom: 24,
    backgroundColor: '#00AA5B',
    borderRadius: 20,
    padding: 20,
    overflow: 'hidden',
    shadowColor: '#00AA5B',
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.3,
    shadowRadius: 12,
    elevation: 8,
  },
  heroTop: {
    marginBottom: 16,
    zIndex: 2,
  },
  heroTagContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
    marginBottom: 10,
  },
  heroBannerTag: {
    fontSize: 11,
    color: 'rgba(255,255,255,0.9)',
    fontWeight: '700',
    textTransform: 'uppercase',
    letterSpacing: 0.8,
  },
  heroBannerTitle: {
    fontSize: 24,
    fontWeight: '900',
    color: '#fff',
    lineHeight: 30,
    marginBottom: 8,
    letterSpacing: -0.3,
  },
  heroBannerSub: {
    fontSize: 14,
    color: 'rgba(255,255,255,0.85)',
    lineHeight: 20,
    fontWeight: '500',
  },

  // Promotor — contain agar tidak terpotong
  promotorWrapper: {
    width: '100%',
    aspectRatio: 1,          // bujur sangkar — sesuaikan jika foto aslinya berbeda rasio
    backgroundColor: 'rgba(0,0,0,0.08)',
    borderRadius: 16,
    overflow: 'hidden',
    marginBottom: 16,
    alignSelf: 'center',
  },
  promotorProfile: {
    width: '100%',
    height: '100%',
  },

  // Keunggulan
  advantagesContainer: {
    backgroundColor: 'rgba(255,255,255,0.12)',
    borderRadius: 14,
    padding: 14,
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.22)',
  },
  advantagesHeading: {
    fontSize: 10,
    fontWeight: '800',
    color: 'rgba(255,255,255,0.7)',
    letterSpacing: 1,
    marginBottom: 10,
  },
  advantageItem: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 8,
    gap: 10,
  },
  advantageIconWrapper: {
    backgroundColor: '#fff',
    borderRadius: 8,
    width: 28,
    height: 28,
    justifyContent: 'center',
    alignItems: 'center',
  },
  advantageText: {
    fontSize: 14,
    fontWeight: '700',
    color: '#fff',
  },
  heroBannerDeco: {
    position: 'absolute',
    right: -10,
    bottom: -15,
    zIndex: 1,
  },

  // Slider
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
  livePill: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#e8f5e9',
    borderRadius: 12,
    paddingHorizontal: 8,
    paddingVertical: 4,
    gap: 5,
  },
  liveDotInner: {
    width: 6,
    height: 6,
    borderRadius: 3,
    backgroundColor: '#00AA5B',
  },
  liveText: { fontSize: 11, color: '#00AA5B', fontWeight: '700' },

  loadingBox: { padding: 40, alignItems: 'center', gap: 8 },
  loadingText: { fontSize: 12, color: '#90a4ae' },
  sliderContainer: {
    paddingLeft: 16,
    paddingRight: 4,
    paddingBottom: 12,
  },

  // Card
  card: {
    width: CARD_WIDTH,
    borderRadius: 16,
    marginRight: CARD_GAP,
    backgroundColor: '#ffffff',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.10,
    shadowRadius: 10,
    elevation: 5,
    overflow: 'hidden',
    borderWidth: 1,
    borderColor: '#e8f0eb',
  } as ViewStyle,
  cardImageWrapper: {
    width: '100%',
    aspectRatio: 4 / 3,
    backgroundColor: '#f0f7f4',
    justifyContent: 'center',
    alignItems: 'center',
    overflow: 'hidden',
  },
  cardImageBlur: {
    width: '100%',
    height: '100%',
    position: 'absolute',
    opacity: 0.6,
  },
  cardFullImage: { width: '100%', height: '100%' },
  cardNoImage: {
    width: '100%',
    height: '100%',
    backgroundColor: '#e8f5e9',
    justifyContent: 'center',
    alignItems: 'center',
  },
  cardTextContent: { padding: 14 },
  cardTitle: {
    fontSize: 15,
    fontWeight: '800',
    color: '#0f172a',
    lineHeight: 21,
    marginBottom: 6,
  },
  cardDescription: {
    fontSize: 13,
    color: '#475569',
    lineHeight: 19,
  },

  // Modal
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(15, 23, 42, 0.6)',
    justifyContent: 'center',
    alignItems: 'center',
    padding: 20,
  },
  modalContent: {
    width: '100%',
    maxHeight: height * 0.8,
    backgroundColor: '#ffffff',
    borderRadius: 24,
    padding: 20,
    position: 'relative',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 10 },
    shadowOpacity: 0.25,
    shadowRadius: 12,
    elevation: 10,
  },
  modalCloseButton: {
    position: 'absolute',
    top: 16,
    right: 16,
    zIndex: 10,
    backgroundColor: '#f1f5f9',
    width: 26,
    height: 26,
    borderRadius: 13,
    justifyContent: 'center',
    alignItems: 'center',
  },
  modalImageContainer: {
    width: '100%',
    aspectRatio: 4 / 3,
    borderRadius: 14,
    marginTop: 24,
    marginBottom: 16,
    overflow: 'hidden',
    backgroundColor: '#f0f7f4',
    justifyContent: 'center',
    alignItems: 'center',
  },
  modalImageBlur: {
    width: '100%',
    height: '100%',
    position: 'absolute',
    opacity: 0.6,
  },
  modalImageFull: { width: '100%', height: '100%' },
  modalTextContainer: { paddingVertical: 4 },
  modalTitle: {
    fontSize: 18,
    fontWeight: '800',
    color: '#0f172a',
    lineHeight: 24,
    marginBottom: 12,
  },
  modalDescription: {
    fontSize: 14,
    color: '#334155',
    lineHeight: 22,
    textAlign: 'justify',
  },
  modalActionButon: {
    backgroundColor: '#00AA5B',
    paddingVertical: 12,
    borderRadius: 12,
    alignItems: 'center',
    marginTop: 16,
  },
  modalActionButtonText: {
    color: '#ffffff',
    fontSize: 14,
    fontWeight: '700',
  },

  // CTA
  ctaSection: { paddingHorizontal: 16, marginTop: 6 },
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
    shadowOffset: { width: 0, height: 0 },
    shadowOpacity: 0.5,
    shadowRadius: 12,
    elevation: 8,
  },
  ctaLeft: { flex: 1 },
  ctaLabel: {
    fontSize: 11,
    color: '#00AA5B',
    fontWeight: '700',
    textTransform: 'capitalize',
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
    shadowOffset: { width: 0, height: 0 },
    shadowOpacity: 0.6,
    shadowRadius: 10,
    elevation: 8,
  },
});