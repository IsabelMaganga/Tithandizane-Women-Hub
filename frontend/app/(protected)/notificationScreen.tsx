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
import { getUserToken } from '@/hooks/useAuth';
//import { getNotifications, markNotificationRead } from '@/services/api';
import { StatusBar } from 'expo-status-bar';
import Toast from 'react-native-toast-message';
import { SimpleLineIcons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';

type Notification = {
  id: number;
  title: string;
  message: string;
  created_at: string;
  read: boolean;
};

const NotificationsScreen = () => {
  const [notifications, setNotifications] = useState<Notification[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const router = useRouter()

  const fetchNotifications = useCallback(async (silent = false) => {
    if (!silent) setLoading(true);
    try {
      const token = await getUserToken();
      //const data = await getNotifications(token);
      //setNotifications(data);
    } catch (err) {
      console.error('Error fetching notifications:', err);
      Toast.show({
        type: 'error',
        text1: 'Error',
        text2: 'Could not fetch notifications',
      });
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, []);

  useEffect(() => {
    fetchNotifications();
  }, [fetchNotifications]);

  // const handleMarkRead = async (id: number) => {
  //   try {
  //     const token = await getUserToken();
  //     await markNotificationRead(id, token);
  //     setNotifications(prev =>
  //       prev.map(n => (n.id === id ? { ...n, read: true } : n))
  //     );
  //   } catch (err) {
  //     console.error('Mark read failed:', err);
  //     Toast.show({ type: 'error', text1: 'Could not mark as read' });
  //   }
  // };

  const renderNotification = ({ item }: { item: Notification }) => (
    <Pressable
      //onPress={() => handleMarkRead(item.id)}
      className={`p-4 mb-2 rounded-xl border ${
        item.read ? 'border-slate-200 bg-white' : 'border-purple-300 bg-purple-50'
      }`}
    >
      <Text className={`font-bold text-base ${item.read ? 'text-slate-900' : 'text-purple-600'}`}>
        {item.title}
      </Text>
      <Text className="text-slate-600 text-sm mt-1">{item.message}</Text>
      <Text className="text-slate-400 text-xs mt-1">{new Date(item.created_at).toLocaleString()}</Text>
    </Pressable>
  );

  if (loading && !refreshing) {
    return (
      <View className="flex-1 justify-center items-center bg-white">
        <ActivityIndicator size="large" color="#8A4FFF" />
        <Text className="mt-4 text-slate-400 font-medium">Fetching notifications...</Text>
      </View>
    );
  }

  return (
    <SafeAreaView className="flex-1 bg-slate-50" edges={['top']}>
      <StatusBar style="dark" />
      <View className="px-6 pt-4 pb-2 flex-row justify-between items-center bg-white border-b border-slate-100">
        <Pressable onPress={()=> router.back()}>
          <SimpleLineIcons name="arrow-left" size={18} color="black" />
        </Pressable>
        <Text className="text-2xl font-black text-slate-900">Notifications</Text>
        <Pressable onPress={() => fetchNotifications()} className="p-2 active:opacity-50">
          <Feather name="refresh-cw" size={20} color="#8A4FFF" />
        </Pressable>
      </View>

      <LegendList
        data={notifications}
        keyExtractor={item => item.id.toString()}
        estimatedItemSize={80}
        contentContainerStyle={{ padding: 16, paddingBottom: 20 }}
        refreshControl={
          <RefreshControl
            refreshing={refreshing}
            onRefresh={() => {
              setRefreshing(true);
              fetchNotifications(true);
            }}
            tintColor="#8A4FFF"
          />
        }
        renderItem={renderNotification}
        ListEmptyComponent={
          <View className="items-center mt-20 px-10">
            <Feather name="bell-off" size={40} color="#CBD5E1" />
            <Text className="text-slate-900 font-bold text-lg mt-4 text-center">No notifications</Text>
            <Text className="text-slate-400 text-center mt-2">
              You’re all caught up! Notifications will appear here when available.
            </Text>
          </View>
        }
      />
    </SafeAreaView>
  );
};

export default NotificationsScreen;