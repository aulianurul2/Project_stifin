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

const { width } = Dimensions.get('window');
const SLIDES = ['intro', 'kenali', 'action'];

const KEUNGGULAN = [
  { icon: 'checkbox-outline', text: 'Simpel',    desc: 'Mudah dipahami' },
  { icon: 'create-outline',   text: 'Aplikatif', desc: 'Langsung diterapkan' },
  { icon: 'disc-outline',     text: 'Akurat',    desc: 'Berbasis sains' },
];

const FEATURES = [
  { icon: 'sparkles-outline',  label: 'Bakat Alami',       desc: 'Kenali potensi bawaan yang unik' },
  { icon: 'book-outline',      label: 'Gaya Belajar',      desc: 'Cara belajar paling efektif untukmu' },
  { icon: 'briefcase-outline', label: 'Karier Ideal',      desc: 'Arah karier sesuai kepribadianmu' },
  { icon: 'heart-outline',     label: 'Hubungan Harmonis', desc: 'Pahami dirimu untuk relasi lebih baik' },
];

export default function OnboardingScreen() {
  const router = useRouter();
  const flatRef = useRef<FlatList>(null);
  const [activeIndex, setActiveIndex] = useState(0);

  const goTo = (index: number) => {
    flatRef.current?.scrollToIndex({ index, animated: true });
    setActiveIndex(index);
  };

  const handleScroll = (e: any) => {
    const idx = Math.round(e.nativeEvent.contentOffset.x / width);
    setActiveIndex(idx);
  };

  const SlideIntro = () => (
    <View style={{ width }}>
      <ScrollView showsVerticalScrollIndicator={false} bounces={false} contentContainerStyle={{ paddingBottom: 36 }}>

        {/* Image */}
        <View style={styles.imageBox}>
          <Image source={require('../assets/images/boarding.webp')} style={styles.image} resizeMode="cover" />
        </View>

        <View style={styles.content}>
          <Text style={styles.headline}>Apa Itu <Text style={styles.accent}>STIFIn?</Text></Text>
          <View style={styles.underline} />

          <Text style={styles.body}>
            <Text style={styles.bold}>STIFIn</Text> mengidentifikasi{' '}
            <Text style={styles.bold}>mesin kecerdasan</Text> dan karakter manusia
            berdasarkan sistem operasi otak dominan, melalui pemindaian sidik jari
            yang disebut <Text style={styles.accent}>Tes STIFIn</Text>.
          </Text>

          {/* Stat row */}
          <View style={styles.statRow}>
            {[
              { num: '5',  label: 'Mesin\nKecerdasan' },
              { num: '1\'', label: 'Cukup\n1 Menit' },
              { num: '∞',  label: 'Berlaku\nSeumur Hidup' },
            ].map((s, i, arr) => (
              <React.Fragment key={s.num}>
                <View style={styles.statItem}>
                  <Text style={styles.statNum}>{s.num}</Text>
                  <Text style={styles.statLabel}>{s.label}</Text>
                </View>
                {i < arr.length - 1 && <View style={styles.statDiv} />}
              </React.Fragment>
            ))}
          </View>

          {/* Keunggulan */}
          <View style={styles.keunggulanBox}>
            <View style={styles.boxHeader}>
              <View style={styles.boxAccentBar} />
              <Text style={styles.boxTitle}>KEUNGGULAN METODE STIFIn</Text>
            </View>
            <View style={styles.keunggulanGrid}>
              {KEUNGGULAN.map((k) => (
                <View key={k.icon} style={styles.keunggulanCard}>
                  <View style={styles.iconCircle}>
                    <Ionicons name={k.icon as any} size={18} color="#00AA5B" />
                  </View>
                  <Text style={styles.keunggulanTitle}>{k.text}</Text>
                  <Text style={styles.keunggulanDesc}>{k.desc}</Text>
                </View>
              ))}
            </View>
          </View>

          <TouchableOpacity style={styles.btn} onPress={() => goTo(1)} activeOpacity={0.85}>
            <Text style={styles.btnText}>Selanjutnya</Text>
            <View style={styles.btnIcon}><Ionicons name="arrow-forward" size={16} color="#00AA5B" /></View>
          </TouchableOpacity>

          <TouchableOpacity style={styles.skipRow} onPress={() => goTo(2)} activeOpacity={0.7}>
            <Text style={styles.skipText}>Lewati</Text>
            <Ionicons name="play-skip-forward-outline" size={12} color="#90a4ae" />
          </TouchableOpacity>
        </View>
      </ScrollView>
    </View>
  );

  const SlideKenali = () => (
    <View style={{ width }}>
      <ScrollView showsVerticalScrollIndicator={false} bounces={false} contentContainerStyle={{ paddingBottom: 36 }}>

        <View style={[styles.imageBox, { aspectRatio: 946 / 873 }]}>
          <Image source={require('../assets/images/board.jpeg')} style={styles.image} resizeMode="cover" />
        </View>

        <View style={styles.content}>
          <Text style={styles.headline}>Kenali Dirimu,{'\n'}<Text style={styles.accent}>Maksimalkan Potensimu.</Text></Text>
          <View style={styles.underline} />

          <Text style={styles.body}>
            Temukan bakat alami, gaya belajar, karier, dan hubungan yang lebih harmonis
            melalui <Text style={styles.accent}>Tes STIFIn</Text>.
          </Text>

          <View style={styles.featureList}>
            {FEATURES.map((f) => (
              <View key={f.label} style={styles.featureItem}>
                <View style={styles.iconCircle}>
                  <Ionicons name={f.icon as any} size={16} color="#00AA5B" />
                </View>
                <View style={{ flex: 1 }}>
                  <Text style={styles.featureLabel}>{f.label}</Text>
                  <Text style={styles.featureDesc}>{f.desc}</Text>
                </View>
              </View>
            ))}
          </View>

          <TouchableOpacity style={styles.btn} onPress={() => goTo(2)} activeOpacity={0.85}>
            <Text style={styles.btnText}>Selanjutnya</Text>
            <View style={styles.btnIcon}><Ionicons name="arrow-forward" size={16} color="#00AA5B" /></View>
          </TouchableOpacity>
        </View>
      </ScrollView>
    </View>
  );

  const SlideAction = () => (
    <View style={{ width }}>
      <ScrollView showsVerticalScrollIndicator={false} bounces={false} contentContainerStyle={{ paddingBottom: 36 }}>

        <View style={styles.imageBox}>
          <Image source={require('../assets/images/promotor.jpeg')} style={styles.image} resizeMode="cover" />
        </View>

        <View style={styles.content}>
          <Text style={styles.headline}>Siap <Text style={styles.accent}>Memulai?</Text></Text>
          <View style={styles.underline} />

          <Text style={styles.body}>
            Daftar dan ikuti <Text style={styles.bold}>Tes STIFIn</Text> untuk menemukan
            mesin kecerdasan, karakter, dan potensi terbaikmu.
          </Text>

          <TouchableOpacity style={styles.btn} onPress={() => router.push('/register')} activeOpacity={0.85}>
            <Text style={styles.btnText}>Daftar Sekarang</Text>
            <View style={styles.btnIcon}><Ionicons name="arrow-forward" size={16} color="#00AA5B" /></View>
          </TouchableOpacity>

          <View style={styles.divRow}>
            <View style={styles.divLine} />
            <Text style={styles.divText}>atau</Text>
            <View style={styles.divLine} />
          </View>

          <TouchableOpacity style={styles.secBtn} onPress={() => router.replace('/login')} activeOpacity={0.75}>
            <Text style={styles.secBtnText}>Sudah punya akun?</Text>
            <Text style={styles.secBtnBold}> Masuk →</Text>
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
        keyExtractor={(i) => i}
        horizontal
        pagingEnabled
        showsHorizontalScrollIndicator={false}
        onScroll={handleScroll}
        scrollEventThrottle={16}
        renderItem={({ item }) => {
          if (item === 'intro') return <SlideIntro />;
          if (item === 'kenali') return <SlideKenali />;
          return <SlideAction />;
        }}
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
  container: { flex: 1, backgroundColor: '#fff' },

  /* Image */
  imageBox: {
    width: '100%',
    aspectRatio: 1 / 1,
    overflow: 'hidden',
  },
  image: { width: '100%', height: '100%' },

  /* Skip */
  skipRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 4,
    paddingVertical: 6,
  },
  skipText: { fontSize: 12, color: '#90a4ae', fontWeight: '500' },

  /* Content */
  content: { paddingHorizontal: 22, paddingTop: 12, gap: 16 },

  headline: { fontSize: 28, fontWeight: '900', color: '#1a1a2e', lineHeight: 36, letterSpacing: -0.5 },
  accent: { color: '#00AA5B' },
  underline: { width: 40, height: 4, backgroundColor: '#00AA5B', borderRadius: 2 },

  body: { fontSize: 14, color: '#546e7a', lineHeight: 24 },
  bold: { fontWeight: '700', color: '#37474f' },

  /* Stat row */
  statRow: {
    flexDirection: 'row',
    backgroundColor: '#f8fffe',
    borderRadius: 16,
    borderWidth: 1,
    borderColor: '#e0f2e9',
    paddingVertical: 14,
  },
  statItem: { flex: 1, alignItems: 'center', gap: 3 },
  statNum: { fontSize: 22, fontWeight: '900', color: '#00AA5B' },
  statLabel: { fontSize: 10, color: '#78909c', fontWeight: '600', textAlign: 'center', lineHeight: 14 },
  statDiv: { width: 1, backgroundColor: '#e0f2e9', marginVertical: 4 },

  /* Keunggulan */
  keunggulanBox: {
    backgroundColor: '#f8fffe',
    borderRadius: 16,
    padding: 14,
    borderWidth: 1,
    borderColor: '#e0f2e9',
  },
  boxHeader: { flexDirection: 'row', alignItems: 'center', gap: 7, marginBottom: 12 },
  boxAccentBar: { width: 4, height: 14, backgroundColor: '#00AA5B', borderRadius: 2 },
  boxTitle: { fontSize: 10, fontWeight: '800', color: '#1a1a2e', letterSpacing: 0.8 },
  keunggulanGrid: { flexDirection: 'row', gap: 8 },
  keunggulanCard: {
    flex: 1,
    backgroundColor: '#fff',
    borderRadius: 12,
    padding: 10,
    alignItems: 'center',
    gap: 5,
    borderWidth: 1,
    borderColor: '#e8f5e9',
  },
  keunggulanTitle: { fontSize: 12, fontWeight: '800', color: '#1a1a2e', textAlign: 'center' },
  keunggulanDesc: { fontSize: 9, color: '#90a4ae', fontWeight: '500', textAlign: 'center', lineHeight: 13 },

  /* Feature list */
  featureList: { gap: 8 },
  featureItem: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 12,
    backgroundColor: '#f8fffe',
    borderRadius: 12,
    padding: 12,
    borderWidth: 1,
    borderColor: '#e0f2e9',
  },
  featureLabel: { fontSize: 13, fontWeight: '800', color: '#1a1a2e', marginBottom: 1 },
  featureDesc: { fontSize: 11, color: '#90a4ae', fontWeight: '500' },

  /* Shared icon circle */
  iconCircle: {
    width: 36,
    height: 36,
    borderRadius: 18,
    backgroundColor: '#e8f5e9',
    justifyContent: 'center',
    alignItems: 'center',
  },

  /* Button */
  btn: {
    backgroundColor: '#00AA5B',
    paddingVertical: 15,
    paddingHorizontal: 24,
    borderRadius: 16,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 10,
    shadowColor: '#00AA5B',
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.28,
    shadowRadius: 10,
    elevation: 6,
  },
  btnText: { color: '#fff', fontWeight: '800', fontSize: 15, letterSpacing: 0.3 },
  btnIcon: {
    backgroundColor: '#fff',
    width: 26,
    height: 26,
    borderRadius: 13,
    justifyContent: 'center',
    alignItems: 'center',
  },

  /* Secondary */
  divRow: { flexDirection: 'row', alignItems: 'center', gap: 10 },
  divLine: { flex: 1, height: 1, backgroundColor: '#e8f0eb' },
  divText: { fontSize: 12, color: '#b0bec5', fontWeight: '500' },
  secBtn: { flexDirection: 'row', alignItems: 'center', justifyContent: 'center', paddingVertical: 4 },
  secBtnText: { fontSize: 14, color: '#90a4ae' },
  secBtnBold: { fontSize: 14, color: '#00AA5B', fontWeight: '800' },

  /* Dots */
  dotsWrap: {
    flexDirection: 'row',
    justifyContent: 'center',
    gap: 6,
    paddingVertical: 12,
    backgroundColor: '#fff',
  },
  dot: { width: 6, height: 6, borderRadius: 3, backgroundColor: '#c8e6c9' },
  dotActive: { width: 22, borderRadius: 3, backgroundColor: '#00AA5B' },
});