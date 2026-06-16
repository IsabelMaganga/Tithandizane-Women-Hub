import React, { useState } from 'react';
import { View, Text, TextInput, TouchableOpacity, ScrollView, KeyboardAvoidingView, Platform, ActivityIndicator, Image } from 'react-native';
import { Ionicons, MaterialCommunityIcons } from '@expo/vector-icons';
import { inteligencyRequest } from '@/services/api';
import { router } from 'expo-router';

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
      // Laravel sends back { success: true, ... }
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
    <KeyboardAvoidingView 
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'} 
      className="flex-1 bg-slate-50"
    >
      <ScrollView className="flex-1 px-4 pt-6" showsVerticalScrollIndicator={false}>
        
        {/* Header Section */}
        <View className="mb-6 mt-4">
          <View className="flex-row items-center mb-1">
            <MaterialCommunityIcons name="auto-fix" size={20} color="#6366f1" style={{ marginRight: 6 }} />
            <Text className="text-xs font-semibold tracking-wider text-indigo-600 uppercase">
              AI Support Hub
            </Text>
          </View>
          <Text className="text-2xl font-bold text-slate-900 tracking-tight">
            How are you feeling today?
          </Text>
          <Text className="text-sm text-slate-500 mt-1">
            Share your thoughts or current challenges. Our system will analyze your input to provide tailored suggestions and expert mentors based on your issue.
          </Text>
        </View>

        {/* Input Area */}
        <View className="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 mb-6">
          <TextInput
            multiline
            numberOfLines={5}
            textAlignVertical="top"
            placeholder="Type your thoughts, feelings, or current challenges here..."
            placeholderTextColor="#94a3b8"
            value={query}
            onChangeText={setQuery}
            editable={!isLoading}
            className="text-slate-700 text-base min-h-[120px] mb-4"
          />

          {errorMessage ? (
            <Text className="text-sm text-red-500 mb-3 font-medium">{errorMessage}</Text>
          ) : null}
          
          <TouchableOpacity 
            onPress={handleAnalyze}
            disabled={isLoading || !query.trim()}
            className={`w-full py-3.5 rounded-xl flex-row justify-center items-center ${
              query.trim() && !isLoading ? 'bg-purple-600' : 'bg-slate-200'
            }`}
          >
            {isLoading ? (
              <ActivityIndicator size="small" color="#ffffff" style={{ marginRight: 8 }} />
            ) : null}
            <Text className={`font-semibold text-base ${query.trim() && !isLoading ? 'text-white' : 'text-slate-400'}`}>
              {isLoading ? 'Analyzing Insights...' : 'Analyze My Request'}
            </Text>
          </TouchableOpacity>
        </View>

        {/* Results Section */}
        {predictions != null && (
          <View className="mb-10">
            <Text className="text-xs font-bold tracking-wider text-slate-400 uppercase mb-4">
              Analysis & Recommendations
            </Text>

            {/* Path A: Predicted Topics */}
            <View className="mb-6">
              <View className="flex-row items-center mb-3">
                <Ionicons name="bookmark-outline" size={18} color="#475569" style={{ marginRight: 6 }} />
                <Text className="text-base font-semibold text-slate-800">Related Recommended Topics</Text>
              </View>
              
              {predictions.topics && predictions.topics.length > 0 ? (
                predictions.topics.map((topic) => (
                  <TouchableOpacity 
                    key={topic.id}
                    className="bg-white border border-slate-100 p-4 rounded-xl mb-2 flex-row justify-between items-center shadow-sm"
                  >
                    <View className="flex-1 pr-3">
                      <Text className="text-xs font-medium text-indigo-600 mb-1">{topic.category}</Text>
                      <Text className="text-sm font-semibold text-slate-700 leading-snug">{topic.title}</Text>
                    </View>
                    <Ionicons name="chevron-forward" size={16} color="#94a3b8" />
                  </TouchableOpacity>
                ))
              ) : (
                <View className="bg-white border border-slate-100 p-4 rounded-xl items-center">
                  <Text className="text-sm text-slate-400">Sorry, the system couldn't analyze. Please provide more detail.</Text>
                </View>
              )}
            </View>

            {/* Path B: Mentor Matching */}
            {predictions.mentors?.length > 0 && (
              <View className="mt-2">
                <View className="flex-row items-center mb-3">
                  <Ionicons name="people-outline" size={18} color="#475569" style={{ marginRight: 6 }} />
                  <Text className="text-base font-semibold text-slate-800">Talk to a Verified Mentor</Text>
                </View>
                
                <View className="bg-indigo-50 border border-indigo-100 rounded-xl p-4">
                  <Text className="text-xs font-medium text-indigo-700 uppercase tracking-wider mb-3">
                    Expert Mentors Matching Your Request
                  </Text>

                  {predictions.mentors.map((mentor) => (
                    <View key={mentor.id} className="bg-white p-3.5 rounded-lg mb-2.5 shadow-xs border border-indigo-100/50 flex-row justify-between items-center">
                      <View className="flex-1 pr-2">
                        <Text className="text-sm font-bold text-slate-800">{mentor.name}</Text>
                        <Text className="text-xs text-indigo-600 mt-0.5 font-medium">{mentor.expertise}</Text>
                        
                        <TouchableOpacity onPress={()=>router.push("/(protected)/mentorship-request")} className="mt-3 bg-purple-600 self-start px-4 py-1.5 rounded-md flex-row items-center">
                          <Ionicons name="chatbubble-ellipses-outline" size={14} color="#ffffff" style={{ marginRight: 6 }} />
                          <Text className="text-xs font-medium text-white">Book</Text>
                        </TouchableOpacity>
                      </View>

                      {/* Display Mentor Photo if available */}
                      {mentor.photo ? (
                        <Image 
                          source={{ uri: mentor.photo }} 
                          className="w-12 h-12 rounded-full bg-slate-100"
                        />
                      ) : (
                        <View className="w-12 h-12 rounded-full bg-slate-100 items-center justify-center">
                          <Ionicons name="person" size={20} color="#cbd5e1" />
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
  );
}