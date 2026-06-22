import React, { useEffect, useState, useCallback } from 'react';
import {
  View,
  Text,
  Pressable,
  ActivityIndicator,
  RefreshControl,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { LegendList } from '@legendapp/list';
import { Feather } from '@expo/vector-icons';
import { StatusBar } from 'expo-status-bar';
import Toast from 'react-native-toast-message';
import { SimpleLineIcons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import {
  AppNotification,
  getNotifications,
  markAllNotificationsRead,
  markNotificationRead,
} from '../../services/api';

const NotificationsScreen = () => {
  const [notifications, setNotifications] = useState<AppNotification[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const router = useRouter();

  const fetchNotifications = useCallback(async (silent = false) => {
    if (!silent) setLoading(true);
    try {
      const response = await getNotifications();

      // ── Handle all common response shapes ──────────────────────────────────
      // Shape A: { notifications: [...] }
      // Shape B: { data: [...] }
      // Shape C: [...] (direct array)
      let data: AppNotification[] = [];

      if (Array.isArray(response)) {
        data = response;
      } else if (Array.isArray(response?.notifications)) {
        data = response.notifications;
      } else if (Array.isArray(response?.data)) {
        data = response.data;
      } else {
        // Log what actually came back so you can adjust the shape above
        console.warn('⚠️ Unexpected notifications response shape:', JSON.stringify(response));
        data = [];
      }

      setNotifications(data);
    } catch (err) {
      console.error('❌ fetchNotifications error:', err);
      Toast.show({
        type: 'error',
        text1: 'Error',
        text2: 'Could not fetch notifications',
        position: 'top',
      });
    } finally {
      // ── Always clear loading — this was the silent bug ───────────────────
      setLoading(false);
      setRefreshing(false);
    }
  }, []);

  useEffect(() => {
    fetchNotifications();
  }, [fetchNotifications]);

  const handleMarkRead = async (item: AppNotification) => {
    if (item.is_read) {
      if (item.report_id) router.push('/(protected)/myReportsScreen');
      return;
    }

    try {
      await markNotificationRead(item.id);
      setNotifications((prev) =>
        prev.map((n) => (n.id === item.id ? { ...n, is_read: true } : n))
      );

      if (
        item.report_id ||
        item.type?.includes('report') ||
        item.type?.includes('mentor')
      ) {
        router.push('/(protected)/myReportsScreen');
      }
    } catch {
      Toast.show({ type: 'error', text1: 'Could not mark as read', position: 'top' });
    }
  };

  const handleMarkAllRead = async () => {
    try {
      await markAllNotificationsRead();
      setNotifications((prev) => prev.map((n) => ({ ...n, is_read: true })));
      Toast.show({
        type: 'success',
        text1: 'All notifications marked as read',
        position: 'top',
      });
    } catch {
      Toast.show({ type: 'error', text1: 'Could not mark all as read', position: 'top' });
    }
  };

  const renderNotification = ({ item }: { item: AppNotification }) => (
    <Pressable
      onPress={() => handleMarkRead(item)}
      className={`p-4 mb-2 rounded-xl border ${
        item.is_read ? 'border-slate-200 bg-white' : 'border-violet-300 bg-violet-50'
      }`}
    >
      <Text
        className={`font-bold text-base ${
          item.is_read ? 'text-slate-900' : 'text-[#7c3aed]'
        }`}
      >
        {item.title}
      </Text>
      <Text className="text-slate-600 text-sm mt-1">{item.message}</Text>
      <Text className="text-slate-400 text-xs mt-1">
        {item.created_at ? new Date(item.created_at).toLocaleString() : ''}
      </Text>
    </Pressable>
  );

  if (loading && !refreshing) {
    return (
      <View className="flex-1 justify-center items-center bg-white">
        <ActivityIndicator size="large" color="#7c3aed" />
        <Text className="mt-4 text-slate-400 font-medium">
          Fetching notifications...
        </Text>
      </View>
    );
  }

  return (
    <SafeAreaView className="flex-1 bg-slate-50" edges={['top']}>
      <StatusBar style="dark" />

      {/* HEADER */}
      <View className="px-6 pt-4 pb-2 flex-row justify-between items-center bg-white border-b border-slate-100">
        <Pressable onPress={() => router.back()}>
          <SimpleLineIcons name="arrow-left" size={18} color="black" />
        </Pressable>
        <Text className="text-2xl font-black text-slate-900">Notifications</Text>
        <View className="flex-row gap-3">
          {notifications.some((n) => !n.is_read) ? (
            <Pressable onPress={handleMarkAllRead} className="p-2 active:opacity-50">
              <Feather name="check-circle" size={20} color="#7c3aed" />
            </Pressable>
          ) : (
            <View className="w-9" />
          )}
          <Pressable
            onPress={() => {
              setRefreshing(true);
              fetchNotifications(true);
            }}
            className="p-2 active:opacity-50"
          >
            <Feather name="refresh-cw" size={20} color="#7c3aed" />
          </Pressable>
        </View>
      </View>

      {/* LIST */}
      <LegendList
        data={notifications}
        keyExtractor={(item) => item.id.toString()}
        estimatedItemSize={80}
        contentContainerStyle={{ padding: 16, paddingBottom: 20 }}
        refreshControl={
          <RefreshControl
            refreshing={refreshing}
            onRefresh={() => {
              setRefreshing(true);
              fetchNotifications(true);
            }}
            tintColor="#7c3aed"
          />
        }
        renderItem={renderNotification}
        ListEmptyComponent={
          <View className="items-center mt-20 px-10">
            <Feather name="bell-off" size={40} color="#CBD5E1" />
            <Text className="text-slate-900 font-bold text-lg mt-4 text-center">
              No notifications
            </Text>
            <Text className="text-slate-400 text-center mt-2">
              Report updates and mentor responses will appear here.
            </Text>
          </View>
        }
      />
    </SafeAreaView>
  );
};

export default NotificationsScreen;