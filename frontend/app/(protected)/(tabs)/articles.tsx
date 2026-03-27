import React, { useEffect, useState, useMemo, useCallback } from 'react';
import { 
  View, 
  Text, 
  Pressable, 
  ScrollView, 
  Image, 
  TextInput, 
  KeyboardAvoidingView, 
  Platform 
} from 'react-native';
import { useRouter } from 'expo-router';
import { getHygieneArticles, getGeneralGuides } from '../../../services/api';
import { LegendList } from '@legendapp/list';
import LottieView from 'lottie-react-native';
import { Feather } from '@expo/vector-icons';
import { StatusBar } from 'expo-status-bar';

// --- Types ---
type Article = {
  id: number;
  title: string;
  content: string;
  category: string;
  image_url: string;
};

type SourceType = 'hygiene' | 'general';

const ArticlesScreen = () => {
  const router = useRouter();

  // --- State ---
  const [articles, setArticles] = useState<Article[]>([]);
  const [loading, setLoading] = useState(true);
  const [categories, setCategories] = useState<string[]>([]);
  const [selectedCategory, setSelectedCategory] = useState<string | null>(null);
  const [activeSource, setActiveSource] = useState<SourceType>('hygiene');
  const [searchQuery, setSearchQuery] = useState('');

  // --- Data Fetching ---
  const fetchArticles = useCallback(async (source: SourceType) => {
    try {
      setLoading(true);
      setSelectedCategory(null);
      setSearchQuery('');
      
      const data = source === 'hygiene' 
        ? await getHygieneArticles() 
        : await getGeneralGuides();
      
      const safeData = Array.isArray(data) ? data : [];
      setArticles(safeData);

      const uniqueCategories = [
        ...new Set(safeData.map((article: Article) => article.category).filter(Boolean)),
      ];
      setCategories(uniqueCategories as string[]);
    } catch (error) {
      console.error(`Failed to fetch ${source} articles:`, error);
      setArticles([]);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    fetchArticles(activeSource);
  }, [activeSource, fetchArticles]);

  const filteredArticles = useMemo(() => {
    return articles.filter((article) => {
      const matchesCategory = !selectedCategory || 
        article?.category?.toLowerCase() === selectedCategory.toLowerCase();
      
      const matchesSearch = !searchQuery || 
        article.title.toLowerCase().includes(searchQuery.toLowerCase()) ||
        article.content.toLowerCase().includes(searchQuery.toLowerCase());

      return matchesCategory && matchesSearch;
    });
  }, [articles, selectedCategory, searchQuery]);

  const handlePressArticle = (id: number) => {
    router.push(`/articles/${id}`);
  };

  if (loading && articles.length === 0) {
    return (
      <View className="flex-1 justify-center items-center bg-white">
        <LottieView
          source={require("../../../assets/animations/loading.json")}
          autoPlay
          loop
          style={{ width: 180, height: 180 }}
        />
        <Text className="text-slate-400 font-medium -mt-4">Loading health tips...</Text>
      </View>
    );
  }

  //Header Component for the List
  const ListHeader = () => (
    <View className="mt-[-30px]">
      {/* Search Bar */}
      <View className="flex-row items-center bg-white rounded-2xl px-4 mb-5 mt-10 border border-slate-200 shadow-sm mx-5">
        <Feather name="search" size={18} color="#059669" />
        <TextInput
          placeholder="Search articles..."
          placeholderTextColor="#94a3b8"
          className="flex-1 py-4 px-3 text-slate-900 font-medium"
          value={searchQuery}
          onChangeText={setSearchQuery}
        />
        {searchQuery.length > 0 && (
          <Pressable onPress={() => setSearchQuery('')}>
            <Feather name="x-circle" size={18} color="#94a3b8" />
          </Pressable>
        )}
      </View>

      {/* Source Toggle */}
      <View className="mx-5 mb-6 flex-row bg-slate-200/50 p-1.5 rounded-2xl border border-slate-100">
        <Pressable 
          onPress={() => setActiveSource('hygiene')}
          className={`flex-1 py-3 rounded-xl items-center ${activeSource === 'hygiene' ? 'bg-white shadow-sm' : ''}`}
        >
          <Text className={`font-bold text-xs ${activeSource === 'hygiene' ? 'text-emerald-700' : 'text-slate-500'}`}>
            Hygiene Tips
          </Text>
        </Pressable>
        <Pressable 
          onPress={() => setActiveSource('general')}
          className={`flex-1 py-3 rounded-xl items-center ${activeSource === 'general' ? 'bg-white shadow-sm' : ''}`}
        >
          <Text className={`font-bold text-xs ${activeSource === 'general' ? 'text-emerald-700' : 'text-slate-500'}`}>
            General Guides
          </Text>
        </Pressable>
      </View>

      {/* Category Filter */}
      <View className="mb-4">
        <ScrollView 
          horizontal 
          showsHorizontalScrollIndicator={false}
          contentContainerStyle={{ paddingHorizontal: 20 }}
        >
          <CategoryPill 
            label="All Topics" 
            isActive={selectedCategory === null} 
            onPress={() => setSelectedCategory(null)} 
          />
          {categories.map((cat, index) => (
            <CategoryPill 
              key={`${cat}-${index}`}
              label={cat} 
              isActive={selectedCategory === cat} 
              onPress={() => setSelectedCategory(cat)} 
            />
          ))}
        </ScrollView>
      </View>
    </View>
  );

  return (
    <KeyboardAvoidingView 
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'} 
      className="flex-1 bg-slate-50"
    >
      <StatusBar style="light" />
      
      {/* Header*/}
      <View className="bg-emerald-600 pt-16 pb-6 px-6 rounded-b-[48px] z-10">
        <Text className="text-white text-3xl font-black tracking-tight">Health & Education</Text>
        <Text className="text-emerald-100 text-sm mt-1 font-medium">Expert advice for your wellbeing</Text>
      </View>

      {/* ARTICLES LIST */}
      <LegendList
        data={filteredArticles}
        estimatedItemSize={350}
        keyExtractor={(item) => item.id.toString()}
        contentContainerStyle={{ paddingBottom: 40 }}
        ListHeaderComponent={ListHeader}
        ListEmptyComponent={
          <View className="items-center mt-30 px-10">
            <View className="bg-slate-100 p-6 rounded-full">
               <Feather name="search" size={40} color="#94a3b8" />
            </View>
            <Text className="text-slate-900 mt-6 text-center font-bold text-lg">
              {searchQuery ? `No results for "${searchQuery}"` : "No articles found"}
            </Text>
            <Text className="text-slate-400 text-center mt-2 leading-5">
              Try adjusting your keywords or exploring a different category.
            </Text>
          </View>
        }
        renderItem={({ item }) => (
          <View className="px-5">
            <ArticleCard item={item} onPress={() => handlePressArticle(item.id)} />
          </View>
        )}
      />
    </KeyboardAvoidingView>
  );
};

// --- Sub-components ---

const CategoryPill = ({ label, isActive, onPress }: { label: string, isActive: boolean, onPress: () => void }) => (
  <Pressable
    onPress={onPress}
    className={`px-6 py-3 mr-2 rounded-[18px] border ${
      isActive 
        ? 'bg-emerald-600 border-emerald-600 shadow-md shadow-emerald-100' 
        : 'bg-white border-slate-200 shadow-sm'
    }`}
  >
    <Text className={`font-bold text-[13px] ${isActive ? 'text-white' : 'text-slate-500'}`}>
      {label}
    </Text>
  </Pressable>
);

const ArticleCard = ({ item, onPress }: { item: Article, onPress: () => void }) => (
  <Pressable
    onPress={onPress}
    className="bg-white mb-5 rounded-[32px] overflow-hidden shadow-sm border border-slate-100 active:opacity-95"
  >
    {item.image_url ? (
      <Image
        source={{ uri: item.image_url }}
        className="w-full h-52"
        resizeMode="cover"
      />
    ) : (
      <View className="w-full h-52 bg-emerald-50 items-center justify-center">
        <Feather name="image" size={32} color="#10b981" />
      </View>
    )}

    <View className="p-6">
      <View className="flex-row items-center mb-3">
        <View className="bg-emerald-50 px-3 py-1 rounded-full">
          <Text className="text-emerald-700 text-[10px] font-black uppercase tracking-widest">
            {item.category || "General"}
          </Text>
        </View>
      </View>

      <Text className="text-slate-900 font-bold text-xl mb-2 leading-7" numberOfLines={2}>
        {item.title}
      </Text>

      <Text className="text-slate-500 text-[14px] leading-6 mb-5" numberOfLines={3}>
        {item.content}
      </Text>

      <View className="flex-row items-center justify-between pt-4 border-t border-slate-50">
        <View className="flex-row items-center">
          <Feather name="book-open" size={14} color="#94a3b8" />
          <Text className="text-slate-400 text-xs ml-1.5 font-medium">5 min read</Text>
        </View>
        <View className="flex-row items-center">
          <Text className="text-emerald-600 font-bold text-sm mr-1">Continue</Text>
          <Feather name="arrow-right" size={14} color="#059669" />
        </View>
      </View>
    </View>
  </Pressable>
);

export default ArticlesScreen;