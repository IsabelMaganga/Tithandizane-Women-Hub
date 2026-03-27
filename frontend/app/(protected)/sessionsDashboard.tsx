import React, { useEffect, useState, useCallback } from 'react';
import { 
  View, Text, Pressable, ScrollView, ActivityIndicator, 
  RefreshControl, TouchableOpacity, Modal 
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Feather, MaterialCommunityIcons, Ionicons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import { getMentorshipSessions, updateSessionStatus } from '@/services/api';
import { getUserToken } from '@/hooks/useAuth';
import Toast from 'react-native-toast-message';
import { StatusBar } from 'expo-status-bar';
import { useAuth } from '@/context/AuthContext';
import { BackButton } from '@/components/BackButton';
import Animated, { FadeInDown } from 'react-native-reanimated';
import AsyncStorage from '@react-native-async-storage/async-storage';

export default function MentorshipDashboard() {
  const router = useRouter();
  const { user, token, loading: authLoading } = useAuth();
  const [sessions, setSessions] = useState({ incoming: [], outgoing: [] });
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  
  const [tab, setTab] = useState<'incoming' | 'outgoing'>(
    user?.role === "mentor" ? 'incoming' : 'outgoing'
  );

  const fetchSessions = useCallback(async (isSilent = false) => {
    if (!isSilent) setLoading(true);
    try {
      const token = await getUserToken();
      const response = await getMentorshipSessions(token);
      setSessions({
        incoming: Array.isArray(response?.incoming) ? response.incoming : [],
        outgoing: Array.isArray(response?.outgoing) ? response.outgoing : []
      });
    } catch (err) {
      Toast.show({ type: 'error', text1: 'Sync Error' });
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, []);

  useEffect(() => {
    if (user) fetchSessions();
  }, [user, fetchSessions]);

  // FIXED: Argument order and Status mapping
  const handleStatusUpdate = async (id: number, status: 'accepted' | 'denied') => {
    try {
      const token = await getUserToken();
      if (!token) return;

      // Laravel expects 'declined', not 'denied'
      const apiStatus = status === 'denied' ? 'declined' : 'accepted';

      await updateSessionStatus(id.toString(), token, { 
        status: apiStatus 
      });

      Toast.show({ type: 'success', text1: `Session ${apiStatus}` });
      fetchSessions(true);
    } catch (err) {
      Toast.show({ type: 'error', text1: 'Update failed' });
    }
  };
  

  const currentData = tab === 'outgoing' ? sessions.outgoing : sessions.incoming;

  if (authLoading || (loading && !refreshing)) {
    return (
      <View className="flex-1 justify-center items-center bg-white">
        <ActivityIndicator size="large" color="#4f46e5" />
      </View>
    );
  }

  return (
    <SafeAreaView className="flex-1 bg-slate-50" edges={['top']}>
      <StatusBar style="dark" />
      
      <View className="px-6 py-4 bg-white flex-row justify-between items-center border-b border-slate-100">
        <BackButton />
        <View className="items-center">
          <Text className="text-xl font-black text-slate-900">Mentorship</Text>
          <Text className="text-slate-400 text-[10px] font-bold uppercase tracking-widest">Dashboard</Text>
        </View>
        <TouchableOpacity onPress={() => fetchSessions()} className="p-2 bg-slate-50 rounded-xl">
          <Feather name="refresh-cw" size={18} color="#4f46e5" />
        </TouchableOpacity>
      </View>

      <View className="flex-row p-1.5 bg-slate-200/40 mx-6 mt-6 rounded-3xl border border-slate-200/50">
        <TabButton title="My Requests" active={tab === 'outgoing'} onPress={() => setTab('outgoing')} count={sessions.outgoing.length} />
        {user?.role === "mentor" && (
          <TabButton title="Incoming" active={tab === 'incoming'} onPress={() => setTab('incoming')} count={sessions.incoming.length} />
        )}
      </View>

      <ScrollView
        className="flex-1 px-6 mt-4"
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); fetchSessions(true); }} tintColor="#4f46e5" />}
      >
        {currentData.length > 0 ? (
          currentData.map((item, index) => (
            <Animated.View key={item.id} entering={FadeInDown.delay(index * 100)}>
              <SessionCard
                session={item}
                isIncoming={tab === 'incoming'}
                onUpdate={handleStatusUpdate}
                onPress={() => router.push({
                  pathname: "/mentorshipRequestInfo",
                  params: {
                    sessionId: item.id.toString(),
                    topic: item.topic,
                    status: item.status,
                    userName: tab === 'incoming' ? item.mentee?.name : item.mentor?.name,
                    isIncoming: tab === 'incoming' ? 'true' : 'false',
                    date: item.created_at,
                    message: item.message
                  }
                })}
              />
            </Animated.View>
          ))
        ) : (
          <EmptyState tab={tab} />
        )}
        <View className="h-20" />
      </ScrollView>
    </SafeAreaView>
  );
}

// Sub-components (SessionCard, TabButton, EmptyState) remain same as your previous version 
// but ensure SessionCard modal uses the handleStatusUpdate correctly.

