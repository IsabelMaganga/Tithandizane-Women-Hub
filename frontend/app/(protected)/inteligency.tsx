import React, { useState } from 'react';
import { View, Text, TextInput, TouchableOpacity, ScrollView, KeyboardAvoidingView, Platform, ActivityIndicator, Image, Pressable } from 'react-native';
import { Ionicons, MaterialCommunityIcons } from '@expo/vector-icons';
import { inteligencyRequest } from '@/services/api';
import { router } from 'expo-router';
import { SafeAreaView } from 'react-native-safe-area-context';

interface Topic {
  id: string;
  title: string;
  category: string;
}

interface Mentor {
  id: string;
  name: string;
  expertise: string;
  photo: string | null;
}

interface PredictionData {
  success: boolean;
  incident_id: number;
  topics: Topic[];
  mentors: Mentor[];
}

export default function IntelligenceScreen() {
  const [query, setQuery] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [errorMessage, setErrorMessage] = useState('');
  const [predictions, setPredictions] = useState<PredictionData | null>(null);

  const handleAnalyze = async () => {
    if (!query.trim()) return;
    
    setIsLoading(true);
    setErrorMessage('');
    
    try {
      const response = await inteligencyRequest(query);
      if (response && response.success) {
        setPredictions(response);
      } else {
        setErrorMessage(response?.message || 'Could not load suggestions. Please try again.');
      }
    } catch (error) {
      setErrorMessage('An unexpected error occurred connecting to the server.');
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <SafeAreaView className="flex-1 bg-slate-50">
      <KeyboardAvoidingView 
        behavior={Platform.OS === 'ios' ? 'padding' : 'height'} 
        className="flex-1"
      >
        {/* Navigation Header Utility Row */}
        <View className="flex-row items-center justify-between px-4 pt-4 pb-2">
          <Pressable 
            onPress={() => router.back()} 
            className="w-10 h-10 bg-white rounded-xl items-center justify-center shadow-xs border border-slate-200 active:bg-slate-100"
          >
            <Ionicons name="chevron-back" size={20} color="#334155" />
          </Pressable>
          <Text className="text-slate-800 font-bold text-base">AI Intelligence Hub</Text>
          <View className="w-10" /> 
        </View>

        <ScrollView className="flex-1 px-4" showsVerticalScrollIndicator={false}>
          {/* Header Section */}
          <View className="mb-6 mt-2">
            <View className="flex-row items-center mb-1">
              <MaterialCommunityIcons name="auto-fix" size={18} color="#4f46e5" style={{ marginRight: 6 }} />
              <Text className="text-xs font-bold tracking-wider text-indigo-600 uppercase">
                Natural Language Processing System
              </Text>
            </View>
            <Text className="text-2xl font-black text-slate-900 tracking-tight">
              How are you feeling today?
            </Text>
            <Text className="text-sm font-medium text-slate-500 mt-1.5 leading-5">
              Share your thoughts, experiences, or current challenges. Our pipeline will safely evaluate keywords to instantly offer specialized community literature and link you directly to professional staff.
            </Text>
          </View>

          {/* Input Card Container */}
          <View className="bg-white rounded-3xl p-5 shadow-xs border border-slate-100 mb-6">
            <TextInput
              multiline
            
              textAlignVertical="top"
              placeholder="Type your thoughts, feelings, or current campus challenges here..."
              placeholderTextColor="#94a3b8"
              value={query}
              onChangeText={setQuery}
              editable={!isLoading}
              className="text-slate-800 text-base min-h-[130px] mb-4 p-0 font-medium leading-6"
            />

            
            {!!errorMessage && (
              <Text className="text-sm text-rose-500 mb-4 font-semibold">⚠️ {errorMessage}</Text>
            )}
            
            <TouchableOpacity 
              onPress={handleAnalyze}
              disabled={isLoading || !query.trim()}
              activeOpacity={0.8}
              className={`w-full py-4 rounded-2xl flex-row justify-center items-center ${
                query.trim() && !isLoading ? 'bg-purple-600' : 'bg-slate-100'
              }`}
            >
              {isLoading && (
                <ActivityIndicator size="small" color="#ffffff" style={{ marginRight: 8 }} />
              )}
              <Text className={`font-bold text-base ${query.trim() && !isLoading ? 'text-white' : 'text-slate-400'}`}>
                {isLoading ? 'Analyzing Telemetry Data...' : 'Analyze My Request'}
              </Text>
            </TouchableOpacity>
          </View>

          {/* Results Analysis Panel */}
          {predictions !== null && (
            <View className="mb-12">
              <Text className="text-xs font-bold tracking-widest text-slate-400 uppercase mb-4">
                Analysis & Recommended Channels
              </Text>

              {/* Recommended Topics */}
              <View className="mb-6">
                <View className="flex-row items-center mb-3">
                  <Ionicons name="bookmark" size={18} color="#6366f1" style={{ marginRight: 6 }} />
                  <Text className="text-base font-bold text-slate-800">Matching Support Topics</Text>
                </View>
                
                {Array.isArray(predictions.topics) && predictions.topics.length > 0 ? (
                  predictions.topics.map((topic) => (
                    <TouchableOpacity 
                      key={topic.id}
                      activeOpacity={0.7}
                      className="bg-white border border-slate-100 p-4 rounded-2xl mb-2 flex-row justify-between items-center shadow-xs"
                    >
                      <View className="flex-1 pr-3">
                        <Text className="text-[10px] font-bold text-indigo-600 uppercase tracking-wider mb-0.5">{topic.category}</Text>
                        <Text className="text-sm font-bold text-slate-700 leading-snug">{topic.title}</Text>
                      </View>
                      <Ionicons name="chevron-forward" size={16} color="#94a3b8" />
                    </TouchableOpacity>
                  ))
                ) : (
                  <View className="bg-white border border-dashed border-slate-200 p-5 rounded-2xl items-center">
                    <Text className="text-sm font-medium text-slate-400 text-center">No structural categorization flags triggered. Provide more descriptive text context above.</Text>
                  </View>
                )}
              </View>

              {/* Intelligent Advisor Matchmaking */}
              {Array.isArray(predictions.mentors) && predictions.mentors.length > 0 && (
                <View className="mt-2">
                  <View className="flex-row items-center mb-3">
                    <Ionicons name="people" size={18} color="#8b5cf6" style={{ marginRight: 6 }} />
                    <Text className="text-base font-bold text-slate-800">Verified On-Call Mentors</Text>
                  </View>
                  
                  <View className="bg-indigo-50 border border-indigo-100 rounded-3xl p-4">
                    <Text className="text-[10px] font-black text-indigo-700 uppercase tracking-widest mb-3 px-1">
                      Matched Specialized Support
                    </Text>

                    {predictions.mentors.map((mentor) => (
                      <View key={mentor.id} className="bg-white p-4 rounded-2xl mb-2.5 shadow-xs border border-slate-100 flex-row justify-between items-center">
                        <View className="flex-1 pr-3">
                          <Text className="text-sm font-bold text-slate-800">{mentor.name}</Text>
                          <Text className="text-xs text-indigo-600 font-semibold mt-0.5">{mentor.expertise}</Text>
                          
                          <TouchableOpacity 
                            onPress={() => router.push({
                              pathname: "/mentorship-request",
                              params: { mentorId: mentor.id, mentorName: mentor.name }
                            })} 
                            activeOpacity={0.8}
                            className="mt-3 bg-purple-600 self-start px-4 py-2 rounded-xl flex-row items-center"
                          >
                            <Ionicons name="calendar-outline" size={13} color="#ffffff" style={{ marginRight: 6 }} />
                            <Text className="text-xs font-bold text-white">Book Free Session</Text>
                          </TouchableOpacity>
                        </View>

                        {mentor.photo ? (
                          <Image 
                            source={{ uri: mentor.photo }} 
                            className="w-14 h-14 rounded-2xl bg-slate-100"
                          />
                        ) : (
                          <View className="w-14 h-14 rounded-2xl bg-slate-100 items-center justify-center border border-slate-200">
                            <Ionicons name="person" size={22} color="#cbd5e1" />
                          </View>
                        )}
                      </View>
                    ))}
                  </View>
                </View>
              )}
            </View>
          )}
        </ScrollView>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}