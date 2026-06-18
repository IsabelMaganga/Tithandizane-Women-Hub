import React, { useCallback, useEffect, useState } from 'react';
import {
  View,
  Text,
  Pressable,
  ScrollView,
  RefreshControl,
  Image,
} from 'react-native';
import { useRouter } from 'expo-router';
import { SafeAreaView } from 'react-native-safe-area-context';
import { StatusBar } from 'expo-status-bar';
import { Feather } from '@expo/vector-icons';
import LottieView from 'lottie-react-native';
import Toast from 'react-native-toast-message';
import {
  getPublishedGuidanceContent,
  GuidanceContent,
} from '../../services/api';

const PURPLE = '#7c3aed';
const PURPLE_DARK = '#6d28d9';

type CategoryTab = 'menstrual_hygiene' | 'general';

const CATEGORY_LABELS: Record<CategoryTab, string> = {
  menstrual_hygiene: 'Menstrual Hygiene',
  general: 'General Issues',
};

const formatDate = (dateStr: string) => {
  try {
    return new Date(dateStr).toLocaleDateString('en-US', {
      month: 'short',
      day: 'numeric',
      year: 'numeric',
    });
  } catch {
    return '';
  }
};

const GuidanceScreen = () => {
  const router = useRouter();
  const [activeTab, setActiveTab] = useState<CategoryTab>('menstrual_hygiene');
  const [contents, setContents] = useState<GuidanceContent[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const fetchContent = useCallback(async (category: CategoryTab, isRefresh = false) => {
    try {
      if (isRefresh) {
        setRefreshing(true);
      } else {
        setLoading(true);
      }
      const data = await getPublishedGuidanceContent(category);
      setContents(Array.isArray(data) ? data : []);
    } catch {
      Toast.show({
        type: 'error',
        text1: 'Failed to load content',
        text2: 'Please try again later.',
        position: 'top',
      });
      setContents([]);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, []);

  useEffect(() => {
    fetchContent(activeTab);
  }, [activeTab, fetchContent]);

  const handleOpenDetail = (id: number) => {
    router.push({
      pathname: '/(protected)/contentDetailScreen',
      params: { id: String(id) },
    });
  };

  if (loading && !refreshing) {
    return (
      <View className="flex-1 justify-center items-center bg-slate-50">
        <LottieView
          source={require('../../assets/animations/loading.json')}
          autoPlay
          loop
          style={{ width: 180, height: 180 }}
        />
        <Text className="text-slate-400 font-medium -mt-4">Loading guidance...</Text>
      </View>
    );
  }

  return (
    <SafeAreaView className="flex-1 bg-slate-50" edges={['top']}>
      <StatusBar style="light" />

      <View className="bg-[#7c3aed] pt-4 pb-8 px-6 rounded-b-[40px]">
        {router.canGoBack() && (
          <Pressable onPress={() => router.back()} className="flex-row items-center mb-3 -ml-1">
            <Feather name="arrow-left" size={20} color="white" />
            <Text className="text-white font-medium text-sm ml-2">Back</Text>
          </Pressable>
        )}
        <Text className="text-white text-3xl font-black tracking-tight">Guidance</Text>
        <Text className="text-violet-200 text-sm mt-1 font-medium">
          Educational content from our mentors
        </Text>
      </View>

      <View className="mx-5 -mt-5 flex-row bg-white p-1.5 rounded-2xl border border-violet-100 shadow-sm">
        {(Object.keys(CATEGORY_LABELS) as CategoryTab[]).map((tab) => (
          <Pressable
            key={tab}
            onPress={() => setActiveTab(tab)}
            className={`flex-1 py-3 rounded-xl items-center ${
              activeTab === tab ? 'bg-[#7c3aed]' : ''
            }`}
          >
            <Text
              className={`font-bold text-xs text-center px-1 ${
                activeTab === tab ? 'text-white' : 'text-slate-500'
              }`}
            >
              {CATEGORY_LABELS[tab]}
            </Text>
          </Pressable>
        ))}
      </View>

      <ScrollView
        className="flex-1 px-5 pt-5"
        contentContainerStyle={{ paddingBottom: 40 }}
        refreshControl={
          <RefreshControl
            refreshing={refreshing}
            onRefresh={() => fetchContent(activeTab, true)}
            colors={[PURPLE]}
            tintColor={PURPLE}
          />
        }
      >
        {contents.length === 0 ? (
          <View className="items-center mt-20 px-6">
            <View className="bg-violet-50 p-6 rounded-full">
              <Feather name="book-open" size={40} color={PURPLE} />
            </View>
            <Text className="text-slate-900 mt-6 text-center font-bold text-lg">
              No content yet
            </Text>
            <Text className="text-slate-400 text-center mt-2 leading-5">
              Check back soon for new guidance in this category.
            </Text>
          </View>
        ) : (
          contents.map((item) => (
            <ContentCard key={item.id} item={item} onPress={() => handleOpenDetail(item.id)} />
          ))
        )}
      </ScrollView>
    </SafeAreaView>
  );
};

const ContentCard = ({
  item,
  onPress,
}: {
  item: GuidanceContent;
  onPress: () => void;
}) => {
  const hasPhoto = Boolean(item.photo_url);

  if (hasPhoto) {
    return (
      <Pressable
        onPress={onPress}
        className="bg-white mb-4 rounded-3xl overflow-hidden border border-slate-100 shadow-sm active:opacity-95"
      >
        <Image
          source={{ uri: item.photo_url! }}
          className="w-full h-40"
          resizeMode="cover"
        />
        <View className="p-5">
          <View className="flex-row items-center justify-between mb-2">
            <View className="bg-violet-50 px-2.5 py-1 rounded-full">
              <Text className="text-[#6d28d9] text-[10px] font-black uppercase">
                {CATEGORY_LABELS[item.category as CategoryTab] ?? item.category}
              </Text>
            </View>
            {item.created_at ? (
              <Text className="text-slate-400 text-xs">{formatDate(item.created_at)}</Text>
            ) : null}
          </View>

          <Text className="text-slate-900 font-bold text-lg mb-2 leading-7" numberOfLines={2}>
            {item.title}
          </Text>

          <Text className="text-slate-500 text-sm leading-6" numberOfLines={2}>
            {item.body}
          </Text>

          <View className="flex-row items-center justify-between mt-4 pt-3 border-t border-slate-50">
            {item.mentor_name ? (
              <Text className="text-slate-400 text-xs">by {item.mentor_name}</Text>
            ) : (
              <View />
            )}
            <View className="flex-row items-center">
              <Text className="text-[#7c3aed] font-bold text-sm mr-1">Read</Text>
              <Feather name="arrow-right" size={14} color={PURPLE} />
            </View>
          </View>
        </View>
      </Pressable>
    );
  }

  return (
    <Pressable
      onPress={onPress}
      className="bg-white mb-4 rounded-3xl p-5 border border-slate-100 shadow-sm active:opacity-95"
    >
      <View className="w-1 h-full absolute left-0 top-0 bottom-0 bg-[#7c3aed] rounded-l-3xl" style={{ width: 4 }} />

      <View className="flex-row items-center justify-between mb-3 pl-1">
        <View className="bg-violet-50 px-2.5 py-1 rounded-full">
          <Text className="text-[#6d28d9] text-[10px] font-black uppercase">
            {CATEGORY_LABELS[item.category as CategoryTab] ?? item.category}
          </Text>
        </View>
        {item.created_at ? (
          <Text className="text-slate-400 text-xs">{formatDate(item.created_at)}</Text>
        ) : null}
      </View>

      <Text className="text-slate-900 font-bold text-lg mb-2 leading-7 pl-1" numberOfLines={2}>
        {item.title}
      </Text>

      <Text className="text-slate-500 text-sm leading-6 pl-1" numberOfLines={3}>
        {item.body}
      </Text>

      <View className="flex-row items-center justify-between mt-4 pt-3 border-t border-slate-50 pl-1">
        {item.mentor_name ? (
          <Text className="text-slate-400 text-xs font-medium">by {item.mentor_name}</Text>
        ) : (
          <View />
        )}
        <View className="flex-row items-center">
          <Text className="text-[#7c3aed] font-bold text-sm mr-1">Read more</Text>
          <Feather name="arrow-right" size={14} color={PURPLE} />
        </View>
      </View>
    </Pressable>
  );
};

export default GuidanceScreen;
