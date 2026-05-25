import React from 'react';
import { View, TouchableOpacity, StyleSheet, Platform } from 'react-native';
import { Tabs } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';

export default function TabLayout() {
  return (
    <Tabs
      screenOptions={{
        headerShown: false,
        tabBarActiveTintColor: '#16a34a',
        tabBarInactiveTintColor: '#9ca3af',
      }}
      tabBar={(props) => <CustomTabBar {...props} />}
    >
      <Tabs.Screen
        name="index"
        options={{
          title: 'Dokumen',
        }}
      />

      <Tabs.Screen
        name="center-page"
        options={{
          title: 'Home',
        }}
      />

      <Tabs.Screen
        name="home"
        options={{
          title: 'Edit',
        }}
      />

      <Tabs.Screen
        name="edit-profile"
        options={{
          title: 'Profil',
        }}
      />
    </Tabs>
  );
}

// ================= CUSTOM TAB BAR =================

const CustomTabBar = ({ state, navigation }: any) => {
  return (
    <View style={styles.wrapper}>
      <View style={styles.tabContainer}>
        {state.routes.map((route: any, index: number) => {
          const isFocused = state.index === index;

          // Tombol tengah
          const isCenter = index === 1;

          // ================= CENTER BUTTON =================
          if (isCenter) {
            return (
              <TouchableOpacity
                key={route.key}
                style={styles.centerButton}
                activeOpacity={0.8}
                onPress={() => navigation.navigate(route.name)}
              >
                <Ionicons
                  name="home-outline"
                  size={30}
                  color="#16a34a"
                />
              </TouchableOpacity>
            );
          }

          // ================= NORMAL BUTTON =================
          return (
            <TouchableOpacity
              key={route.key}
              style={styles.tabItem}
              activeOpacity={0.7}
              onPress={() => navigation.navigate(route.name)}
            >
              <Ionicons
                name={getIcon(route.name)}
                size={25}
                color={isFocused ? '#16a34a' : '#9ca3af'}
              />
            </TouchableOpacity>
          );
        })}
      </View>
    </View>
  );
};

// ================= ICON =================

const getIcon = (name: string) => {
  const icons: any = {
    // kiri
    index: 'document-text-outline',

    // kanan tengah
    home: 'create-outline',

    // kanan
    'edit-profile': 'person-outline',
  };

  return icons[name] || 'ellipse-outline';
};

// ================= STYLE =================

const styles = StyleSheet.create({
  wrapper: {
    backgroundColor: 'transparent',
  },

  tabContainer: {
    flexDirection: 'row',
    alignItems: 'center',

    backgroundColor: '#ffffff',

    height: Platform.OS === 'ios' ? 85 : 72,

    borderTopLeftRadius: 28,
    borderTopRightRadius: 28,

    paddingHorizontal: 20,

    elevation: 12,

    shadowColor: '#000',
    shadowOpacity: 0.08,
    shadowRadius: 10,
    shadowOffset: {
      width: 0,
      height: -2,
    },
  },

  tabItem: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },

  centerButton: {
    top: -24,

    width: 70,
    height: 70,
    borderRadius: 35,

    justifyContent: 'center',
    alignItems: 'center',

    backgroundColor: '#ffffff',

    borderWidth: 4,
    borderColor: '#dcfce7',

    marginHorizontal: 10,

    elevation: 10,

    shadowColor: '#16a34a',
    shadowOpacity: 0.2,
    shadowRadius: 8,
    shadowOffset: {
      width: 0,
      height: 4,
    },
  },
});