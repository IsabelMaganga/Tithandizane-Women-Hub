import { useLocalSearchParams, useRouter } from "expo-router";
import { useEffect, useState } from "react";
import { View, Text, Image, ScrollView, Pressable, Share } from "react-native";
import { getSingleArticle } from "../../../services/api";
import { Feather, Ionicons } from "@expo/vector-icons";
import LottieView from "lottie-react-native";

export default function ArticleDetails() {
  const { id } = useLocalSearchParams();
  const router = useRouter();
  const [article, setArticle] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (id) fetchArticle();
  }, [id]);

  const fetchArticle = async () => {
    try {
      setLoading(true);
      const data = await getSingleArticle(Number(id));
      setArticle(data);
    } catch (error) {
      console.log("Error fetching article:", error);
    } finally {
      setLoading(false);
    }
  };

  const onShare = async () => {
    try {
      await Share.share({
        message: `Check out this article: ${article.title}\n\n${article.content.substring(0, 100)}...`,
      });
    } catch (error) {
      console.log(error);
    }
  };

  if (loading) {
    return (
      <View className="flex-1 justify-center items-center bg-white">
        <LottieView
          source={require("../../../assets/animations/loading.json")}
          autoPlay
          loop
          style={{ width: 150, height: 150 }}
        />
      </View>
    );
  }

  if (!article) {
    return (
      <View className="flex-1 justify-center items-center p-10">
        <Feather name="alert-circle" size={50} color="gray" />
        <Text className="text-gray-500 mt-4 text-center">Article not found</Text>
        <Pressable onPress={() => router.back()} className="mt-6 bg-emerald-600 px-6 py-2 rounded-full">
          <Text className="text-white font-bold">Go Back</Text>
        </Pressable>
      </View>
    );
  }

  return (
    <View className="flex-1 bg-white">
      {/* Custom Header Navigation */}
      <View className="absolute top-12 left-6 z-10 flex-row justify-between right-6">
        <Pressable 
          onPress={() => router.back()} 
          className="bg-black/20 p-2 rounded-full backdrop-blur-md"
        >
          <Ionicons name="arrow-back" size={24} color="white" />
        </Pressable>
        <Pressable 
          onPress={onShare} 
          className="bg-black/20 p-2 rounded-full backdrop-blur-md"
        >
          <Feather name="share-2" size={22} color="white" />
        </Pressable>
      </View>

      <ScrollView showsVerticalScrollIndicator={false} bounces={false}>
        {/* Hero Image */}
        <View>
          {article.image_url ? (
            <Image
              source={{ uri: article.image_url }}
              className="w-full h-80"
              resizeMode="cover"
            />
          ) : (
            <View className="w-full h-60 bg-emerald-100 items-center justify-center">
              <Feather name="image" size={50} color="#10b981" />
            </View>
          )}
          {/* Subtle overlay to make white icons visible */}
          <View className="absolute top-0 w-full h-24 bg-black/10" />
        </View>

        {/* Content Container */}
        <View className="px-6 -mt-10 bg-white rounded-t-[40px] pt-8 pb-20">
          {/* Category Badge */}
          <View className="bg-emerald-100 self-start px-4 py-1.5 rounded-full mb-4">
            <Text className="text-emerald-700 font-bold text-xs uppercase tracking-widest">
              {article.category}
            </Text>
          </View>

          {/* Title */}
          <Text className="text-slate-900 text-3xl font-extrabold leading-tight mb-3">
            {article.title}
          </Text>

          {/* Metadata */}
          <View className="flex-row items-center mb-8 border-b border-slate-100 pb-6">
            <View className="bg-slate-100 p-2 rounded-full mr-3">
              <Feather name="calendar" size={14} color="#64748b" />
            </View>
            <Text className="text-slate-400 text-sm font-medium">
              {new Date(article.created_at).toLocaleDateString('en-US', {
                month: 'long',
                day: 'numeric',
                year: 'numeric'
              })}
            </Text>
            <View className="w-1 h-1 bg-slate-300 rounded-full mx-3" />
            <Text className="text-slate-400 text-sm font-medium">5 min read</Text>
          </View>

          {/* Article Body */}
          <Text className="text-slate-700 text-lg leading-7 text-justify mb-10">
            {article.content}
          </Text>

          {/* Safety Footer */}
          <View className="bg-slate-50 p-6 rounded-3xl border border-slate-100">
            <View className="flex-row items-center mb-2">
              <Feather name="info" size={18} color="#10b981" />
              <Text className="ml-2 font-bold text-slate-800">Health Note</Text>
            </View>
            <Text className="text-slate-500 text-xs leading-4">
              This information is for educational purposes. Always consult a healthcare professional for personalized medical advice.
            </Text>
          </View>
        </View>
      </ScrollView>
    </View>
  );
}