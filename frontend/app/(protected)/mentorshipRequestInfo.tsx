import React, { useState } from 'react';
import {
  View, Text, ScrollView, TouchableOpacity, Share,
  TextInput, Alert, ActivityIndicator, KeyboardAvoidingView,
  Platform, Modal, FlatList, Pressable,
} from 'react-native';
import { useLocalSearchParams, useRouter } from 'expo-router';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Ionicons, Feather } from '@expo/vector-icons';
import { StatusBar } from 'expo-status-bar';
import { BackButton } from '@/components/BackButton';
import { LinearGradient } from 'expo-linear-gradient';
import { updateSessionStatus } from '@/services/api';
import { useAuth } from '@/context/AuthContext';

// ─── Helpers ─────────────────────────────────────────────────────────────────

const formatTimeDisplay = (t: string) => {
  const [h, m] = t.split(':').map(Number);
  const ampm = h >= 12 ? 'PM' : 'AM';
  const hour = h % 12 || 12;
  return `${hour}:${String(m).padStart(2, '0')} ${ampm}`;
};

const buildDateOptions = () => {
  const options = [];
  for (let i = 0; i < 30; i++) {
    const d = new Date();
    d.setDate(d.getDate() + i);
    const value = d.toISOString().split('T')[0];
    const label = d.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
    options.push({ label, value });
  }
  return options;
};

const buildTimeOptions = () => {
  const slots = [];
  for (let h = 6; h <= 20; h++) {
    for (const m of [0, 30]) {
      const value = `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}`;
      slots.push({ label: formatTimeDisplay(value), value });
    }
  }
  return slots;
};

// ─── Simple Picker Modal ──────────────────────────────────────────────────────

const PickerModal = ({
  visible, title, options, selected, onSelect, onClose,
}: {
  visible: boolean;
  title: string;
  options: { label: string; value: string }[];
  selected: string;
  onSelect: (v: string) => void;
  onClose: () => void;
}) => (
  <Modal visible={visible} transparent animationType="slide">
    <Pressable style={{ flex: 1, backgroundColor: 'rgba(0,0,0,0.4)' }} onPress={onClose} />
    <View style={{ backgroundColor: 'white', borderTopLeftRadius: 24, borderTopRightRadius: 24, padding: 24, maxHeight: 400 }}>
      <View style={{ flexDirection: 'row', justifyContent: 'space-between', marginBottom: 12 }}>
        <Text style={{ fontWeight: 'bold', fontSize: 16, color: '#0f172a' }}>{title}</Text>
        <Pressable onPress={onClose}><Feather name="x" size={20} color="#64748b" /></Pressable>
      </View>
      <FlatList
        data={options}
        keyExtractor={(item) => item.value}
        renderItem={({ item }) => (
          <Pressable
            onPress={() => { onSelect(item.value); onClose(); }}
            style={{
              padding: 12, borderRadius: 12, marginBottom: 4,
              backgroundColor: selected === item.value ? '#f5f3ff' : 'transparent',
              flexDirection: 'row', justifyContent: 'space-between',
            }}
          >
            <Text style={{ color: selected === item.value ? '#7c3aed' : '#334155', fontWeight: '500' }}>
              {item.label}
            </Text>
            {selected === item.value && <Feather name="check" size={16} color="#7c3aed" />}
          </Pressable>
        )}
      />
    </View>
  </Modal>
);

// ─── Main Screen ──────────────────────────────────────────────────────────────

