import React, { useRef, useState } from 'react';
import {
  View,
  Text,
  TouchableOpacity,
  StyleSheet,
  SafeAreaView,
  StatusBar,
  Dimensions,
  FlatList,
  ScrollView,
  Image,
} from 'react-native';
import { useRouter } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';

const { width, height } = Dimensions.get('window');

const SLIDES = ['intro', 'action'];



const KEUNGGULAN = [
  { icon: 'checkbox-outline', text: 'Simpel',    desc: 'Mudah dipahami' },
  { icon: 'create-outline',   text: 'Aplikatif', desc: 'Langsung diterapkan' },
  { icon: 'disc-outline',     text: 'Akurat',    desc: 'Berbasis sains' },
];

export default function OnboardingScreen() {
  const router = useRouter();
  const flatRef = useRef<FlatList>(null);
  const [activeIndex, setActiveIndex] = useState(0);

  const goNext = () => {
    flatRef.current?.scrollToIndex({ index: 1, animated: true });
    setActiveIndex(1);
  };

  const handleScroll = (e: any) => {
    const idx = Math.round(e.nativeEvent.contentOffset.x / width);
    setActiveIndex(idx);
  };

  const SlideIntro = () => (
    <View style={{ width }}>
      <ScrollView
        showsVerticalScrollIndicator={false}
        contentContainerStyle={{ paddingBottom: 40 }}
        bounces={false}
      >
        {/* Hero Image boarding - rasio 1600x1331 */}
        <View style={[styles.heroImageWrapper, { aspectRatio: 1600 / 1331 }]}>
          <Image
            source={require('../assets/images/boarding.webp')}
            style={styles.heroImageFull}
            resizeMode="cover"
          />
          <View style={styles.heroGradient} />
        </View>

        <View style={styles.contentArea}>

          {/* Headline */}
          <View>
            <Text style={styles.headline}>Apa Itu{'\n'}
              <Text style={styles.headlineAccent}>STIFIn?</Text>
            </Text>
            <View style={styles.headlineUnderline} />
          </View>

          {/* Body */}
          <Text style={styles.body}>
            <Text style={styles.bold}>STIFIn</Text> mengidentifikasi{' '}
            <Text style={styles.bold}>mesin kecerdasan</Text> dan karakter manusia
            berdasarkan sistem operasi otak dominan, melalui pemindaian sidik jari
            yang disebut <Text style={{ color: '#00AA5B', fontWeight: '700' }}>Tes STIFIn</Text>.
          </Text>

          {/* Stat bar */}
          <View style={styles.statRow}>
            <View style={styles.statItem}>
              <Text style={styles.statNumber}>5</Text>
              <Text style={styles.statLabel}>Mesin Kecerdasan</Text>
            </View>
            <View style={styles.statDivider} />
            <View style={styles.statItem}>
              <Text style={styles.statNumber}>1'</Text>
              <Text style={styles.statLabel}>Cukup 1 Menit</Text>
            </View>
            <View style={styles.statDivider} />
            <View style={styles.statItem}>
              <Text style={styles.statNumber}>∞</Text>
              <Text style={styles.statLabel}>Seumur Hidup</Text>
            </View>
          </View>

          {/* Keunggulan grid */}
          <View style={styles.keunggulanWrap}>
            <View style={styles.keunggulanHeader}>
              <View style={styles.keunggulanHeaderDot} />
              <Text style={styles.sectionLabel}>KEUNGGULAN METODE STIFIn</Text>
            </View>
            <View style={styles.keunggulanGrid}>
              {KEUNGGULAN.map((item) => (
                <View key={item.icon} style={styles.keunggulanCard}>
                  <View style={styles.keunggulanIconCircle}>
                    <Ionicons name={item.icon as any} size={20} color="#00AA5B" />
                  </View>
                  <Text style={styles.keunggulanCardTitle}>{item.text}</Text>
                  <Text style={styles.keunggulanCardDesc}>{item.desc}</Text>
                </View>
              ))}
            </View>
          </View>

          {/* CTA Button */}
          <TouchableOpacity style={styles.primaryBtn} onPress={goNext} activeOpacity={0.85}>
            <Text style={styles.primaryBtnText}>Mulai Sekarang</Text>
            <View style={styles.primaryBtnIcon}>
              <Ionicons name="arrow-forward" size={16} color="#00AA5B" />
            </View>
          </TouchableOpacity>

        </View>
      </ScrollView>
    </View>
  );

  const SlideAction = () => (
    <View style={{ width }}>
      <ScrollView
        showsVerticalScrollIndicator={false}
        contentContainerStyle={{ paddingBottom: 40 }}
        bounces={false}
      >
        {/* Hero Image promotor - rasio 640x640 (1:1) */}
        <View style={[styles.heroImageWrapper, { aspectRatio: 1 / 1 }]}>
          <Image
            source={require('../assets/images/promotor.jpeg')}
            style={styles.heroImageFull}
            resizeMode="cover"
          />
          <View style={styles.heroGradient} />
        </View>

        <View style={styles.contentArea}>

          <View>
            <Text style={styles.headline}>Siap{' '}
              <Text style={styles.headlineAccent}>Memulai?</Text>
            </Text>
            <View style={styles.headlineUnderline} />
          </View>

          <Text style={styles.body}>
            Daftar dan ikuti <Text style={styles.bold}>Tes STIFIn</Text> untuk menemukan
            mesin kecerdasan, karakter, dan potensi terbaikmu.
          </Text>

          <TouchableOpacity
            style={styles.primaryBtn}
            onPress={() => router.push('/register')}
            activeOpacity={0.85}
          >
            <Text style={styles.primaryBtnText}>Daftar Sekarang</Text>
            <View style={styles.primaryBtnIcon}>
              <Ionicons name="arrow-forward" size={16} color="#00AA5B" />
            </View>
          </TouchableOpacity>

          <View style={styles.dividerRow}>
            <View style={styles.dividerLine} />
            <Text style={styles.dividerText}>atau</Text>
            <View style={styles.dividerLine} />
          </View>

          <TouchableOpacity
            style={styles.secondaryBtn}
            onPress={() => router.replace('/login')}
            activeOpacity={0.75}
          >
            <Text style={styles.secondaryBtnText}>Sudah punya akun?</Text>
            <Text style={styles.secondaryBtnBold}> Masuk →</Text>
          </TouchableOpacity>

        </View>
      </ScrollView>
    </View>
  );

  return (
    <View style={styles.container}>
      <StatusBar barStyle="dark-content" backgroundColor="#fff" />
      <SafeAreaView style={{ backgroundColor: '#fff' }} />

      <FlatList
        ref={flatRef}
        data={SLIDES}
        keyExtractor={(item) => item}
        horizontal
        pagingEnabled
        showsHorizontalScrollIndicator={false}
        onScroll={handleScroll}
        scrollEventThrottle={16}
        renderItem={({ item }) =>
          item === 'intro' ? <SlideIntro /> : <SlideAction />
        }
        style={{ flex: 1 }}
      />

      <View style={styles.dotsWrap}>
        {SLIDES.map((_, i) => (
          <View key={i} style={[styles.dot, activeIndex === i && styles.dotActive]} />
        ))}
      </View>

      <SafeAreaView style={{ backgroundColor: '#fff' }} />
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
  },

  /* ── Hero Image ── */
  heroImageWrapper: {
    width: width,
    aspectRatio: 4 / 3,
    overflow: 'hidden',
    backgroundColor: '#0a0a0a',
    position: 'relative',
    alignSelf: 'stretch',
    marginLeft: 0,
    marginRight: 0,
  },
  heroImageBlur: {
    position: 'absolute',
    width: '100%',
    height: '100%',
    opacity: 0.6,
  },
  heroImageFull: {
    width: '100%',
    height: '100%',
  },
  heroGradient: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    height: 80,
    backgroundColor: 'rgba(255,255,255,0.0)',
    // Simulasi gradient dengan shadow
  },


  /* ── Content ── */
  contentArea: {
    paddingHorizontal: 22,
    paddingTop: 24,
    gap: 18,
  },



  /* ── Headline ── */
  headline: {
    fontSize: 30,
    fontWeight: '900',
    color: '#1a1a2e',
    lineHeight: 38,
    letterSpacing: -0.5,
  },
  headlineAccent: {
    color: '#00AA5B',
  },
  headlineUnderline: {
    width: 40,
    height: 4,
    backgroundColor: '#00AA5B',
    borderRadius: 2,
    marginTop: 8,
  },

  /* ── Body ── */
  body: {
    fontSize: 14,
    color: '#546e7a',
    lineHeight: 24,
  },
  bold: {
    fontWeight: '700',
    color: '#37474f',
  },

  /* ── Stat Bar ── */
  statRow: {
    flexDirection: 'row',
    backgroundColor: '#f8fffe',
    borderRadius: 16,
    borderWidth: 1,
    borderColor: '#e0f2e9',
    paddingVertical: 14,
    paddingHorizontal: 8,
  },
  statItem: {
    flex: 1,
    alignItems: 'center',
    gap: 3,
  },
  statNumber: {
    fontSize: 22,
    fontWeight: '900',
    color: '#00AA5B',
  },
  statLabel: {
    fontSize: 10,
    color: '#78909c',
    fontWeight: '600',
    textAlign: 'center',
  },
  statDivider: {
    width: 1,
    backgroundColor: '#e0f2e9',
    marginVertical: 4,
  },

  /* ── Keunggulan ── */
  keunggulanWrap: {
    backgroundColor: '#f8fffe',
    borderRadius: 18,
    padding: 16,
    borderWidth: 1,
    borderColor: '#e0f2e9',
  },
  keunggulanHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 7,
    marginBottom: 14,
  },
  keunggulanHeaderDot: {
    width: 4,
    height: 16,
    backgroundColor: '#00AA5B',
    borderRadius: 2,
  },
  sectionLabel: {
    fontSize: 11,
    fontWeight: '800',
    color: '#1a1a2e',
    letterSpacing: 0.8,
  },
  keunggulanGrid: {
    flexDirection: 'row',
    gap: 10,
  },
  keunggulanCard: {
    flex: 1,
    backgroundColor: '#fff',
    borderRadius: 12,
    padding: 12,
    alignItems: 'center',
    gap: 6,
    borderWidth: 1,
    borderColor: '#e8f5e9',
    shadowColor: '#00AA5B',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.06,
    shadowRadius: 6,
    elevation: 2,
  },
  keunggulanIconCircle: {
    width: 40,
    height: 40,
    borderRadius: 20,
    backgroundColor: '#e8f5e9',
    justifyContent: 'center',
    alignItems: 'center',
  },
  keunggulanCardTitle: {
    fontSize: 13,
    fontWeight: '800',
    color: '#1a1a2e',
    textAlign: 'center',
  },
  keunggulanCardDesc: {
    fontSize: 10,
    color: '#90a4ae',
    fontWeight: '500',
    textAlign: 'center',
    lineHeight: 14,
  },

  /* ── Buttons ── */
  primaryBtn: {
    backgroundColor: '#00AA5B',
    paddingVertical: 16,
    paddingHorizontal: 24,
    borderRadius: 16,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    shadowColor: '#00AA5B',
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.3,
    shadowRadius: 10,
    elevation: 6,
    gap: 10,
  },
  primaryBtnText: {
    color: '#fff',
    fontWeight: '800',
    fontSize: 15,
    letterSpacing: 0.3,
  },
  primaryBtnIcon: {
    backgroundColor: '#fff',
    width: 26,
    height: 26,
    borderRadius: 13,
    justifyContent: 'center',
    alignItems: 'center',
  },

  /* ── Divider ── */
  dividerRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 10,
  },
  dividerLine: {
    flex: 1,
    height: 1,
    backgroundColor: '#e8f0eb',
  },
  dividerText: {
    fontSize: 12,
    color: '#b0bec5',
    fontWeight: '500',
  },

  secondaryBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 4,
  },
  secondaryBtnText: {
    fontSize: 14,
    color: '#90a4ae',
  },
  secondaryBtnBold: {
    fontSize: 14,
    color: '#00AA5B',
    fontWeight: '800',
  },

  /* ── Dots ── */
  dotsWrap: {
    flexDirection: 'row',
    justifyContent: 'center',
    gap: 6,
    paddingVertical: 14,
    backgroundColor: '#fff',
  },
  dot: {
    width: 6,
    height: 6,
    borderRadius: 3,
    backgroundColor: '#c8e6c9',
  },
  dotActive: {
    width: 22,
    borderRadius: 3,
    backgroundColor: '#00AA5B',
  },
});