const SessionCard = ({ session, isIncoming, onUpdate, onPress }: any) => {
  const [showActions, setShowActions] = useState(false);
  const status = session.status || 'pending';
  const person = isIncoming ? session.mentee : session.mentor;

  const statusColors = {
    accepted: { text: 'text-emerald-600', bg: 'bg-emerald-50', border: 'border-emerald-100' },
    denied: { text: 'text-rose-600', bg: 'bg-rose-50', border: 'border-rose-100' },
    pending: { text: 'text-amber-600', bg: 'bg-amber-50', border: 'border-amber-100' }
  }[status as 'accepted' | 'denied' | 'pending'] || { text: 'text-slate-500', bg: 'bg-slate-50', border: 'border-slate-100' };

  return (
    <Pressable onPress={onPress} className="bg-white rounded-[32px] p-6 mb-4 border border-slate-100 shadow-sm active:opacity-95">
      <View className="flex-row justify-between items-start mb-4">
        <View className={`${statusColors.bg} ${statusColors.border} px-3 py-1 rounded-full border`}>
          <Text className={`${statusColors.text} text-[9px] font-black uppercase tracking-widest`}>{status}</Text>
        </View>
        <Text className="text-slate-300 text-[10px] font-bold">
          {session.created_at ? new Date(session.created_at).toLocaleDateString() : ''}
        </Text>
      </View>

      <Text className="text-slate-900 font-bold text-lg leading-6 mb-4">{session.topic}</Text>

      <View className="flex-row items-center justify-between pt-4 border-t border-slate-50">
        <View className="flex-row items-center">
          <View className="w-10 h-10 rounded-2xl bg-purple-100 items-center justify-center">
            <Text className="text-purple-600 font-bold">{person?.name?.charAt(0) || '?'}</Text>
          </View>
          <View className="ml-3">
            <Text className="text-slate-400 text-[8px] font-black uppercase tracking-tighter">{isIncoming ? 'Mentee' : 'Mentor'}</Text>
            <Text className="text-slate-800 font-bold text-sm">{person?.name || 'Unknown'}</Text>
          </View>
        </View>

        {isIncoming && status === 'pending' ? (
          <TouchableOpacity onPress={() => setShowActions(true)} className="bg-purple-600 px-4 py-2 rounded-xl">
            <Text className="text-white text-xs font-bold">Manage</Text>
          </TouchableOpacity>
        ) : (
          <Feather name="chevron-right" size={20} color="#CBD5E1" />
        )}
      </View>

      {/* Action Modal */}
      <Modal transparent visible={showActions} animationType="fade">
        <Pressable className="flex-1 bg-black/40 justify-center items-center" onPress={() => setShowActions(false)}>
          <View className="bg-white w-64 rounded-3xl p-2 shadow-2xl">
             <Text className="text-center py-4 font-black text-slate-400 text-[10px] uppercase">Update Request</Text>
             <TouchableOpacity onPress={() => { onUpdate(session.id, 'accepted'); setShowActions(false); }} className="flex-row items-center p-4 border-b border-slate-50"><Ionicons name="checkmark-circle" size={20} color="#10b981" /><Text className="ml-3 font-bold text-slate-700">Accept</Text></TouchableOpacity>
             <TouchableOpacity onPress={() => { setShowActions(false); onPress(); }} className="flex-row items-center p-4 border-b border-slate-50"><MaterialCommunityIcons name="eye-outline" size={20} color="#8A4FFF" /><Text className="ml-3 font-bold text-slate-700">View Full</Text></TouchableOpacity>
             <TouchableOpacity onPress={() => { onUpdate(session.id, 'denied'); setShowActions(false); }} className="flex-row items-center p-4"><Ionicons name="close-circle" size={20} color="#ef4444" /><Text className="ml-3 font-bold text-slate-700">Decline</Text></TouchableOpacity>
          </View>
        </Pressable>
      </Modal>
    </Pressable>
  );
};

const TabButton = ({ title, active, onPress, count }: any) => (
  <Pressable 
    onPress={onPress}
    className={`flex-1 py-3 rounded-2xl flex-row items-center justify-center ${active ? 'bg-white shadow-sm' : ''}`}
  >
    <Text className={`font-bold text-xs ${active ? 'text-purple-600' : 'text-slate-500'}`}>{title}</Text>
    {count > 0 && (
      <View className={`ml-2 px-1.5 py-0.5 rounded-full ${active ? 'bg-purple-100' : 'bg-slate-200'}`}>
        <Text className={`text-[9px] font-black ${active ? 'text-purple-600' : 'text-slate-500'}`}>{count}</Text>
      </View>
    )}
  </Pressable>
);

const EmptyState = ({ tab }: { tab: string }) => (
  <View className="items-center mt-20 opacity-50">
    <MaterialCommunityIcons name="calendar-multiselect" size={48} color="#CBD5E1" />
    <Text className="text-slate-900 font-bold text-lg mt-6">Nothing here yet</Text>
    <Text className="text-slate-400 text-center px-10 mt-2">
      {tab === 'outgoing' ? "Your sent requests will appear here." : "Received  requests will appear here."}
    </Text>
  </View>
);