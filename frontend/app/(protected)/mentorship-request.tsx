import React, { useState } from 'react';
import {
  View, Text, TextInput, Pressable, ScrollView,
  KeyboardAvoidingView, Platform, ActivityIndicator, Alert, Modal, FlatList,
} from 'react-native';
import { useLocalSearchParams, useRouter } from 'expo-router';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Feather, MaterialCommunityIcons } from '@expo/vector-icons';
import { useAuth } from '@/context/AuthContext';
import { getUserToken } from '@/hooks/useAuth';
import { sendMentorshipRequest } from '@/services/api';
import { StatusBar } from 'expo-status-bar';
import Toast from 'react-native-toast-message';

// ─── Helpers ─────────────────────────────────────────────────────────────────

/** Build the next 14 selectable dates (today + 13 days) */
const buildDateOptions = (): { label: string; value: string }[] => {
  const options = [];
  for (let i = 0; i < 14; i++) {
    const d = new Date();
    d.setDate(d.getDate() + i);
    const value = d.toISOString().split('T')[0]; // "YYYY-MM-DD"
    const label = d.toLocaleDateString('en-US', {
      weekday: 'short', month: 'short', day: 'numeric',
    });
    options.push({ label, value });
  }
  return options;
};

/** Build half-hour time slots between two HH:MM strings */
const buildTimeSlots = (from = '06:00', to = '20:00'): string[] => {
  const slots: string[] = [];
  const [fH, fM] = from.split(':').map(Number);
  const [tH, tM] = to.split(':').map(Number);
  let current = fH * 60 + fM;
  const end = tH * 60 + tM;
  while (current <= end) {
    const h = Math.floor(current / 60);
    const m = current % 60;
    const label = `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}`;
    slots.push(label);
    current += 30;
  }
  return slots;
};

const formatTimeDisplay = (t: string) => {
  const [h, m] = t.split(':').map(Number);
  const ampm = h >= 12 ? 'PM' : 'AM';
  const hour = h % 12 || 12;
  return `${hour}:${String(m).padStart(2, '0')} ${ampm}`;
};

// ─── Picker Modal ─────────────────────────────────────────────────────────────

interface PickerModalProps {
  visible: boolean;
  title: string;
  options: { label: string; value: string }[];
  selected: string;
  onSelect: (value: string) => void;
  onClose: () => void;
}

const PickerModal: React.FC<PickerModalProps> = ({
  visible, title, options, selected, onSelect, onClose,
}) => (
  <Modal visible={visible} transparent animationType="slide">
    <Pressable className="flex-1 bg-black/40" onPress={onClose} />
    <View className="bg-white rounded-t-3xl px-6 pt-4 pb-8 max-h-96">
      <View className="flex-row justify-between items-center mb-4">
        <Text className="text-slate-900 font-bold text-base">{title}</Text>
        <Pressable onPress={onClose}>
          <Feather name="x" size={20} color="#64748B" />
        </Pressable>
      </View>
      <FlatList
        data={options}
        keyExtractor={(item) => item.value}
        renderItem={({ item }) => (
          <Pressable
            onPress={() => { onSelect(item.value); onClose(); }}
            className={`py-3 px-4 rounded-xl mb-1 flex-row justify-between items-center ${
              selected === item.value ? 'bg-purple-50' : ''
            }`}
          >
            <Text className={`font-medium ${
              selected === item.value ? 'text-purple-700' : 'text-slate-700'
            }`}>
              {item.label}
            </Text>
            {selected === item.value && (
              <Feather name="check" size={16} color="#7C3AED" />
            )}
          </Pressable>
        )}
      />
    </View>
  </Modal>
);

// ─── Main Screen ──────────────────────────────────────────────────────────────