const MentorshipRequestInfo = () => {
  const router = useRouter();
  const params = useLocalSearchParams();
  const { token } = useAuth();

  const sessionId   = params.sessionId as string;
  const isIncoming  = params.isIncoming === 'true';
  const status      = (params.status as string) || 'pending';
  const topic       = (params.topic as string) || 'Session Details';
  const userName    = (params.userName as string) || 'User';
  const reqDate     = params.requested_date as string | undefined;
  const reqTimeFrom = params.requested_time_from as string | undefined;
  const reqTimeTo   = params.requested_time_to as string | undefined;

  const [note, setNote]               = useState('');
  const [schedDate, setSchedDate]     = useState(reqDate ?? '');
  const [schedTime, setSchedTime]     = useState(reqTimeFrom ?? '');
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [showDateModal, setShowDateModal] = useState(false);
  const [showTimeModal, setShowTimeModal] = useState(false);

  const statusConfig = {
    accepted:  { color: '#10b981', bg: '#f0fdf4', icon: 'checkmark-circle-outline', label: 'Accepted' },
    declined:  { color: '#ef4444', bg: '#fef2f2', icon: 'close-circle-outline',     label: 'Declined' },
    pending:   { color: '#f59e0b', bg: '#fffbeb', icon: 'time-outline',              label: 'Pending Review' },
    missed:    { color: '#64748b', bg: '#f8fafc', icon: 'alert-circle-outline',      label: 'Missed' },
    completed: { color: '#6366f1', bg: '#eef2ff', icon: 'checkmark-done-outline',   label: 'Completed' },
  }[status] ?? { color: '#64748b', bg: '#f8fafc', icon: 'help-circle-outline', label: status };

  const onShare = async () => {
    await Share.share({ message: `Mentorship Session: ${topic}\nStatus: ${status}\nWith: ${userName}` });
  };

  const handleAction = async (action: 'accepted' | 'declined') => {
    if (!sessionId || !token) return;

    if (action === 'accepted' && (!schedDate || !schedTime)) {
      Alert.alert('Missing Info', 'Please set a scheduled date and time before accepting.');
      return;
    }

    Alert.alert(
      action === 'accepted' ? 'Accept Session' : 'Decline Session',
      action === 'accepted'
        ? `Confirm acceptance for ${schedDate} at ${formatTimeDisplay(schedTime)}?`
        : 'Are you sure you want to decline this request?',
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: action === 'accepted' ? 'Accept' : 'Decline',
          style: action === 'declined' ? 'destructive' : 'default',
          onPress: async () => {
            setIsSubmitting(true);
            try {
              await updateSessionStatus(sessionId, token, {
                status: action,
                mentor_notes: note || undefined,
                scheduled_at: action === 'accepted'
                  ? `${schedDate} ${schedTime}:00`
                  : undefined,
              });

              Alert.alert(
                'Done',
                action === 'accepted' ? 'Session accepted!' : 'Session declined.',
                [{ text: 'OK', onPress: () => router.back() }]
              );
            } catch (error: any) {
              const msg = error?.response?.data?.message || error?.message || 'Something went wrong.';
              Alert.alert('Error', msg);
            } finally {
              setIsSubmitting(false);
            }
          },
        },
      ]
    );
  };

  if (!sessionId) {
    return (
      <SafeAreaView style={{ flex: 1, backgroundColor: 'white', alignItems: 'center', justifyContent: 'center', padding: 24 }}>
        <Feather name="alert-circle" size={50} color="#CBD5E1" />
        <Text style={{ fontSize: 20, fontWeight: 'bold', color: '#0f172a', marginTop: 16 }}>Request Not Found</Text>
        <TouchableOpacity onPress={() => router.back()} style={{ marginTop: 24, backgroundColor: '#4f46e5', paddingHorizontal: 32, paddingVertical: 12, borderRadius: 16 }}>
          <Text style={{ color: 'white', fontWeight: 'bold' }}>Go Back</Text>
        </TouchableOpacity>
      </SafeAreaView>
    );
  }

  return (
    <SafeAreaView className="flex-1 bg-white" edges={['top']}>
      <StatusBar style="dark" />

      {/* HEADER */}
      <View className="px-6 py-4 flex-row justify-between items-center border-b border-slate-50">
        <BackButton />
        <Text className="text-[17px] font-bold text-slate-900">Request Details</Text>
        <TouchableOpacity onPress={onShare} className="w-10 h-10 items-center justify-center bg-slate-50 rounded-full">
          <Feather name="share" size={18} color="#64748B" />
        </TouchableOpacity>
      </View>

      <KeyboardAvoidingView behavior={Platform.OS === 'ios' ? 'padding' : undefined} className="flex-1">
        <ScrollView className="flex-1" showsVerticalScrollIndicator={false}>

          {/* HERO */}
          <LinearGradient colors={['#f8fafc', '#ffffff']} className="px-6 pt-8 pb-10 border-b border-slate-50">
            <View style={{ backgroundColor: statusConfig.bg }}
              className="self-start px-3 py-1 rounded-lg flex-row items-center mb-4 border border-slate-100">
              <Ionicons name={statusConfig.icon as any} size={14} color={statusConfig.color} />
              <Text style={{ color: statusConfig.color }} className="ml-1.5 font-bold text-[10px] uppercase tracking-widest">
                {statusConfig.label}
              </Text>
            </View>

            <Text className="text-2xl font-black text-slate-900 leading-tight mb-8">{topic}</Text>

            <View className="flex-row items-center bg-white p-4 rounded-2xl shadow-sm border border-slate-100">
              <View className="w-12 h-12 rounded-xl bg-indigo-600 items-center justify-center">
                <Text className="text-white font-bold text-lg">{userName.charAt(0)}</Text>
              </View>
              <View className="ml-4 flex-1">
                <Text className="text-slate-400 text-[10px] font-bold uppercase tracking-widest">
                  {isIncoming ? 'Mentee' : 'Mentor'}
                </Text>
                <Text className="text-slate-900 font-bold text-base">{userName}</Text>
              </View>
            </View>
          </LinearGradient>

          {/* INFO TILES */}
          <View className="flex-row px-6 mt-6">
            <View className="flex-1 bg-slate-50 p-4 rounded-2xl border border-slate-100 mr-2">
              <Feather name="calendar" size={16} color="#6366f1" />
              <Text className="text-slate-400 text-[9px] font-bold uppercase mt-2">Requested Date</Text>
              <Text className="text-slate-900 font-bold text-[13px]">
                {reqDate ?? 'Not set'}
              </Text>
            </View>
            <View className="flex-1 bg-slate-50 p-4 rounded-2xl border border-slate-100">
              <Feather name="clock" size={16} color="#6366f1" />
              <Text className="text-slate-400 text-[9px] font-bold uppercase mt-2">Requested Time</Text>
              <Text className="text-slate-900 font-bold text-[13px]">
                {reqTimeFrom && reqTimeTo
                  ? `${formatTimeDisplay(reqTimeFrom)} – ${formatTimeDisplay(reqTimeTo)}`
                  : 'Not set'}
              </Text>
            </View>
          </View>

          {/* MESSAGE */}
          <View className="px-6 mt-8">
            <Text className="text-slate-900 font-bold text-sm mb-3">Inquiry Message</Text>
            <View className="bg-slate-50 p-5 rounded-3xl border border-slate-100">
              <Text className="text-slate-600 leading-6 italic">
                "{params.message || 'No message provided.'}"
              </Text>
            </View>
          </View>

          {/* ACCEPT FORM — mentor only, pending sessions */}
          {isIncoming && status === 'pending' && (
            <View className="px-6 mt-8 mb-4">
              <Text className="text-slate-900 font-bold text-sm mb-4">Confirm Schedule & Notes</Text>
              <View className="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm">

                <TextInput
                  placeholder="Add a meeting link or personal note..."
                  value={note}
                  onChangeText={setNote}
                  multiline
                  className="bg-slate-50 rounded-xl p-4 text-slate-700 mb-4"
                  style={{ minHeight: 90, textAlignVertical: 'top' }}
                />

                {/* Date picker */}
                <TouchableOpacity
                  onPress={() => setShowDateModal(true)}
                  className="bg-slate-50 border border-slate-200 p-4 rounded-xl flex-row items-center justify-between mb-3"
                >
                  <View className="flex-row items-center">
                    <Feather name="calendar" size={14} color="#6366f1" />
                    <Text className="ml-2 text-slate-700 font-semibold text-xs">
                      {schedDate
                        ? new Date(schedDate).toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' })
                        : 'Set scheduled date'}
                    </Text>
                  </View>
                  <Feather name="chevron-down" size={14} color="#94a3b8" />
                </TouchableOpacity>

                {/* Time picker */}
                <TouchableOpacity
                  onPress={() => setShowTimeModal(true)}
                  className="bg-slate-50 border border-slate-200 p-4 rounded-xl flex-row items-center justify-between"
                >
                  <View className="flex-row items-center">
                    <Feather name="clock" size={14} color="#6366f1" />
                    <Text className="ml-2 text-slate-700 font-semibold text-xs">
                      {schedTime ? formatTimeDisplay(schedTime) : 'Set scheduled time'}
                    </Text>
                  </View>
                  <Feather name="chevron-down" size={14} color="#94a3b8" />
                </TouchableOpacity>
              </View>
            </View>
          )}

          <View className="h-28" />
        </ScrollView>
      </KeyboardAvoidingView>

      {/* FOOTER */}
      {isIncoming && status === 'pending' && (
        <View className="px-6 pt-4 pb-10 border-t border-slate-100 flex-row bg-white">
          <TouchableOpacity
            disabled={isSubmitting}
            className="flex-1 h-14 rounded-2xl items-center justify-center border border-slate-200 mr-4"
            onPress={() => handleAction('declined')}
          >
            <Text className="text-slate-500 font-bold">Decline</Text>
          </TouchableOpacity>
          <TouchableOpacity
            disabled={isSubmitting}
            className="flex-[2] bg-indigo-600 h-14 rounded-2xl items-center justify-center shadow-lg shadow-indigo-200"
            onPress={() => handleAction('accepted')}
          >
            {isSubmitting
              ? <ActivityIndicator color="white" />
              : <Text className="text-white font-bold text-base">Accept Request</Text>}
          </TouchableOpacity>
        </View>
      )}

      {/* Modals */}
      <PickerModal
        visible={showDateModal}
        title="Set Scheduled Date"
        options={buildDateOptions()}
        selected={schedDate}
        onSelect={setSchedDate}
        onClose={() => setShowDateModal(false)}
      />
      <PickerModal
        visible={showTimeModal}
        title="Set Scheduled Time"
        options={buildTimeOptions()}
        selected={schedTime}
        onSelect={setSchedTime}
        onClose={() => setShowTimeModal(false)}
      />
    </SafeAreaView>
  );
};

export default MentorshipRequestInfo;
