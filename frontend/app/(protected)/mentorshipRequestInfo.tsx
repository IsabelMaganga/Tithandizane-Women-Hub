import React, { useState, useEffect } from 'react';
import { 
  View, Text, ScrollView, TouchableOpacity, Share, 
  TextInput, Alert, ActivityIndicator, KeyboardAvoidingView, Platform 
} from 'react-native';
import { useLocalSearchParams, useRouter } from 'expo-router';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Ionicons, Feather, MaterialCommunityIcons } from '@expo/vector-icons';
import { StatusBar } from 'expo-status-bar';
import { BackButton } from '@/components/BackButton';
import { DateTimePickerAndroid } from '@react-native-community/datetimepicker';
import { LinearGradient } from 'expo-linear-gradient';
import { updateSessionStatus } from '@/services/api';
import { useAuth } from '@/context/AuthContext';

const MentorshipRequestInfo = () => {
  const router = useRouter();
  const params = useLocalSearchParams();
  const { token } = useAuth();

  // State Management
  const [note, setNote] = useState('');
  const [selectedDate, setSelectedDate] = useState<Date>(new Date());
  const [selectedTime, setSelectedTime] = useState<Date>(new Date());
  const [isSubmitting, setIsSubmitting] = useState(false);

  // Safely extract params
  const sessionId = params.sessionId as string;
  const isIncoming = params.isIncoming === 'true';
  const status = (params.status as string) || 'pending';
  const topic = (params.topic as string) || "Session Details";
  const userName = (params.userName as string) || "User";

  // Status Badge Configuration
  const statusConfig = {
    accepted: { color: '#10b981', bg: '#f0fdf4', icon: 'checkmark-circle-outline', label: 'Accepted' },
    denied: { color: '#ef4444', bg: '#fef2f2', icon: 'close-circle-outline', label: 'Declined' },
    pending: { color: '#f59e0b', bg: '#fffbeb', icon: 'time-outline', label: 'Pending Review' },
  }[status as 'accepted' | 'denied' | 'pending'] || { color: '#64748b', bg: '#f8fafc', icon: 'help-circle-outline', label: status };

  const onShare = async () => {
    try {
      await Share.share({
        message: `Mentorship Session: ${topic}\nStatus: ${status}\nWith: ${userName}`,
      });
    } catch (error) { console.log(error); }
  };

  const showDatePicker = () => {
    DateTimePickerAndroid.open({
      value: selectedDate,
      onChange: (_, date) => date && setSelectedDate(date),
      mode: 'date',
    });
  };

  const showTimePicker = () => {
    DateTimePickerAndroid.open({
      value: selectedTime,
      onChange: (_, time) => time && setSelectedTime(time),
      mode: 'time',
    });
  };

 const handleAccept = async () => {
  if (!sessionId) {
    Alert.alert('Error', 'Invalid Session ID.');
    return;
  }
  if (!token) return;

  try {
    setIsSubmitting(true);
    
    const scheduledAt = new Date(
      selectedDate.getFullYear(),
      selectedDate.getMonth(),
      selectedDate.getDate(),
      selectedTime.getHours(),
      selectedTime.getMinutes()
    ).toISOString();

    // FIXED: Correct argument order (sessionId, token, payload)
    await updateSessionStatus(sessionId, token, {
      status: 'accepted', // Must match Laravel 'accepted'
      mentor_notes: note,
      scheduled_at: scheduledAt,
    });

    Alert.alert('Success', 'Session scheduled!', [
      { text: 'Done', onPress: () => router.back() }
    ]);
  } catch (error: any) {
    Alert.alert('Error', error.message || 'Could not update session.');
  } finally {
    setIsSubmitting(false);
  }
};

  // Error boundary if sessionId somehow isn't passed
  if (!sessionId) {
    return (
      <SafeAreaView className="flex-1 bg-white items-center justify-center p-6">
        <Feather name="alert-circle" size={50} color="#CBD5E1" />
        <Text className="text-xl font-bold text-slate-900 mt-4">Request Not Found</Text>
        <TouchableOpacity onPress={() => router.back()} className="mt-6 bg-indigo-600 px-8 py-3 rounded-2xl">
          <Text className="text-white font-bold">Go Back</Text>
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
          
          {/* HERO SECTION */}
          <LinearGradient colors={['#f8fafc', '#ffffff']} className="px-6 pt-8 pb-10 border-b border-slate-50">
            <View style={{ backgroundColor: statusConfig.bg }} className="self-start px-3 py-1 rounded-lg flex-row items-center mb-4 border border-slate-100">
              <Ionicons name={statusConfig.icon as any} size={14} color={statusConfig.color} />
              <Text style={{ color: statusConfig.color }} className="ml-1.5 font-bold text-[10px] uppercase tracking-widest">
                {statusConfig.label}
              </Text>
            </View>

            <Text className="text-2xl font-black text-slate-900 leading-tight mb-8">
              {topic}
            </Text>

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
          <View className="flex-row px-6 mt-6 space-x-4">
            <View className="flex-1 bg-slate-50 p-4 rounded-2xl border border-slate-100 mr-2">
              <Feather name="calendar" size={16} color="#6366f1" />
              <Text className="text-slate-400 text-[9px] font-bold uppercase mt-2">Requested Date</Text>
              <Text className="text-slate-900 font-bold text-[13px]">{params.date ? new Date(params.date as string).toLocaleDateString() : 'Pending'}</Text>
            </View>
            <View className="flex-1 bg-slate-50 p-4 rounded-2xl border border-slate-100">
              <Feather name="clock" size={16} color="#6366f1" />
              <Text className="text-slate-400 text-[9px] font-bold uppercase mt-2">Preferred Time</Text>
              <Text className="text-slate-900 font-bold text-[13px]">Morning Window</Text>
            </View>
          </View>

          {/* MESSAGE */}
          <View className="px-6 mt-8">
            <Text className="text-slate-900 font-bold text-sm mb-3">Inquiry Message</Text>
            <View className="bg-slate-50 p-5 rounded-3xl border border-slate-100">
              <Text className="text-slate-600 leading-6 italic">"{params.message || "I am looking for guidance on my career path and skill development."}"</Text>
            </View>
          </View>

          {/* ACTION FORM */}
          {isIncoming && status === 'pending' && (
            <View className="px-6 mt-8 mb-10">
              <Text className="text-slate-900 font-bold text-sm mb-4">Set Schedule & Notes</Text>
              <View className="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm shadow-slate-100">
                <TextInput 
                  placeholder="Add a meeting link or personal note..."
                  value={note}
                  onChangeText={setNote}
                  multiline
                  className="bg-slate-50 rounded-xl p-4 text-slate-700 h-24 mb-4"
                  textAlignVertical="top"
                />
                <View className="flex-row space-x-3">
                  <TouchableOpacity onPress={showDatePicker} className="flex-1 bg-white border border-slate-200 p-4 rounded-xl flex-row items-center justify-between mr-2">
                    <Text className="text-slate-700 font-semibold text-xs">{selectedDate.toLocaleDateString()}</Text>
                    <Feather name="calendar" size={14} color="#6366f1" />
                  </TouchableOpacity>
                  <TouchableOpacity onPress={showTimePicker} className="flex-1 bg-white border border-slate-200 p-4 rounded-xl flex-row items-center justify-between">
                    <Text className="text-slate-700 font-semibold text-xs">
                      {selectedTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                    </Text>
                    <Feather name="clock" size={14} color="#6366f1" />
                  </TouchableOpacity>
                </View>
              </View>
            </View>
          )}

          <View className="h-24" />
        </ScrollView>
      </KeyboardAvoidingView>

      {/* FOOTER */}
      {isIncoming && status === 'pending' && (
        <View className="px-6 pt-4 pb-10 border-t border-slate-100 flex-row bg-white">
          <TouchableOpacity 
            disabled={isSubmitting}
            className="flex-1 h-14 rounded-2xl items-center justify-center border border-slate-200 mr-4"
            onPress={() => router.back()}
          >
            <Text className="text-slate-500 font-bold">Decline</Text>
          </TouchableOpacity>
          <TouchableOpacity 
            disabled={isSubmitting}
            className="flex-[2] bg-indigo-600 h-14 rounded-2xl items-center justify-center shadow-lg shadow-indigo-200"
            onPress={handleAccept}
          >
            {isSubmitting ? (
              <ActivityIndicator color="white" />
            ) : (
              <Text className="text-white font-bold text-base">Accept Request</Text>
            )}
          </TouchableOpacity>
        </View>
      )}
    </SafeAreaView>
  );
};

export default MentorshipRequestInfo;