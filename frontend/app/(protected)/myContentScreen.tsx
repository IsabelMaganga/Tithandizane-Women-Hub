import React, { useCallback, useEffect, useState } from 'react';
import {
  View,
  Text,
  Pressable,
  ScrollView,
  Alert,
  RefreshControl,
  ActivityIndicator,
  Image,
} from 'react-native';
import { useRouter } from 'expo-router';
import { SafeAreaView } from 'react-native-safe-area-context';
import { StatusBar } from 'expo-status-bar';
import { Feather } from '@expo/vector-icons';
import LottieView from 'lottie-react-native';
import Toast from 'react-native-toast-message';
import {
  deleteGuidanceContent,
  getMentorGuidanceContent,
  GuidanceContent,
  toggleGuidanceContentStatus,
} from '../../services/api';

const CATEGORY_LABELS: Record<string, string> = {
  menstrual_hygiene: 'Menstrual Hygiene',
  general: 'General Issues',
};

const MyContentScreen = () => {
  const router = useRouter();
  const [contents, setContents] = useState<GuidanceContent[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [actionId, setActionId] = useState<number | null>(null);

  const fetchContents = useCallback(async (isRefresh = false) => {
    try {
      if (isRefresh) {
        setRefreshing(true);
      } else {
        setLoading(true);
      }
      const data = await getMentorGuidanceContent();
      setContents(Array.isArray(data) ? data : []);
    } catch {
      Toast.show({
        type: 'error',
        text1: 'Failed to load your content',
        position: 'top',
      });
      setContents([]);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, []);

  useEffect(() => {
    fetchContents();
  }, [fetchContents]);

  const handleToggleStatus = async (item: GuidanceContent) => {
    setActionId(item.id);
    try {
      const updated = await toggleGuidanceContentStatus(item.id);
      setContents((prev) => prev.map((c) => (c.id === item.id ? updated : c)));
      Toast.show({
        type: 'success',
        text1: updated.status === 'published' ? 'Content published' : 'Content unpublished',
        position: 'top',
      });
    } catch {
      Toast.show({
        type: 'error',
        text1: 'Action failed',
        text2: 'Please try again.',
        position: 'top',
      });
    } finally {
      setActionId(null);
    }
  };

  const handleDelete = (item: GuidanceContent) => {
    Alert.alert(
      'Delete Content',
      `Are you sure you want to delete "${item.title}"? This cannot be undone.`,
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Delete',
          style: 'destructive',
          onPress: async () => {
            setActionId(item.id);
            try {
              await deleteGuidanceContent(item.id);
              setContents((prev) => prev.filter((c) => c.id !== item.id));
              Toast.show({
                type: 'success',
                text1: 'Content deleted',
                position: 'top',
              });
            } catch {
              Toast.show({
                type: 'error',
                text1: 'Delete failed',
                position: 'top',
              });
            } finally {
              setActionId(null);
            }
          },
        },
      ]
    );
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
      </View>
    );
  }

  return (
    <SafeAreaView className="flex-1 bg-slate-50" edges={['top']}>
      <StatusBar style="light" />

      <View className="bg-[#7c3aed] px-5 pt-2 pb-6 rounded-b-[32px]">
        <View className="flex-row items-center justify-between">
          <Pressable onPress={() => router.back()} className="p-2 -ml-2">
            <Feather name="arrow-left" size={22} color="white" />
          </Pressable>
          <Pressable
            onPress={() => router.push('/(protected)/publishContentScreen')}
            className="bg-white/20 px-4 py-2 rounded-full flex-row items-center"
          >
            <Feather name="plus" size={16} color="white" />
            <Text className="text-white font-bold text-sm ml-1">New</Text>
          </Pressable>
        </View>
        <Text className="text-white text-2xl font-black mt-2">My Content</Text>
        <Text className="text-purple-200 text-sm mt-1">Manage your published guidance</Text>
      </View>

      <ScrollView
        className="flex-1 px-5 pt-5"
        contentContainerStyle={{ paddingBottom: 40 }}
        refreshControl={
          <RefreshControl
            refreshing={refreshing}
            onRefresh={() => fetchContents(true)}
            colors={['#7c3aed']}
            tintColor="#7c3aed"
          />
        }
      >
        {contents.length === 0 ? (
          <View className="items-center mt-16 px-6">
            <Feather name="file-text" size={48} color="#94a3b8" />
            <Text className="text-slate-900 font-bold text-lg mt-4">No content yet</Text>
            <Text className="text-slate-400 text-center mt-2">
              Publish your first guidance article to help users.
            </Text>
            <Pressable
              onPress={() => router.push('/(protected)/publishContentScreen')}
              className="mt-6 bg-[#6d28d9] px-6 py-3 rounded-2xl"
            >
              <Text className="text-white font-bold">Publish Content</Text>
            </Pressable>
          </View>
        ) : (
          contents.map((item) => (
            <View
              key={item.id}
              className="bg-white mb-4 rounded-3xl overflow-hidden border border-slate-100 shadow-sm"
            >
              {item.photo_url ? (
                <Image
                  source={{ uri: item.photo_url }}
                  className="w-full h-28"
                  resizeMode="cover"
                />
              ) : null}
              <View className="p-5">
              <View className="flex-row items-center justify-between mb-2">
                <View
                  className={`px-3 py-1 rounded-full ${
                    item.status === 'published' ? 'bg-green-50' : 'bg-amber-50'
                  }`}
                >
                  <Text
                    className={`text-[10px] font-black uppercase ${
                      item.status === 'published' ? 'text-green-700' : 'text-amber-700'
                    }`}
                  >
                    {item.status}
                  </Text>
                </View>
                <Text className="text-slate-400 text-xs">
                  {CATEGORY_LABELS[item.category] ?? item.category}
                </Text>
              </View>

              <Text className="text-slate-900 font-bold text-lg mb-1" numberOfLines={2}>
                {item.title}
              </Text>
              <Text className="text-slate-500 text-sm mb-4" numberOfLines={2}>
                {item.body}
              </Text>

              <View className="flex-row gap-2 pt-3 border-t border-slate-50">
                <ActionButton
                  label="Edit"
                  icon="edit-2"
                  onPress={() =>
                    router.push(`/(protected)/publishContentScreen?contentId=${item.id}`)
                  }
                  disabled={actionId === item.id}
                />
                <ActionButton
                  label={item.status === 'published' ? 'Unpublish' : 'Republish'}
                  icon={item.status === 'published' ? 'eye-off' : 'eye'}
                  onPress={() => handleToggleStatus(item)}
                  disabled={actionId === item.id}
                  loading={actionId === item.id}
                />
                <ActionButton
                  label="Delete"
                  icon="trash-2"
                  onPress={() => handleDelete(item)}
                  disabled={actionId === item.id}
                  danger
                />
              </View>
              </View>
            </View>
          ))
        )}
      </ScrollView>
    </SafeAreaView>
  );
};

const ActionButton = ({
  label,
  icon,
  onPress,
  disabled,
  loading,
  danger,
}: {
  label: string;
  icon: keyof typeof Feather.glyphMap;
  onPress: () => void;
  disabled?: boolean;
  loading?: boolean;
  danger?: boolean;
}) => (
  <Pressable
    onPress={onPress}
    disabled={disabled}
    className={`flex-1 flex-row items-center justify-center py-2.5 rounded-xl border ${
      danger ? 'border-red-100 bg-red-50' : 'border-slate-100 bg-slate-50'
    } ${disabled ? 'opacity-50' : ''}`}
  >
    {loading ? (
      <ActivityIndicator size="small" color="#7c3aed" />
    ) : (
      <>
        <Feather name={icon} size={14} color={danger ? '#dc2626' : '#7c3aed'} />
        <Text
          className={`text-xs font-bold ml-1 ${
            danger ? 'text-red-600' : 'text-[#7c3aed]'
          }`}
        >
          {label}
        </Text>
      </>
    )}
  </Pressable>
);

export default MyContentScreen;
