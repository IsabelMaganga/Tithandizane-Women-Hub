import React, { useEffect, useState } from 'react';
import { View, Text, ScrollView, Pressable, Share, Image } from 'react-native';
import { useLocalSearchParams, useRouter } from 'expo-router';
import { SafeAreaView } from 'react-native-safe-area-context';
import { StatusBar } from 'expo-status-bar';
import { Feather, Ionicons } from '@expo/vector-icons';
import LottieView from 'lottie-react-native';
import Toast from 'react-native-toast-message';
import { getGuidanceContentDetail, GuidanceContent } from '../../services/api';

const PURPLE = '#7c3aed';
const PURPLE_DARK = '#6d28d9';

const CATEGORY_LABELS: Record<string, string> = {
  menstrual_hygiene: 'Menstrual Hygiene',
  general: 'General Issues',
};

const ContentDetailScreen = () => {
  const { id } = useLocalSearchParams<{ id: string }>();
  const router = useRouter();
  const [content, setContent] = useState<GuidanceContent | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (id) {
      fetchContent();
    }
  }, [id]);

  const fetchContent = async () => {
    try {
      setLoading(true);
      const data = await getGuidanceContentDetail(Number(id));
      setContent(data);
    } catch {
      Toast.show({
        type: 'error',
        text1: 'Content not found',
        text2: 'This article may have been removed.',
        position: 'top',
      });
      setContent(null);
    } finally {
      setLoading(false);
    }
  };

  const onShare = async () => {
    if (!content) return;
    try {
      await Share.share({
        message: `${content.title}\n\n${content.body.substring(0, 200)}...`,
      });
    } catch {
      // user cancelled share
    }
  };

  if (loading) {
    return (
      <View className="flex-1 justify-center items-center bg-white">
        <LottieView
          source={require('../../assets/animations/loading.json')}
          autoPlay
          loop
          style={{ width: 150, height: 150 }}
        />
      </View>
    );
  }

  if (!content) {
    return (
      <SafeAreaView className="flex-1 justify-center items-center bg-white px-10">
        <Feather name="alert-circle" size={50} color="#94a3b8" />
        <Text className="text-slate-500 mt-4 text-center">Content not found</Text>
        <Pressable
          onPress={() => router.back()}
          className="mt-6 bg-[#7c3aed] px-6 py-3 rounded-full"
        >
          <Text className="text-white font-bold">Go Back</Text>
        </Pressable>
      </SafeAreaView>
    );
  }

  const hasPhoto = Boolean(content.photo_url);

  if (hasPhoto) {
    return (
      <View className="flex-1 bg-white">
        <StatusBar style="light" />
        <ScrollView className="flex-1" showsVerticalScrollIndicator={false}>
          <View className="relative">
            <Image
              source={{ uri: content.photo_url! }}
              className="w-full h-56"
              resizeMode="cover"
            />
            <View className="absolute inset-0 bg-black/20" />

            <SafeAreaView edges={['top']} className="absolute top-0 left-0 right-0">
              <View className="px-4 py-2 flex-row items-center justify-between">
                <Pressable
                  onPress={() => router.back()}
                  className="w-10 h-10 rounded-full bg-black/30 items-center justify-center"
                >
                  <Ionicons name="arrow-back" size={22} color="white" />
                </Pressable>
                <Pressable
                  onPress={onShare}
                  className="w-10 h-10 rounded-full bg-black/30 items-center justify-center"
                >
                  <Feather name="share-2" size={20} color="white" />
                </Pressable>
              </View>
            </SafeAreaView>
          </View>

          <View className="px-6 pt-6 pb-10 -mt-6 bg-white rounded-t-[28px]">
            <View className="bg-violet-50 self-start px-3 py-1 rounded-full mb-4">
              <Text className="text-[#6d28d9] text-[10px] font-black uppercase">
                {CATEGORY_LABELS[content.category] ?? content.category}
              </Text>
            </View>

            <Text className="text-slate-900 text-2xl font-black leading-9 mb-3">
              {content.title}
            </Text>

            {content.mentor_name ? (
              <View className="flex-row items-center mb-6">
                <View className="w-8 h-8 rounded-full bg-[#7c3aed] items-center justify-center mr-2">
                  <Text className="text-white font-bold text-sm">
                    {content.mentor_name.charAt(0)}
                  </Text>
                </View>
                <Text className="text-slate-500 text-sm font-medium">
                  Written by {content.mentor_name}
                </Text>
              </View>
            ) : null}

            <View className="h-px bg-violet-100 mb-6" />

            <Text className="text-slate-700 text-base leading-7">{content.body}</Text>
          </View>
        </ScrollView>
      </View>
    );
  }

  return (
    <SafeAreaView className="flex-1 bg-white" edges={['top']}>
      <StatusBar style="dark" />

      <View className="px-5 py-4 flex-row items-center justify-between border-b border-violet-50">
        <Pressable onPress={() => router.back()} className="p-2 -ml-2">
          <Ionicons name="arrow-back" size={24} color="#1E293B" />
        </Pressable>
        <Pressable onPress={onShare} className="p-2 -mr-2">
          <Feather name="share-2" size={22} color={PURPLE} />
        </Pressable>
      </View>

      <ScrollView className="flex-1 px-6 pt-4" showsVerticalScrollIndicator={false}>
        <View className="flex-row flex-wrap gap-2 mb-4">
          <View className="bg-violet-50 px-3 py-1 rounded-full">
            <Text className="text-[#6d28d9] text-[10px] font-black uppercase">
              {CATEGORY_LABELS[content.category] ?? content.category}
            </Text>
          </View>
        </View>

        <Text className="text-slate-900 text-2xl font-black leading-9 mb-3">
          {content.title}
        </Text>

        {content.mentor_name ? (
          <View className="flex-row items-center mb-6">
            <View className="w-8 h-8 rounded-full bg-[#7c3aed] items-center justify-center mr-2">
              <Text className="text-white font-bold text-sm">
                {content.mentor_name.charAt(0)}
              </Text>
            </View>
            <Text className="text-slate-500 text-sm font-medium">
              Written by {content.mentor_name}
            </Text>
          </View>
        ) : null}

        <View className="h-px bg-violet-100 mb-6" />

        <Text className="text-slate-700 text-base leading-7 mb-10">{content.body}</Text>
      </ScrollView>
    </SafeAreaView>
  );
};

export default ContentDetailScreen;
