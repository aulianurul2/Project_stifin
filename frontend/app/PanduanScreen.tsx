import React, { Component } from 'react';
import { 
  View, 
  Text, 
  StyleSheet, 
  FlatList, 
  TouchableOpacity, 
  ActivityIndicator, 
  SafeAreaView 
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { router } from 'expo-router';
import axiosInstance from '@/src/api/axiosConfig';

interface PanduanItem {
  id: number;
  title: string;
  content: string;
  category: string;
  icon?: string;
}

interface Props {
  navigation: any;
}

interface State {
  data: PanduanItem[];
  isLoading: boolean;
  error: string | null;
}

class PanduanScreen extends Component<Props, State> {
  constructor(props: Props) {
    super(props);
    this.state = {
      data: [],
      isLoading: true,
      error: null
    };
  }

  componentDidMount() {
    this.fetchPanduan();
  }

  fetchPanduan = async () => {
    try {
      this.setState({ isLoading: true, error: null });
      
      const response = await axiosInstance.get('/panduan');
      const json = response.data;
      
      if (json.success) {
        this.setState({ data: json.data, isLoading: false });
      } else {
        this.setState({ error: 'Gagal memuat data panduan', isLoading: false });
      }
    } catch (e) {
      this.setState({ error: 'Tidak dapat terhubung ke server', isLoading: false });
    }
  };

  handleBack = () => {
    router.replace('/home');
  };
  

  renderItem = ({ item }: { item: PanduanItem }) => (
    <TouchableOpacity 
      style={styles.card}
      onPress={() => router.push({
        pathname: '/DetailPanduanScreen',
        params: { id: item.id }
      })}
    >
      <View style={styles.iconContainer}>
        <Text style={styles.iconText}>📖</Text>
      </View>
      <View style={styles.cardContent}>
        <Text style={styles.categoryText}>{item.category.toUpperCase()}</Text>
        <Text style={styles.titleText}>{item.title}</Text>
      </View>
      <Text style={styles.arrowText}>❯</Text>
    </TouchableOpacity>
  );

  render() {
    const { data, isLoading, error } = this.state;

    if (isLoading) {
      return (
        <View style={styles.center}>
          <ActivityIndicator size="large" color="#30b9d4" />
        </View>
      );
    }

    if (error) {
      return (
        <View style={styles.center}>
          <Text style={styles.errorText}>{error}</Text>
          <TouchableOpacity style={styles.buttonRetry} onPress={this.fetchPanduan}>
            <Text style={styles.buttonRetryText}>Coba Lagi</Text>
          </TouchableOpacity>
        </View>
      );
    }

    return (
      <SafeAreaView style={styles.container}>
        <View style={styles.header}>
          <TouchableOpacity onPress={this.handleBack} style={styles.backButton} activeOpacity={0.7}>
            <Ionicons name="arrow-back" size={24} color="#1a1a2e" />
          </TouchableOpacity>
          <Text style={styles.headerTitle}>Panduan Aplikasi</Text>
        </View>
        <FlatList
          data={data}
          keyExtractor={(item) => item.id.toString()}
          renderItem={this.renderItem}
          contentContainerStyle={styles.listContainer}
        />
      </SafeAreaView>
    );
  }
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f8f9fa' },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center', backgroundColor: '#f8f9fa' },
  header: { flexDirection: 'row', alignItems: 'center', paddingHorizontal: 16, paddingVertical: 14, backgroundColor: '#ffffff', borderBottomWidth: 1, borderBottomColor: '#e9ecef' },
  backButton: { padding: 4, marginRight: 12 },
  headerTitle: { fontSize: 20, fontWeight: 'bold', color: '#1a1a2e' },
  listContainer: { padding: 16 },
  card: { flexDirection: 'row', alignItems: 'center', backgroundColor: '#ffffff', borderRadius: 12, padding: 16, marginBottom: 12, shadowColor: '#000', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.05, shadowRadius: 4, elevation: 2 },
  iconContainer: { width: 45, height: 45, borderRadius: 10, backgroundColor: '#e6f7fa', justifyContent: 'center', alignItems: 'center' },
  iconText: { fontSize: 20 },
  cardContent: { flex: 1, marginLeft: 14 },
  categoryText: { fontSize: 11, fontWeight: 'bold', color: '#30b9d4', marginBottom: 2 },
  titleText: { fontSize: 15, fontWeight: '600', color: '#2d3748' },
  arrowText: { fontSize: 16, color: '#cbd5e0', marginLeft: 8 },
  errorText: { fontSize: 14, color: '#e53e3e', marginBottom: 12 },
  buttonRetry: { paddingVertical: 8, paddingHorizontal: 16, borderRadius: 6, backgroundColor: '#30b9d4' },
  buttonRetryText: { color: '#ffffff', fontWeight: '600' },
});

export default PanduanScreen;