const MentorshipRequestScreen = () => {
  const { mentorId, mentorName, availableTimeStart, availableTimeEnd } = useLocalSearchParams<{
    mentorId: string;
    mentorName: string;
    availableTimeStart?: string;
    availableTimeEnd?: string;
  }>();
  const router = useRouter();
  const { token } = useAuth();

  const [topic, setTopic] = useState('');
  const [message, setMessage] = useState('');
  const [selectedDate, setSelectedDate] = useState('');
  const [selectedTimeFrom, setSelectedTimeFrom] = useState('');
  const [selectedTimeTo, setSelectedTimeTo] = useState('');
  const [loading, setLoading] = useState(false);

  // Picker modals
  const [showDatePicker, setShowDatePicker] = useState(false);
  const [showFromPicker, setShowFromPicker] = useState(false);
  const [showToPicker, setShowToPicker] = useState(false);

  const dateOptions = buildDateOptions();

  // Respect the mentor's availability window if passed as params
  const mentorFrom = (availableTimeStart as string) || '06:00';
  const mentorTo = (availableTimeEnd as string) || '20:00';
  const allSlots = buildTimeSlots(mentorFrom, mentorTo);

  // "To" slots must be after the selected "from" slot
  const toSlots = selectedTimeFrom
    ? allSlots.filter((s) => s > selectedTimeFrom)
    : allSlots.slice(1);

  const handleSubmit = async () => {
    if (!topic.trim()) {
      Toast.show({ type: 'error', text1: 'Topic Required', text2: 'Please enter a session topic.' });
      return;
    }
    if (!selectedDate) {
      Toast.show({ type: 'error', text1: 'Date Required', text2: 'Please select a session date.' });
      return;
    }
    if (!selectedTimeFrom) {
      Toast.show({ type: 'error', text1: 'Start Time Required', text2: 'Please select a start time.' });
      return;
    }
    if (!selectedTimeTo) {
      Toast.show({ type: 'error', text1: 'End Time Required', text2: 'Please select an end time.' });
      return;
    }

    setLoading(true);
    try {
      const userToken = token ?? (await getUserToken());
      await sendMentorshipRequest(
        {
          mentor_id: Number(mentorId),
          topic,
          message: message || undefined,
          requested_date: selectedDate,
          requested_time_from: selectedTimeFrom,
          requested_time_to: selectedTimeTo,
        },
        userToken
      );

      Toast.show({
        type: 'success',
        text1: 'Request Sent!',
        text2: `We've notified ${mentorName} about your session request.`,
        visibilityTime: 3000,
      });
      router.back();
    } catch (error: any) {
      // Show the server's availability / conflict error directly
      const msg =
         error?.response?.data?.message ||   // axios error shape
    error?.message ||                    // our re-thrown error
    'Could not send request. Please try again.';

  Alert.alert('Not Available', msg);
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView className="flex-1 bg-white" edges={['top']}>
      <StatusBar style="dark" />

      {/* HEADER */}
      <View className="px-6 py-4 flex-row items-center border-b border-slate-50">
        <Pressable onPress={() => router.back()} className="mr-4">
          <Feather name="x" size={24} color="#1E293B" />
        </Pressable>
        <Text className="text-xl font-bold text-slate-900">Request Mentorship</Text>
      </View>

      <KeyboardAvoidingView
        behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
        className="flex-1"
      >
        <ScrollView className="flex-1 px-6 pt-6" showsVerticalScrollIndicator={false}>

          {/* MENTOR CARD */}
          <View className="bg-purple-50 p-5 rounded-3xl mb-8 flex-row items-center border border-purple-100">
            <View className="w-12 h-12 rounded-2xl bg-purple-600 items-center justify-center">
              <Text className="text-white font-bold text-lg">
                {mentorName?.charAt(0)}
              </Text>
            </View>
            <View className="ml-4">
              <Text className="text-purple-900 font-bold text-base">Session with {mentorName}</Text>
              <Text className="text-purple-600 text-xs font-medium">Verified Mentor</Text>
            </View>
          </View>

          {/* TOPIC */}
          <View className="mb-6">
            <Text className="text-slate-900 font-bold mb-2 ml-1">What do you want to learn? *</Text>
            <View className="bg-slate-50 rounded-2xl border border-slate-100 px-4 py-3 flex-row items-center">
              <Feather name="book-open" size={18} color="#8A4FFF" />
              <TextInput
                className="flex-1 ml-3 text-slate-800 font-medium"
                placeholder="e.g. Career Advice, Mental Health Support"
                value={topic}
                onChangeText={setTopic}
                placeholderTextColor="#94A3B8"
              />
            </View>
          </View>

          {/* DATE PICKER */}
          <View className="mb-6">
            <Text className="text-slate-900 font-bold mb-2 ml-1">Session Date *</Text>
            <Pressable
              onPress={() => setShowDatePicker(true)}
              className="bg-slate-50 rounded-2xl border border-slate-100 px-4 py-4 flex-row items-center justify-between"
            >
              <View className="flex-row items-center">
                <Feather name="calendar" size={18} color="#8A4FFF" />
                <Text className={`ml-3 font-medium ${selectedDate ? 'text-slate-800' : 'text-slate-400'}`}>
                  {selectedDate
                    ? dateOptions.find((d) => d.value === selectedDate)?.label ?? selectedDate
                    : 'Select a date'}
                </Text>
              </View>
              <Feather name="chevron-down" size={16} color="#94A3B8" />
            </Pressable>
          </View>

          {/* TIME ROW */}
          <View className="flex-row gap-3 mb-6">
            {/* FROM */}
            <View className="flex-1">
              <Text className="text-slate-900 font-bold mb-2 ml-1">Start Time *</Text>
              <Pressable
                onPress={() => setShowFromPicker(true)}
                className="bg-slate-50 rounded-2xl border border-slate-100 px-4 py-4 flex-row items-center justify-between"
              >
                <View className="flex-row items-center">
                  <Feather name="clock" size={16} color="#8A4FFF" />
                  <Text className={`ml-2 font-medium text-sm ${selectedTimeFrom ? 'text-slate-800' : 'text-slate-400'}`}>
                    {selectedTimeFrom ? formatTimeDisplay(selectedTimeFrom) : 'From'}
                  </Text>
                </View>
                <Feather name="chevron-down" size={14} color="#94A3B8" />
              </Pressable>
            </View>

            {/* TO */}
            <View className="flex-1">
              <Text className="text-slate-900 font-bold mb-2 ml-1">End Time *</Text>
              <Pressable
                onPress={() => {
                  if (!selectedTimeFrom) {
                    Toast.show({ type: 'info', text1: 'Pick a start time first.' });
                    return;
                  }
                  setShowToPicker(true);
                }}
                className="bg-slate-50 rounded-2xl border border-slate-100 px-4 py-4 flex-row items-center justify-between"
              >
                <View className="flex-row items-center">
                  <Feather name="clock" size={16} color="#8A4FFF" />
                  <Text className={`ml-2 font-medium text-sm ${selectedTimeTo ? 'text-slate-800' : 'text-slate-400'}`}>
                    {selectedTimeTo ? formatTimeDisplay(selectedTimeTo) : 'To'}
                  </Text>
                </View>
                <Feather name="chevron-down" size={14} color="#94A3B8" />
              </Pressable>
            </View>
          </View>

          {/* Mentor availability hint */}
          {(availableTimeStart || availableTimeEnd) && (
            <View className="bg-blue-50 px-4 py-3 rounded-2xl mb-6 flex-row items-center">
              <Feather name="info" size={14} color="#3B82F6" />
              <Text className="text-blue-600 text-xs ml-2">
                This mentor is available {formatTimeDisplay(mentorFrom)} – {formatTimeDisplay(mentorTo)}
              </Text>
            </View>
          )}

          {/* MESSAGE */}
          <View className="mb-8">
            <Text className="text-slate-900 font-bold mb-2 ml-1">Add a message (Optional)</Text>
            <View className="bg-slate-50 rounded-3xl border border-slate-100 px-4 py-4 min-h-[130px]">
              <TextInput
                className="flex-1 text-slate-800"
                placeholder="Tell the mentor about your goals for this session..."
                value={message}
                onChangeText={setMessage}
                multiline
                textAlignVertical="top"
                placeholderTextColor="#94A3B8"
              />
            </View>
          </View>

          <View className="bg-slate-50 p-4 rounded-2xl flex-row items-start mb-10">
            <Feather name="info" size={16} color="#64748B" style={{ marginTop: 2 }} />
            <Text className="text-slate-500 text-xs ml-3 leading-4">
              Mentors usually respond within 24–48 hours. If the time slot is already booked you'll be notified immediately.
            </Text>
          </View>
        </ScrollView>

        {/* SUBMIT */}
        <View className="px-6 pb-10 pt-3 border-t border-slate-50 bg-white">
          <Pressable
            onPress={handleSubmit}
            disabled={loading}
            className={`h-16 rounded-2xl flex-row items-center justify-center shadow-lg ${
              loading ? 'bg-slate-200' : 'bg-purple-600 shadow-purple-200'
            }`}
          >
            {loading ? (
              <ActivityIndicator color="white" />
            ) : (
              <>
                <Text className="text-white font-bold text-lg mr-2">Send Request</Text>
                <MaterialCommunityIcons name="send" size={20} color="white" />
              </>
            )}
          </Pressable>
        </View>
      </KeyboardAvoidingView>

      {/* ── Modals ── */}
      <PickerModal
        visible={showDatePicker}
        title="Select Date"
        options={dateOptions}
        selected={selectedDate}
        onSelect={setSelectedDate}
        onClose={() => setShowDatePicker(false)}
      />
      <PickerModal
        visible={showFromPicker}
        title="Select Start Time"
        options={allSlots.map((s) => ({ label: formatTimeDisplay(s), value: s }))}
        selected={selectedTimeFrom}
        onSelect={(v) => {
          setSelectedTimeFrom(v);
          setSelectedTimeTo(''); // reset end time when start changes
        }}
        onClose={() => setShowFromPicker(false)}
      />
      <PickerModal
        visible={showToPicker}
        title="Select End Time"
        options={toSlots.map((s) => ({ label: formatTimeDisplay(s), value: s }))}
        selected={selectedTimeTo}
        onSelect={setSelectedTimeTo}
        onClose={() => setShowToPicker(false)}
      />
    </SafeAreaView>
  );
};

export default MentorshipRequestScreen;
