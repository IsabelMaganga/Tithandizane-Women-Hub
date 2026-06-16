import React, { useState, useEffect } from 'react';
import {
  View, Text, Modal, Linking, Pressable, useWindowDimensions,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { StatusBar } from 'expo-status-bar';
import { getEmergencyContacts } from '@/services/api';
import { LegendList } from '@legendapp/list';
import { Ionicons, Feather, MaterialCommunityIcons } from '@expo/vector-icons';
import LottieView from 'lottie-react-native';
import { useThemeToggle } from '../../hooks/useTheme';
import { LinearGradient } from 'expo-linear-gradient';
import { useRouter } from 'expo-router';

type Helpline = { id: number; name: string; phone: string };

export default function EmergencyScreen() {
  const [helplines, setHelplines]             = useState<Helpline[]>([]);
  const [selectedContact, setSelectedContact] = useState<Helpline | null>(null);
  const [modalVisible, setModalVisible]       = useState(false);
  const [isLoading, setIsLoading]             = useState(false);
  const { colorScheme } = useThemeToggle();
  const { width } = useWindowDimensions();
  const router = useRouter();
  const isDark = colorScheme === 'dark';

  const isTablet = width >= 768;

  const T = {
    bg:      isDark ? '#0f172a' : '#f8fafc',
    card:    isDark ? '#1e293b' : '#ffffff',
    border:  isDark ? '#334155' : '#e2e8f0',
    text:    isDark ? '#f1f5f9' : '#0f172a',
    subtext: isDark ? '#94a3b8' : '#64748b',
    sheet:   isDark ? '#1e293b' : '#ffffff',
  };

  useEffect(() => { fetchHelplines(); }, []);

  const fetchHelplines = async () => {
    try {
      setIsLoading(true);
      const data = await getEmergencyContacts();
      setHelplines(data ?? []);
    } catch {
      console.log('Error fetching contacts');
    } finally {
      setIsLoading(false);
    }
  };

  const confirmCall = (contact: Helpline) => {
    setSelectedContact(contact);
    setModalVisible(true);
  };

  const makeCall = () => {
    if (selectedContact?.phone) Linking.openURL(`tel:${selectedContact.phone}`);
    setModalVisible(false);
  };

  if (isLoading) {
    return (
      <View style={{ flex: 1, backgroundColor: T.bg, alignItems: 'center', justifyContent: 'center' }}>
        <StatusBar style={isDark ? 'light' : 'dark'} />
        <LottieView
          source={require('../../assets/animations/loading.json')}
          autoPlay loop
          style={{ width: 180, height: 180 }}
        />
        <Text style={{ color: T.subtext, fontWeight: '500', marginTop: 8 }}>Loading contacts…</Text>
      </View>
    );
  }

  return (
    <View style={{ flex: 1, backgroundColor: T.bg }}>
      <StatusBar style="light" />

      {/* Header gradient */}
      <LinearGradient
        colors={['#7c3aed', '#6d28d9']}
        style={{ paddingBottom: 32, borderBottomLeftRadius: 36, borderBottomRightRadius: 36, overflow: 'hidden' }}
      >
        <SafeAreaView edges={['top']}>
          <View style={{ paddingHorizontal: 24, paddingTop: 8, paddingBottom: 8 }}>
            <View style={{ flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginBottom: 16 }}>
              <Pressable
                onPress={() => router.back()}
                style={{ width: 40, height: 40, borderRadius: 12, backgroundColor: 'rgba(255,255,255,0.2)', alignItems: 'center', justifyContent: 'center' }}
              >
                <Feather name="arrow-left" size={20} color="white" />
              </Pressable>
              <View style={{ backgroundColor: 'rgba(255,255,255,0.2)', borderRadius: 10, paddingHorizontal: 10, paddingVertical: 5 }}>
                <Text style={{ color: '#fff', fontSize: 11, fontWeight: '700', letterSpacing: 1 }}>EMERGENCY</Text>
              </View>
            </View>

            <View style={{ flexDirection: 'row', alignItems: 'center', gap: 14 }}>
              <View style={{ backgroundColor: 'rgba(255,255,255,0.2)', padding: 14, borderRadius: 20 }}>
                <MaterialCommunityIcons name="phone-alert" size={30} color="white" />
              </View>
              <View>
                <Text style={{ color: '#fff', fontSize: isTablet ? 28 : 24, fontWeight: '900' }}>Emergency</Text>
                <Text style={{ color: 'rgba(255,255,255,0.8)', fontSize: 13, marginTop: 2 }}>Help is just a call away</Text>
              </View>
            </View>
          </View>
        </SafeAreaView>
      </LinearGradient>

      {/* Safety notice */}
      <View style={{ marginHorizontal: 20, marginTop: 20, marginBottom: 8 }}>
        <View style={{
          backgroundColor: isDark ? '#2d1b69' : '#f5f3ff',
          borderRadius: 16, padding: 14,
          flexDirection: 'row', alignItems: 'center', gap: 10,
          borderWidth: 1, borderColor: isDark ? '#4c1d95' : '#ddd6fe',
        }}>
          <Ionicons name="shield-checkmark" size={18} color="#7c3aed" />
          <Text style={{ color: isDark ? '#a78bfa' : '#7c3aed', fontSize: 12, fontWeight: '600', flex: 1 }}>
            All calls are confidential. Reach out whenever you need help.
          </Text>
        </View>
      </View>

      <LegendList
        data={helplines}
        estimatedItemSize={90}
        keyExtractor={item => item.id.toString()}
        extraData={isDark}
        style={{ backgroundColor: T.bg }}
        contentContainerStyle={{ padding: 16, paddingBottom: 40 }}
        ListEmptyComponent={
          <View style={{ alignItems: 'center', marginTop: 60 }}>
            <View style={{
              width: 72, height: 72, borderRadius: 36,
              backgroundColor: isDark ? '#1e293b' : '#f1f5f9',
              alignItems: 'center', justifyContent: 'center', marginBottom: 14,
            }}>
              <Feather name="phone-off" size={32} color={isDark ? '#475569' : '#cbd5e1'} />
            </View>
            <Text style={{ color: T.subtext, textAlign: 'center', fontSize: 14 }}>No helpline contacts available at the moment.</Text>
          </View>
        }
        renderItem={({ item }) => (
          <Pressable
            onPress={() => confirmCall(item)}
            style={({ pressed }) => ({
              backgroundColor: T.card, padding: 18, marginBottom: 12, borderRadius: 24,
              flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between',
              borderWidth: 1, borderColor: T.border,
              shadowColor: '#7c3aed',
              shadowOffset: { width: 0, height: 3 }, shadowOpacity: isDark ? 0.15 : 0.08,
              shadowRadius: 8, elevation: 3,
              opacity: pressed ? 0.85 : 1,
            })}
          >
            <View style={{ flexDirection: 'row', alignItems: 'center', flex: 1 }}>
              <View style={{
                backgroundColor: isDark ? '#2d1b69' : '#f5f3ff',
                padding: 14, borderRadius: 18, marginRight: 14,
              }}>
                <Feather name="shield" size={22} color="#7c3aed" />
              </View>
              <View style={{ flex: 1 }}>
                <Text style={{ color: T.text, fontSize: 15, fontWeight: '700' }} numberOfLines={1}>{item.name}</Text>
                <Text style={{ color: T.subtext, fontSize: 13, marginTop: 3, fontWeight: '500' }}>{item.phone}</Text>
              </View>
            </View>

            <View style={{
              backgroundColor: '#10b981', width: 50, height: 50, borderRadius: 25,
              alignItems: 'center', justifyContent: 'center',
              shadowColor: '#10b981', shadowOffset: { width: 0, height: 4 },
              shadowOpacity: 0.35, shadowRadius: 8, elevation: 4,
            }}>
              <Ionicons name="call" size={22} color="white" />
            </View>
          </Pressable>
        )}
      />

      {/* Confirm call modal */}
      <Modal visible={modalVisible} transparent animationType="slide" onRequestClose={() => setModalVisible(false)}>
        <View style={{ flex: 1, justifyContent: 'flex-end', backgroundColor: 'rgba(0,0,0,0.55)' }}>
          <View style={{
            backgroundColor: T.sheet, padding: 28, borderTopLeftRadius: 36, borderTopRightRadius: 36,
            alignItems: 'center',
          }}>
            <View style={{ width: 40, height: 4, backgroundColor: T.border, borderRadius: 2, marginBottom: 24 }} />

            <View style={{ backgroundColor: isDark ? '#2d1b69' : '#f5f3ff', padding: 22, borderRadius: 999, marginBottom: 16 }}>
              <Ionicons name="alert-circle" size={44} color="#7c3aed" />
            </View>

            <Text style={{ color: T.text, fontSize: 20, fontWeight: '800', textAlign: 'center' }}>
              Call {selectedContact?.name}?
            </Text>
            <Text style={{ color: T.subtext, textAlign: 'center', marginTop: 8, marginBottom: 28, lineHeight: 20, paddingHorizontal: 16 }}>
              You are about to dial {selectedContact?.phone}. Standard call rates may apply.
            </Text>

            <View style={{ flexDirection: 'row', gap: 12, width: '100%' }}>
              <Pressable
                onPress={() => setModalVisible(false)}
                style={({ pressed }) => ({
                  flex: 1, paddingVertical: 16, borderRadius: 18,
                  backgroundColor: isDark ? '#334155' : '#f1f5f9',
                  alignItems: 'center', opacity: pressed ? 0.8 : 1,
                })}
              >
                <Text style={{ color: T.text, fontWeight: '700', fontSize: 16 }}>Cancel</Text>
              </Pressable>
              <Pressable
                onPress={makeCall}
                style={({ pressed }) => ({
                  flex: 1, paddingVertical: 16, borderRadius: 18,
                  backgroundColor: '#10b981',
                  alignItems: 'center', opacity: pressed ? 0.85 : 1,
                  shadowColor: '#10b981', shadowOffset: { width: 0, height: 4 },
                  shadowOpacity: 0.35, shadowRadius: 8, elevation: 4,
                })}
              >
                <Text style={{ color: 'white', fontWeight: '800', fontSize: 16 }}>Call Now</Text>
              </Pressable>
            </View>
          </View>
        </View>
      </Modal>
    </View>
  );
}
