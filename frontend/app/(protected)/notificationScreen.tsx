import React, { useEffect, useState, useCallback } from 'react';
import {
  View, Text, Pressable, ActivityIndicator, RefreshControl, useWindowDimensions,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { StatusBar } from 'expo-status-bar';
import { LegendList } from '@legendapp/list';
import { Feather, Ionicons } from '@expo/vector-icons';
import { getUserToken } from '@/hooks/useAuth';
import Toast from 'react-native-toast-message';
import { useRouter } from 'expo-router';
import { useThemeToggle } from '../../hooks/useTheme';

type Notification = {
  id: number;
  title: string;
  message: string;
  created_at: string;
  read: boolean;
};

export default function NotificationsScreen() {
  const [notifications, setNotifications] = useState<Notification[]>([]);
  const [loading, setLoading]     = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const router = useRouter();
  const { colorScheme } = useThemeToggle();
  const { width } = useWindowDimensions();
  const isDark = colorScheme === 'dark';

  const T = {
    bg:       isDark ? '#0f172a' : '#f8fafc',
    card:     isDark ? '#1e293b' : '#ffffff',
    border:   isDark ? '#334155' : '#e2e8f0',
    text:     isDark ? '#f1f5f9' : '#0f172a',
    subtext:  isDark ? '#94a3b8' : '#64748b',
    unreadBg: isDark ? '#2d1b69' : '#f5f3ff',
    unreadBd: isDark ? '#5b21b6' : '#ddd6fe',
    header:   isDark ? '#1e293b' : '#ffffff',
  };

  const fetchNotifications = useCallback(async (silent = false) => {
    if (!silent) setLoading(true);
    try {
      const token = await getUserToken();
      // const data = await getNotifications(token);
      // setNotifications(data);
    } catch {
      Toast.show({ type: 'error', text1: 'Error', text2: 'Could not fetch notifications' });
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, []);

  useEffect(() => { fetchNotifications(); }, [fetchNotifications]);

  if (loading && !refreshing) {
    return (
      <View style={{ flex: 1, backgroundColor: T.bg, alignItems: 'center', justifyContent: 'center' }}>
        <StatusBar style={isDark ? 'light' : 'dark'} />
        <ActivityIndicator size="large" color="#7c3aed" />
        <Text style={{ color: T.subtext, marginTop: 12, fontWeight: '500' }}>Loading notifications…</Text>
      </View>
    );
  }

  return (
    <SafeAreaView style={{ flex: 1, backgroundColor: T.bg }} edges={['top']}>
      <StatusBar style={isDark ? 'light' : 'dark'} />

      {/* Header */}
      <View style={{
        flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between',
        paddingHorizontal: 20, paddingVertical: 14,
        backgroundColor: T.header,
        borderBottomWidth: 1, borderBottomColor: T.border,
        shadowColor: '#000', shadowOffset: { width: 0, height: 2 },
        shadowOpacity: isDark ? 0.3 : 0.05, shadowRadius: 4, elevation: 2,
      }}>
        <Pressable
          onPress={() => router.back()}
          style={({ pressed }) => ({
            width: 38, height: 38, borderRadius: 12,
            backgroundColor: isDark ? '#334155' : '#f1f5f9',
            alignItems: 'center', justifyContent: 'center',
            opacity: pressed ? 0.7 : 1,
          })}
        >
          <Feather name="arrow-left" size={18} color={T.text} />
        </Pressable>

        <Text style={{ fontSize: 18, fontWeight: '800', color: T.text }}>Notifications</Text>

        <Pressable
          onPress={() => fetchNotifications()}
          style={({ pressed }) => ({
            width: 38, height: 38, borderRadius: 12,
            backgroundColor: isDark ? '#334155' : '#f5f3ff',
            alignItems: 'center', justifyContent: 'center',
            opacity: pressed ? 0.7 : 1,
          })}
        >
          <Feather name="refresh-cw" size={16} color="#7c3aed" />
        </Pressable>
      </View>

      <LegendList
        data={notifications}
        keyExtractor={item => item.id.toString()}
        estimatedItemSize={90}
        contentContainerStyle={{ padding: 16, paddingBottom: 32 }}
        refreshControl={
          <RefreshControl
            refreshing={refreshing}
            onRefresh={() => { setRefreshing(true); fetchNotifications(true); }}
            tintColor="#7c3aed"
          />
        }
        renderItem={({ item }) => (
          <Pressable
            style={({ pressed }) => ({
              padding: 16, marginBottom: 10, borderRadius: 18,
              borderWidth: 1,
              backgroundColor: item.read ? T.card : T.unreadBg,
              borderColor: item.read ? T.border : T.unreadBd,
              opacity: pressed ? 0.85 : 1,
              shadowColor: '#000',
              shadowOffset: { width: 0, height: 2 },
              shadowOpacity: isDark ? 0.2 : 0.05, shadowRadius: 6, elevation: 1,
            })}
          >
            <View style={{ flexDirection: 'row', alignItems: 'flex-start', gap: 12 }}>
              <View style={{
                width: 40, height: 40, borderRadius: 12,
                backgroundColor: item.read
                  ? (isDark ? '#334155' : '#f1f5f9')
                  : (isDark ? '#4c1d95' : '#ede9fe'),
                alignItems: 'center', justifyContent: 'center',
              }}>
                <Ionicons
                  name={item.read ? 'notifications-outline' : 'notifications'}
                  size={20}
                  color={item.read ? T.subtext : '#7c3aed'}
                />
              </View>
              <View style={{ flex: 1 }}>
                <Text style={{
                  fontSize: 14, fontWeight: item.read ? '600' : '800',
                  color: item.read ? T.text : '#7c3aed', marginBottom: 3,
                }}>
                  {item.title}
                </Text>
                <Text style={{ color: T.subtext, fontSize: 13, lineHeight: 18 }}>{item.message}</Text>
                <Text style={{ color: T.subtext, fontSize: 11, marginTop: 6, opacity: 0.7 }}>
                  {new Date(item.created_at).toLocaleString()}
                </Text>
              </View>
              {!item.read && (
                <View style={{ width: 8, height: 8, borderRadius: 4, backgroundColor: '#7c3aed', marginTop: 6 }} />
              )}
            </View>
          </Pressable>
        )}
        ListEmptyComponent={
          <View style={{ alignItems: 'center', marginTop: 80, paddingHorizontal: 40 }}>
            <View style={{
              width: 80, height: 80, borderRadius: 40,
              backgroundColor: isDark ? '#1e293b' : '#f1f5f9',
              alignItems: 'center', justifyContent: 'center', marginBottom: 16,
            }}>
              <Feather name="bell-off" size={36} color={isDark ? '#475569' : '#cbd5e1'} />
            </View>
            <Text style={{ color: T.text, fontWeight: '700', fontSize: 17, textAlign: 'center' }}>
              No notifications
            </Text>
            <Text style={{ color: T.subtext, textAlign: 'center', marginTop: 8, lineHeight: 20, fontSize: 14 }}>
              {`You're all caught up! Notifications will appear here when available.`}
            </Text>
          </View>
        }
      />
    </SafeAreaView>
  );
}
