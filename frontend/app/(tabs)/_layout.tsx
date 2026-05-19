import React from 'react';
import { Tabs } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';

export default function TabLayout() {
  return (
    <Tabs screenOptions={{
      tabBarActiveTintColor: '#3b82f6',
      tabBarInactiveTintColor: '#94a3b8',
      tabBarStyle: {
        backgroundColor: '#ffffff',
        borderTopWidth: 1,
        borderTopColor: '#e2e8f0',
        height: 60,
        paddingBottom: 8,
        paddingTop: 8,
      },
      headerShown: false, // Kita buat header custom di masing-masing page
    }}>
      <Tabs.Screen
        name="index"
        options={{
          title: 'Home',
          tabBarIcon: ({ color }) => <Ionicons name="home-outline" size={24} color={color} />,
        }}
      />
       <Tabs.Screen
        name="home"
        options={{
          title: 'Pendaftaran',
          tabBarIcon: ({ color }) => <Ionicons name="create-outline" size={24} color={color} />,
        }}
      />


        
      <Tabs.Screen
        name="edit-profile"
        options={{
          title: 'Edit Profil',
          tabBarIcon: ({ color }) => <Ionicons name="person-outline" size={24} color={color} />,
        }}
      />
     
   
    </Tabs>
    
  );
}